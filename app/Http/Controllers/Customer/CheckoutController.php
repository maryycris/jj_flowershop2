<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CartItem;
use App\Models\User;
use App\Models\Order;
use App\Models\Delivery;
use App\Models\Address;
use App\Services\PayMongoService;

class CheckoutController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $cartItems = $user->cartItems()->with('product')->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('customer.cart.index')->with('error', 'Your cart is empty.');
        }

        $subtotal = 0;
        foreach ($cartItems as $item) {
            $subtotal += $item->quantity * $item->product->price;
        }

        // Get user's default address or first address
        $deliveryAddress = $user->addresses()->where('is_default', true)->first() 
                          ?? $user->addresses()->first();

        // Shipping fee logic
        $shippingFees = [
            'Cordova' => 30,
            'Mandaue' => 60,
            'Lapu-Lapu' => 70,
            'Cebu City' => 80,
        ];
        $shippingFee = 100; // Default
        if ($deliveryAddress) {
            // Try to get city/municipality from address fields
            $city = $deliveryAddress->city ?? $deliveryAddress->municipality ?? null;
            if ($city && isset($shippingFees[$city])) {
                $shippingFee = $shippingFees[$city];
            } elseif ($city) {
                // Try partial match
                foreach ($shippingFees as $key => $fee) {
                    if (stripos($city, $key) !== false) {
                        $shippingFee = $fee;
                        break;
                    }
                }
            }
        }

        return view('customer.checkout.index', compact('cartItems', 'subtotal', 'deliveryAddress', 'shippingFee'));
    }

    public function paymentMethod()
    {
        $user = Auth::user();
        $cartItems = $user->cartItems()->with('product')->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('customer.cart.index')->with('error', 'Your cart is empty.');
        }

        $subtotal = 0;
        foreach ($cartItems as $item) {
            $subtotal += $item->quantity * $item->product->price;
        }

        // Get user's default address or first address
        $deliveryAddress = $user->addresses()->where('is_default', true)->first() 
                          ?? $user->addresses()->first();

        // Shipping fee logic
        $shippingFees = [
            'Cordova' => 30,
            'Mandaue' => 60,
            'Lapu-Lapu' => 70,
            'Cebu City' => 80,
        ];
        $shippingFee = 100; // Default
        if ($deliveryAddress) {
            // Try to get city/municipality from address fields
            $city = $deliveryAddress->city ?? $deliveryAddress->municipality ?? null;
            if ($city && isset($shippingFees[$city])) {
                $shippingFee = $shippingFees[$city];
            } elseif ($city) {
                // Try partial match
                foreach ($shippingFees as $key => $fee) {
                    if (stripos($city, $key) !== false) {
                        $shippingFee = $fee;
                        break;
                    }
                }
            }
        }

        return view('customer.checkout.payment_method', compact('cartItems', 'subtotal', 'shippingFee'));
    }

    public function processOrder(Request $request)
    {
        // Add debugging
        \Log::info('Checkout process started', ['request_data' => $request->all()]);
        
        $user = Auth::user();
        $cartItems = $user->cartItems()->with('product')->get();

        if ($cartItems->isEmpty()) {
            \Log::warning('Cart is empty for user', ['user_id' => $user->id]);
            return redirect()->route('customer.cart.index')->with('error', 'Your cart is empty.');
        }

        \Log::info('Cart items found', ['count' => $cartItems->count()]);

        $request->validate([
            'payment_method' => 'required|in:cod,gcash,paymaya',
        ]);

        \Log::info('Validation passed');

        // Calculate total
        $subtotal = $cartItems->sum(function($item) { 
            return $item->quantity * $item->product->price; 
        });
        // Use shipping_fee from form if present
        $shippingFee = $request->input('shipping_fee');
        if (!$shippingFee || !is_numeric($shippingFee) || $shippingFee < 30) {
            $shippingFee = 30; // fallback minimum
        }
        $totalPrice = $subtotal + $shippingFee;

        \Log::info('Calculated totals', ['subtotal' => $subtotal, 'total' => $totalPrice]);

        $paymentMethod = $request->input('payment_method');

        // Get delivery details from session or use defaults
        $deliveryDate = $request->input('delivery_date') ?? now()->addDays(2)->format('Y-m-d');
        $deliveryTime = $request->input('delivery_time') ?? '09:00 AM';
        $deliveryAddress = $request->input('delivery_address') ?? $user->addresses()->where('is_default', true)->first()?->street_address ?? 'Bang-bang Cordova, Cebu';
        $recipientName = $request->input('recipient_name') ?? $user->first_name . ' ' . $user->last_name;
        $recipientPhone = $request->input('recipient_phone') ?? $user->contact_number;

        // Handle different payment methods
        if ($paymentMethod === 'cod') {
            // For COD, create order immediately
            return $this->createOrder($request, $user, $cartItems, $totalPrice, 'cod', $deliveryDate, $deliveryTime, $deliveryAddress, $recipientName, $recipientPhone);
        } else {
            // For GCash and PayMaya, redirect to payment gateway
            return $this->redirectToPaymentGateway($request, $user, $cartItems, $totalPrice, $paymentMethod, $deliveryDate, $deliveryTime, $deliveryAddress, $recipientName, $recipientPhone);
        }
    }

    private function createOrder(Request $request, $user, $cartItems, $totalPrice, $paymentMethod, $deliveryDate, $deliveryTime, $deliveryAddress, $recipientName, $recipientPhone)
    {
        try {
            // Create the order
            $order = new Order([
                'user_id' => $user->id,
                'total_price' => $totalPrice,
                'status' => 'pending',
                'payment_status' => $paymentMethod === 'cod' ? 'pending' : 'unpaid',
                'payment_method' => $paymentMethod,
                'type' => 'online',
                'notes' => $request->input('notes', ''),
            ]);
            $order->save();

            \Log::info('Order created successfully', ['order_id' => $order->id]);

            // Create notifications
            $this->createNotifications($order);

            // Attach products to the order
            foreach ($cartItems as $item) {
                $order->products()->attach($item->product_id, ['quantity' => $item->quantity]);
            }

            // Create delivery record
            $delivery = new Delivery([
                'order_id' => $order->id,
                'delivery_date' => $deliveryDate,
                'delivery_time' => $deliveryTime,
                'status' => 'pending',
                'recipient_name' => $recipientName,
                'recipient_phone' => $recipientPhone,
                'delivery_address' => $deliveryAddress,
            ]);
            $delivery->save();

            // Clear the cart after order is placed
            $user->cartItems()->delete();

            return redirect()->route('customer.orders.show', $order->id)
                            ->with('success', 'Order placed successfully! Your order number is #' . $order->id);

        } catch (\Exception $e) {
            \Log::error('Error creating order', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()->with('error', 'An error occurred while processing your order. Please try again.');
        }
    }

    private function redirectToPaymentGateway(Request $request, $user, $cartItems, $totalPrice, $paymentMethod, $deliveryDate, $deliveryTime, $deliveryAddress, $recipientName, $recipientPhone)
    {
        \Log::info('TEST: Entered redirectToPaymentGateway', [
            'user_id' => $user->id,
            'payment_method' => $paymentMethod,
            'cart_count' => $cartItems->count(),
            'total_price' => $totalPrice,
        ]);
        try {
            // Create a temporary order for payment processing
            $order = new Order([
                'user_id' => $user->id,
                'total_price' => $totalPrice,
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'payment_method' => $paymentMethod,
                'type' => 'online',
                'notes' => $request->input('notes', ''),
            ]);
            $order->save();
            foreach ($cartItems as $item) {
                $order->products()->attach($item->product_id, ['quantity' => $item->quantity]);
            }
            $delivery = new \App\Models\Delivery([
                'order_id' => $order->id,
                'delivery_date' => $deliveryDate,
                'delivery_time' => $deliveryTime,
                'status' => 'pending',
                'recipient_name' => $recipientName,
                'recipient_phone' => $recipientPhone,
                'delivery_address' => $deliveryAddress,
            ]);
            $delivery->save();
            // PayMongo integration for GCash/PayMaya
            if (in_array($paymentMethod, ['gcash', 'paymaya', 'maya'])) {
                $paymongo = new PayMongoService();
                $redirectUrl = route('customer.payment.callback', ['order' => $order->id]);
                // Map payment method to PayMongo type
                $sourceType = $paymentMethod === 'gcash' ? 'gcash' : 'paymaya';
                $source = $paymongo->createSource($totalPrice, $sourceType, $redirectUrl);
                $order->paymongo_source_id = $source['data']['id'];
                $order->save();
                return redirect($source['data']['attributes']['redirect']['checkout_url']);
            }
            // fallback (should not happen)
            return redirect()->route('customer.orders.show', $order->id)
                ->with('success', 'Order placed successfully! Your order number is #' . $order->id);
        } catch (\Exception $e) {
            \Log::error('Error preparing payment', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all(),
                'user_id' => $user->id,
            ]);
            return redirect()->back()->with('error', 'An error occurred while preparing payment. Please try again.');
        }
    }

    private function createNotifications($order)
    {
        try {
            // Create notification for admin
            $adminUsers = \App\Models\User::where('role', 'admin')->get();
            foreach ($adminUsers as $admin) {
                $admin->notifications()->create([
                    'id' => \Illuminate\Support\Str::uuid(),
                    'type' => 'App\Notifications\NewOrderNotification',
                    'data' => json_encode([
                        'message' => 'New order #' . $order->id . ' has been placed by ' . $order->user->name,
                        'order_id' => $order->id,
                        'user_id' => $order->user_id,
                        'total_price' => $order->total_price,
                    ]),
                ]);
            }
            
            // Create notification for customer
            $order->user->notifications()->create([
                'id' => \Illuminate\Support\Str::uuid(),
                'type' => 'App\Notifications\OrderPlacedNotification',
                'data' => json_encode([
                    'message' => 'Your order #' . $order->id . ' has been placed successfully!',
                    'order_id' => $order->id,
                    'total_price' => $order->total_price,
                ]),
            ]);
            
            \Log::info('Notifications created successfully');
        } catch (\Exception $e) {
            \Log::error('Error creating notifications', ['error' => $e->getMessage()]);
        }
    }
}
