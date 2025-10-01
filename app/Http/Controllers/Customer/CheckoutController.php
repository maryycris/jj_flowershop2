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
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Check if this is a "Buy now" flow (product_id and quantity provided)
        $productId = $request->input('product_id');
        $catalogProductId = $request->input('catalog_product_id');
        $quantity = $request->input('quantity', 1);
        
        if ($productId) {
            // "Buy now" flow with regular product ID
            $product = \App\Models\Product::find($productId);
            if (!$product) {
                return redirect()->route('customer.products.index')->with('error', 'Product not found.');
            }
        } elseif ($catalogProductId) {
            // "Buy now" flow with catalog product ID - convert to regular product
            $catalog = \App\Models\CatalogProduct::find($catalogProductId);
            if (!$catalog) {
                return redirect()->route('customer.dashboard')->with('error', 'Product not found.');
            }
            
            // Find existing product or create new one
            $product = \App\Models\Product::where('name', $catalog->name)
                ->where('price', $catalog->price)
                ->where('category', $catalog->category)
                ->first();
                
            if (!$product) {
                $product = new \App\Models\Product([
                    'code' => null,
                    'name' => $catalog->name,
                    'description' => $catalog->description,
                    'price' => $catalog->price,
                    'stock' => 0,
                    'category' => $catalog->category,
                    'image' => $catalog->image,
                    'image2' => $catalog->image2,
                    'image3' => $catalog->image3,
                    'status' => true,
                    'is_approved' => true,
                ]);
                $product->save();
            }
            $productId = $product->id;
        }
        
        if ($productId) {
            
            // Create a temporary cart item object for the checkout process
            $tempCartItem = new \stdClass();
            $tempCartItem->id = 'temp_' . $productId;
            $tempCartItem->product_id = $productId;
            $tempCartItem->quantity = $quantity;
            $tempCartItem->product = $product;
            
            $cartItems = collect([$tempCartItem]);
        } else {
            // Regular cart flow
            $selectedItemIds = $request->input('selected_items', []);
        
        if (!empty($selectedItemIds)) {
            $cartItems = $user->cartItems()->with('product')->whereIn('id', $selectedItemIds)->get();
        } else {
        $cartItems = $user->cartItems()->with('product')->get();
        }

        if ($cartItems->isEmpty()) {
            return redirect()->route('customer.cart.index')->with('error', 'Your cart is empty.');
            }
        }

        $subtotal = 0;
        foreach ($cartItems as $item) {
            $subtotal += $item->quantity * $item->product->price;
        }

        // Get user's addresses
        $addresses = $user->addresses()->get();
        // Use default address (or first) for initial calculation
        $deliveryAddress = $addresses->where('is_default', true)->first() ?? $addresses->first();

        // Use passed-in shipping fee if available; otherwise, calculate
        \Log::info('PaymentMethod params', ['query' => $request->query()]);
        $shippingFee = null;
        $shippingFeeParam = $request->query('shipping_fee', null);
        if ($shippingFeeParam !== null) {
            $raw = (string) $shippingFeeParam;
            $sanitized = is_numeric($raw) ? $raw : preg_replace('/[^0-9.]/', '', $raw);
            if ($sanitized !== '' && is_numeric($sanitized)) {
                $shippingFee = (float) $sanitized;
            }
        }
        if ($shippingFee === null) {
        // Calculate shipping fee using the updated helper
        $shippingFee = 30; // Default for Cordova
        if ($deliveryAddress) {
            $originAddress = 'Cordova, Cebu'; // Shop location
            $destinationAddress = $deliveryAddress->street_address . ', ' . 
                                ($deliveryAddress->barangay ?? '') . ', ' . 
                                ($deliveryAddress->municipality ?? '') . ', ' . 
                                ($deliveryAddress->city ?? '') . ', ' . 
                                ($deliveryAddress->region ?? 'Region VII');
            
            $shippingFee = \App\Helpers\ShippingFeeHelper::calculateShippingFee($originAddress, $destinationAddress);
        }
        }
        \Log::info('PaymentMethod resolved shipping fee', ['shipping_fee' => $shippingFee]);

        return view('customer.checkout.index', compact('cartItems', 'subtotal', 'deliveryAddress', 'shippingFee', 'addresses'));
    }

    public function paymentMethod(Request $request)
    {
        $user = Auth::user();
        
        // Store recipient information in session for later use
        $checkoutData = [
            'recipient_type' => $request->input('recipient_type', 'someone'),
            'delivery_address' => $request->input('delivery_address', ''),
            'recipient_name' => $request->input('recipient_name', ''),
            'recipient_phone' => $request->input('recipient_phone', ''),
            'recipient_relationship' => $request->input('recipient_relationship', ''),
            'recipient_instructions' => $request->input('recipient_instructions', ''),
            'delivery_message' => $request->input('delivery_message', ''),
            'delivery_date' => $request->input('delivery_date', ''),
            'delivery_time' => $request->input('delivery_time', ''),
            'promo_code' => $request->input('promo_code', ''),
        ];
        
        // Store in session
        session(['checkout_data' => $checkoutData]);
        
        \Log::info('Checkout data stored in session', $checkoutData);
        
        // Check if this is a "Buy now" flow (product_id and quantity provided)
        $productId = $request->input('product_id');
        $catalogProductId = $request->input('catalog_product_id');
        $quantity = $request->input('quantity', 1);
        
        if ($productId) {
            // "Buy now" flow with regular product ID
            $product = \App\Models\Product::find($productId);
            if (!$product) {
                return redirect()->route('customer.products.index')->with('error', 'Product not found.');
            }
        } elseif ($catalogProductId) {
            // "Buy now" flow with catalog product ID - convert to regular product
            $catalog = \App\Models\CatalogProduct::find($catalogProductId);
            if (!$catalog) {
                return redirect()->route('customer.dashboard')->with('error', 'Product not found.');
            }
            
            // Find existing product or create new one
            $product = \App\Models\Product::where('name', $catalog->name)
                ->where('price', $catalog->price)
                ->where('category', $catalog->category)
                ->first();
                
            if (!$product) {
                $product = new \App\Models\Product([
                    'code' => null,
                    'name' => $catalog->name,
                    'description' => $catalog->description,
                    'price' => $catalog->price,
                    'stock' => 0,
                    'category' => $catalog->category,
                    'image' => $catalog->image,
                    'image2' => $catalog->image2,
                    'image3' => $catalog->image3,
                    'status' => true,
                    'is_approved' => true,
                ]);
                $product->save();
            }
            $productId = $product->id;
        }
        
        if ($productId) {
            
            // Create a temporary cart item object for the checkout process
            $tempCartItem = new \stdClass();
            $tempCartItem->id = 'temp_' . $productId;
            $tempCartItem->product_id = $productId;
            $tempCartItem->quantity = $quantity;
            $tempCartItem->product = $product;
            
            $cartItems = collect([$tempCartItem]);
        } else {
            // Regular cart flow
            $selectedItemIds = $request->input('selected_items', []);
        
        if (!empty($selectedItemIds)) {
            $cartItems = $user->cartItems()->with('product')->whereIn('id', $selectedItemIds)->get();
        } else {
        $cartItems = $user->cartItems()->with('product')->get();
        }

        if ($cartItems->isEmpty()) {
            return redirect()->route('customer.cart.index')->with('error', 'Your cart is empty.');
            }
        }

        $subtotal = 0;
        foreach ($cartItems as $item) {
            $subtotal += $item->quantity * $item->product->price;
        }

        // Get user's default address or first address
        $deliveryAddress = $user->addresses()->where('is_default', true)->first() 
                          ?? $user->addresses()->first();

        // Use shipping fee from URL parameter if available, otherwise calculate
        $shippingFee = $request->query('shipping_fee');
        \Log::info('Payment method shipping fee check', ['url_shipping_fee' => $shippingFee, 'type' => gettype($shippingFee)]);
        if (!$shippingFee || !is_numeric($shippingFee)) {
            // Calculate shipping fee using the updated helper
            $shippingFee = 30; // Default for Cordova
            if ($deliveryAddress) {
                $originAddress = 'Cordova, Cebu'; // Shop location
                $destinationAddress = $deliveryAddress->street_address . ', ' . 
                                    ($deliveryAddress->barangay ?? '') . ', ' . 
                                    ($deliveryAddress->municipality ?? '') . ', ' . 
                                    ($deliveryAddress->city ?? '') . ', ' . 
                                    ($deliveryAddress->region ?? 'Region VII');
                
                $shippingFee = \App\Helpers\ShippingFeeHelper::calculateShippingFee($originAddress, $destinationAddress);
            }
        } else {
            $shippingFee = (float) $shippingFee;
        }

        return view('customer.checkout.payment_method', compact('cartItems', 'subtotal', 'shippingFee'));
    }

    public function processOrder(Request $request)
    {
        // Add debugging
        \Log::info('Checkout process started', ['request_data' => $request->all()]);
        
        $user = Auth::user();
        
        // Check if this is a "Buy now" flow (product_id and quantity provided)
        $productId = $request->input('product_id');
        $catalogProductId = $request->input('catalog_product_id');
        $quantity = $request->input('quantity', 1);
        
        \Log::info('ProcessOrder - Buy now flow check', [
            'product_id' => $productId,
            'catalog_product_id' => $catalogProductId,
            'quantity' => $quantity,
            'has_product_id' => !empty($productId),
            'has_catalog_product_id' => !empty($catalogProductId)
        ]);
        
        if ($productId) {
            // "Buy now" flow with regular product ID
            $product = \App\Models\Product::find($productId);
            if (!$product) {
                return redirect()->route('customer.products.index')->with('error', 'Product not found.');
            }
        } elseif ($catalogProductId) {
            // "Buy now" flow with catalog product ID - convert to regular product
            $catalog = \App\Models\CatalogProduct::find($catalogProductId);
            if (!$catalog) {
                return redirect()->route('customer.dashboard')->with('error', 'Product not found.');
            }
            
            // Find existing product or create new one
            $product = \App\Models\Product::where('name', $catalog->name)
                ->where('price', $catalog->price)
                ->where('category', $catalog->category)
                ->first();
                
            if (!$product) {
                $product = new \App\Models\Product([
                    'code' => null,
                    'name' => $catalog->name,
                    'description' => $catalog->description,
                    'price' => $catalog->price,
                    'stock' => 0,
                    'category' => $catalog->category,
                    'image' => $catalog->image,
                    'image2' => $catalog->image2,
                    'image3' => $catalog->image3,
                    'status' => true,
                    'is_approved' => true,
                ]);
                $product->save();
            }
            $productId = $product->id;
        }
        
        if ($productId) {
            
            // Create a temporary cart item object for the checkout process
            $tempCartItem = new \stdClass();
            $tempCartItem->id = 'temp_' . $productId;
            $tempCartItem->product_id = $productId;
            $tempCartItem->quantity = $quantity;
            $tempCartItem->product = $product;
            
            $cartItems = collect([$tempCartItem]);
            \Log::info('ProcessOrder - Created temp cart item for Buy Now', ['product_id' => $productId, 'quantity' => $quantity]);
        } else {
            // Regular cart flow
        $cartItems = $user->cartItems()->with('product')->get();

        if ($cartItems->isEmpty()) {
            \Log::warning('Cart is empty for user', ['user_id' => $user->id]);
            return redirect()->route('customer.cart.index')->with('error', 'Your cart is empty.');
            }
        }

        \Log::info('Cart items found', ['count' => $cartItems->count()]);

        $request->validate([
            'payment_method' => 'required|in:cod,gcash,paymaya,seabank,rcbc',
        ]);

        \Log::info('Validation passed');

        // Calculate total
        $subtotal = $cartItems->sum(function($item) { 
            return $item->quantity * $item->product->price; 
        });
        // Use shipping_fee from form if present
        $shippingFee = $request->input('shipping_fee');
        \Log::info('Shipping fee from request', ['shipping_fee' => $shippingFee, 'type' => gettype($shippingFee)]);
        if (!$shippingFee || !is_numeric($shippingFee)) {
            $shippingFee = 30; // fallback minimum only if not provided or invalid
            \Log::info('Using fallback shipping fee', ['shipping_fee' => $shippingFee]);
        }
        $totalPrice = $subtotal + $shippingFee;

        \Log::info('Calculated totals', ['subtotal' => $subtotal, 'shipping_fee' => $shippingFee, 'total' => $totalPrice]);
        
        \Log::info('Delivery details before processing', [
            'delivery_address_input' => $request->input('delivery_address'),
            'recipient_name_input' => $request->input('recipient_name'),
            'recipient_phone_input' => $request->input('recipient_phone'),
            'user_first_name' => $user->first_name,
            'user_last_name' => $user->last_name,
            'user_contact_number' => $user->contact_number,
        ]);

        $paymentMethod = $request->input('payment_method');

        // Get delivery details from session or request
        $checkoutData = session('checkout_data', []);
        $deliveryDate = $request->input('delivery_date') ?? $checkoutData['delivery_date'] ?? now()->addDays(2)->format('Y-m-d');
        $deliveryTime = $request->input('delivery_time') ?? $checkoutData['delivery_time'] ?? '09:00 AM';
        $deliveryAddress = $request->input('delivery_address') ?? $checkoutData['delivery_address'] ?? $user->addresses()->where('is_default', true)->first()?->street_address ?? 'Bang-bang Cordova, Cebu';
        
        // Ensure delivery_date and delivery_time are not empty
        if (empty($deliveryDate)) {
            $deliveryDate = now()->addDays(2)->format('Y-m-d');
        }
        if (empty($deliveryTime)) {
            $deliveryTime = '09:00 AM';
        }
        
        // Use recipient information from session if available, otherwise fall back to user info
        $recipientType = $checkoutData['recipient_type'] ?? 'someone';
        if ($recipientType === 'someone') {
            $recipientName = $checkoutData['recipient_name'] ?? $user->first_name . ' ' . $user->last_name;
            $recipientPhone = $checkoutData['recipient_phone'] ?? $user->contact_number;
        } else {
            $recipientName = $user->first_name . ' ' . $user->last_name;
            $recipientPhone = $user->contact_number;
        }
        
        // Ensure delivery address is not null
        if (empty($deliveryAddress)) {
            $deliveryAddress = 'Bang-bang Cordova, Cebu';
        }
        
        // Ensure recipient name is not null
        if (empty($recipientName)) {
            $recipientName = $user->first_name . ' ' . $user->last_name;
        }
        
        // Ensure recipient phone is not null
        if (empty($recipientPhone)) {
            $recipientPhone = $user->contact_number ?? 'N/A';
        }
        
        \Log::info('Final delivery details', [
            'delivery_address' => $deliveryAddress,
            'recipient_name' => $recipientName,
            'recipient_phone' => $recipientPhone,
            'delivery_date' => $deliveryDate,
            'delivery_time' => $deliveryTime,
        ]);

        // Handle different payment methods
        if ($paymentMethod === 'cod') {
            // For COD, create order immediately
            return $this->createOrder($request, $user, $cartItems, $totalPrice, 'cod', $deliveryDate, $deliveryTime, $deliveryAddress, $recipientName, $recipientPhone, $shippingFee);
        } else {
            // For GCash and PayMaya, redirect to payment gateway
            return $this->redirectToPaymentGateway($request, $user, $cartItems, $totalPrice, $paymentMethod, $deliveryDate, $deliveryTime, $deliveryAddress, $recipientName, $recipientPhone, $shippingFee);
        }
    }

    private function createOrder(Request $request, $user, $cartItems, $totalPrice, $paymentMethod, $deliveryDate, $deliveryTime, $deliveryAddress, $recipientName, $recipientPhone, $shippingFee)
    {
        try {
            // Create the order
            $order = new Order([
                'user_id' => $user->id,
                'total_price' => $totalPrice,
                'status' => 'pending',
                'order_status' => 'pending',
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

            // Create delivery record with enhanced recipient information
            $delivery = new Delivery([
                'order_id' => $order->id,
                'delivery_date' => $deliveryDate,
                'delivery_time' => $deliveryTime,
                'status' => 'pending',
                'recipient_name' => $recipientName,
                'recipient_phone' => $recipientPhone,
                'delivery_address' => $deliveryAddress,
                'shipping_fee' => $shippingFee,
                'special_instructions' => $checkoutData['recipient_instructions'] ?? '',
                'delivery_message' => $checkoutData['delivery_message'] ?? '',
                'recipient_relationship' => $checkoutData['recipient_relationship'] ?? '',
            ]);
            $delivery->save();

            // Clear the cart after order is placed
            // Only clear cart if this is not a "Buy now" flow
            if (!$request->has('product_id')) {
            $user->cartItems()->delete();
            }

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

    private function redirectToPaymentGateway(Request $request, $user, $cartItems, $totalPrice, $paymentMethod, $deliveryDate, $deliveryTime, $deliveryAddress, $recipientName, $recipientPhone, $shippingFee)
    {
        \Log::info('TEST: Entered redirectToPaymentGateway', [
            'user_id' => $user->id,
            'payment_method' => $paymentMethod,
            'cart_count' => $cartItems->count(),
            'total_price' => $totalPrice,
            'recipient_name' => $recipientName,
            'recipient_phone' => $recipientPhone,
        ]);
        try {
            // Ensure delivery_date and delivery_time are not empty
            if (empty($deliveryDate)) {
                $deliveryDate = now()->addDays(2)->format('Y-m-d');
            }
            if (empty($deliveryTime)) {
                $deliveryTime = '09:00 AM';
            }
            
            // Create a temporary order for payment processing
            $order = new Order([
                'user_id' => $user->id,
                'total_price' => $totalPrice,
                'status' => 'pending',
                'order_status' => 'pending',
                'payment_status' => 'unpaid',
                'payment_method' => $paymentMethod,
                'type' => 'online',
                'notes' => $request->input('notes', ''),
            ]);
            $order->save();
            foreach ($cartItems as $item) {
                $order->products()->attach($item->product_id, ['quantity' => $item->quantity]);
            }
            // Get checkout data from session for additional recipient information
            $checkoutData = session('checkout_data', []);
            
            $delivery = new \App\Models\Delivery([
                'order_id' => $order->id,
                'delivery_date' => $deliveryDate,
                'delivery_time' => $deliveryTime,
                'status' => 'pending',
                'recipient_name' => $recipientName,
                'recipient_phone' => $recipientPhone,
                'delivery_address' => $deliveryAddress,
                'shipping_fee' => $shippingFee,
                'special_instructions' => $checkoutData['recipient_instructions'] ?? '',
                'delivery_message' => $checkoutData['delivery_message'] ?? '',
                'recipient_relationship' => $checkoutData['recipient_relationship'] ?? '',
            ]);
            $delivery->save();
            
            // Clear the cart after order is created (for all payment methods)
            // Only clear cart if this is not a "Buy now" flow
            if (!$request->has('product_id')) {
            $user->cartItems()->delete();
            }
            
            // PayMongo integration via Checkout Sessions (shows E-Wallet page first)
            if (in_array($paymentMethod, ['gcash', 'paymaya'])) {
                \Log::info('Creating PayMongo Checkout Session', ['payment_method' => $paymentMethod, 'amount' => $totalPrice]);
                try {
                    $amountInCents = (int) ($totalPrice * 100);
                    $response = \Illuminate\Support\Facades\Http::withBasicAuth(env('PAYMONGO_SECRET_KEY'), '')
                        ->post(env('PAYMONGO_BASE_URL', 'https://api.paymongo.com/v1') . '/checkout_sessions', [
                            'data' => [
                                'attributes' => [
                                    'billing' => [
                                        'name' => $user->name ?? ($user->first_name . ' ' . $user->last_name),
                                        'email' => $user->email,
                                    ],
                                    'line_items' => [
                                        [
                                            'name' => 'Order #' . $order->id,
                                            'quantity' => 1,
                                            'amount' => $amountInCents,
                                            'currency' => 'PHP'
                                        ]
                                    ],
                                    'payment_method_types' => ['gcash', 'paymaya'],
                                    'success_url' => route('customer.payment.callback', $order->id),
                                    'cancel_url' => route('customer.orders.show', $order->id),
                                ]
                            ]
                        ]);
                    if ($response->successful()) {
                        $checkoutSessionId = $response['data']['id'] ?? null;
                        $checkoutUrl = $response['data']['attributes']['checkout_url'] ?? null;
                        \Log::info('Checkout Session created successfully', ['session_id' => $checkoutSessionId, 'checkout_url' => $checkoutUrl]);
                        $order->paymongo_checkout_session_id = $checkoutSessionId;
                        $order->save();
                        return redirect()->away($checkoutUrl);
                    }
                    \Log::error('Checkout Session error (wallets)', ['status' => $response->status(), 'body' => $response->body()]);
                } catch (\Throwable $e) {
                    \Log::error('Checkout Session exception (wallets)', ['error' => $e->getMessage()]);
                }
                return redirect()->route('customer.orders.show', $order->id)
                    ->with('warning', 'Order created but could not start payment session.');
            } elseif (in_array($paymentMethod, ['seabank', 'rcbc'])) {
                // Use Checkout Sessions API for Seabank/RCBC
                try {
                    $amountInCents = (int) ($totalPrice * 100);
                    $response = \Illuminate\Support\Facades\Http::withBasicAuth(env('PAYMONGO_SECRET_KEY'), '')
                        ->post(env('PAYMONGO_BASE_URL', 'https://api.paymongo.com/v1') . '/checkout_sessions', [
                            'data' => [
                                'attributes' => [
                                    'billing' => [
                                        'name' => $user->name ?? ($user->first_name . ' ' . $user->last_name),
                                        'email' => $user->email,
                                    ],
                                    'line_items' => [
                                        [
                                            'name' => 'Order #' . $order->id,
                                            'quantity' => 1,
                                            'amount' => $amountInCents,
                                            'currency' => 'PHP'
                                        ]
                                    ],
                                    'payment_method_types' => ['gcash', 'paymaya'], // PayMongo supports these payment methods
                                    'success_url' => route('customer.payment.callback', $order->id),
                                    'cancel_url' => route('customer.orders.show', $order->id),
                                ]
                            ]
                        ]);
                    if ($response->successful()) {
                        $order->paymongo_checkout_session_id = $response['data']['id'] ?? null;
                $order->save();
                        return redirect()->away($response['data']['attributes']['checkout_url']);
                    }
                    \Log::error('Checkout Session error', ['body' => $response->body()]);
                } catch (\Throwable $e) {
                    \Log::error('Checkout Session exception', ['error' => $e->getMessage()]);
                }
                return redirect()->route('customer.orders.show', $order->id)
                    ->with('warning', 'Order created but could not start payment session.');
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
