<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\Delivery;
use App\Models\CartItem;
use App\Services\OrderStatusService;
use App\Services\PayMongoService;
use App\Services\LoyaltyService;

class PaymentController extends Controller
{
    public function gcashPayment($orderId)
    {
        $order = Order::findOrFail($orderId);

        // Verify the order belongs to the authenticated user
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to order.');
        }

        // Check if order is in payment_pending status
        if ($order->status !== 'payment_pending') {
            return redirect()->route('customer.orders.show', $order->id)
                            ->with('error', 'This order is not ready for payment.');
        }

        return view('customer.payment.gcash', compact('order'));
    }

    public function grabPayPayment($orderId)
    {
        $order = Order::findOrFail($orderId);

        // Verify the order belongs to the authenticated user
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to order.');
        }

        // Check if order is in payment_pending status
        if ($order->status !== 'payment_pending') {
            return redirect()->route('customer.orders.show', $order->id)
                            ->with('error', 'This order is not ready for payment.');
        }

        // Prepare PayMongo Checkout Session for GrabPay
        try {
            $amount = $order->total_amount * 100; // PayMongo expects amount in cents

            $response = \Illuminate\Support\Facades\Http::withToken(env('PAYMONGO_SECRET_KEY'))
                ->post(env('PAYMONGO_BASE_URL', 'https://api.paymongo.com/v1') . '/checkout_sessions', [
                    'data' => [
                        'attributes' => [
                            'billing' => [
                                'name' => $order->customer_name ?? Auth::user()->name,
                                'email' => $order->customer_email ?? Auth::user()->email,
                            ],
                            'amount' => $amount,
                            'description' => 'Order #' . $order->id,
                            'redirect' => [
                                'success' => route('customer.payment.callback', $order->id),
                                'failed' => route('customer.orders.show', $order->id),
                            ],
                            'type' => 'grab_pay', // IMPORTANT: use 'grab_pay' for GrabPay
                        ]
                    ]
                ]);

            if ($response->successful()) {
                // Optionally save the source id to the order for callback verification
                $order->update([
                    'paymongo_source_id' => $response['data']['id'] ?? null,
                ]);
                return redirect($response['data']['attributes']['checkout_url']);
            } else {
                \Log::error('GrabPay Payment Error: ' . $response->body());
                return back()->with('error', 'An error occured while preparing payment. Please try again.');
            }
        } catch (\Exception $e) {
            \Log::error('GrabPay Payment Exception: ' . $e->getMessage());
            return back()->with('error', 'An error occured while preparing payment. Please try again.');
        }
    }

    public function paymayaPayment($orderId)
    {
        $order = Order::findOrFail($orderId);

        // Verify the order belongs to the authenticated user
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to order.');
        }

        // Check if order is in payment_pending status
        if ($order->status !== 'payment_pending') {
            return redirect()->route('customer.orders.show', $order->id)
                            ->with('error', 'This order is not ready for payment.');
        }

        // Prepare PayMongo Checkout Session for PayMaya
        try {
            $amount = $order->total_amount * 100; // PayMongo expects amount in cents

            $response = \Illuminate\Support\Facades\Http::withToken(env('PAYMONGO_SECRET_KEY'))
                ->post(env('PAYMONGO_BASE_URL', 'https://api.paymongo.com/v1') . '/checkout_sessions', [
                    'data' => [
                        'attributes' => [
                            'billing' => [
                                'name' => $order->customer_name ?? Auth::user()->name,
                                'email' => $order->customer_email ?? Auth::user()->email,
                            ],
                            'amount' => $amount,
                            'description' => 'Order #' . $order->id,
                            'redirect' => [
                                'success' => route('customer.payment.callback', $order->id),
                                'failed' => route('customer.orders.show', $order->id),
                            ],
                            'type' => 'paymaya', // IMPORTANT: use 'paymaya' for PayMaya
                        ]
                    ]
                ]);

            if ($response->successful()) {
                // Optionally save the source id to the order for callback verification
                $order->update([
                    'paymongo_source_id' => $response['data']['id'] ?? null,
                ]);
                return redirect($response['data']['attributes']['checkout_url']);
            } else {
                \Log::error('PayMaya Payment Error: ' . $response->body());
                return back()->with('error', 'An error occured while preparing payment. Please try again.');
            }
        } catch (\Exception $e) {
            \Log::error('PayMaya Payment Exception: ' . $e->getMessage());
            return back()->with('error', 'An error occured while preparing payment. Please try again.');
        }
    }

    public function seabankPayment($orderId)
    {
        $order = Order::findOrFail($orderId);

        // Verify the order belongs to the authenticated user
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to order.');
        }

        // Check if order is in payment_pending status
        if ($order->status !== 'payment_pending') {
            return redirect()->route('customer.orders.show', $order->id)
                            ->with('error', 'This order is not ready for payment.');
        }

        // Prepare PayMongo Checkout Session for Seabank
        try {
            $amount = $order->total_amount * 100; // PayMongo expects amount in cents

            $response = \Illuminate\Support\Facades\Http::withToken(env('PAYMONGO_SECRET_KEY'))
                ->post(env('PAYMONGO_BASE_URL', 'https://api.paymongo.com/v1') . '/checkout_sessions', [
                    'data' => [
                        'attributes' => [
                            'billing' => [
                                'name' => $order->customer_name ?? Auth::user()->name,
                                'email' => $order->customer_email ?? Auth::user()->email,
                            ],
                            'amount' => $amount,
                            'description' => 'Order #' . $order->id,
                            'redirect' => [
                                'success' => route('customer.payment.callback', $order->id),
                                'failed' => route('customer.orders.show', $order->id),
                            ],
                            'type' => 'seabank', // IMPORTANT: use 'seabank' for Seabank
                        ]
                    ]
                ]);

            if ($response->successful()) {
                // Optionally save the source id to the order for callback verification
                $order->update([
                    'paymongo_source_id' => $response['data']['id'] ?? null,
                ]);
                return redirect($response['data']['attributes']['checkout_url']);
            } else {
                \Log::error('Seabank Payment Error: ' . $response->body());
                return back()->with('error', 'An error occured while preparing payment. Please try again.');
            }
        } catch (\Exception $e) {
            \Log::error('Seabank Payment Exception: ' . $e->getMessage());
            return back()->with('error', 'An error occured while preparing payment. Please try again.');
        }
    }

    public function rcbcPayment($orderId)
    {
        $order = Order::findOrFail($orderId);

        // Verify the order belongs to the authenticated user
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to order.');
        }

        // Check if order is in payment_pending status
        if ($order->status !== 'payment_pending') {
            return redirect()->route('customer.orders.show', $order->id)
                            ->with('error', 'This order is not ready for payment.');
        }

        // Prepare PayMongo Checkout Session for RCBC
        try {
            $amount = $order->total_amount * 100; // PayMongo expects amount in cents

            $response = \Illuminate\Support\Facades\Http::withToken(env('PAYMONGO_SECRET_KEY'))
                ->post(env('PAYMONGO_BASE_URL', 'https://api.paymongo.com/v1') . '/checkout_sessions', [
                    'data' => [
                        'attributes' => [
                            'billing' => [
                                'name' => $order->customer_name ?? Auth::user()->name,
                                'email' => $order->customer_email ?? Auth::user()->email,
                            ],
                            'amount' => $amount,
                            'description' => 'Order #' . $order->id,
                            'redirect' => [
                                'success' => route('customer.payment.callback', $order->id),
                                'failed' => route('customer.orders.show', $order->id),
                            ],
                            'type' => 'rcbc', // IMPORTANT: use 'rcbc' for RCBC
                        ]
                    ]
                ]);

            if ($response->successful()) {
                // Optionally save the source id to the order for callback verification
                $order->update([
                    'paymongo_source_id' => $response['data']['id'] ?? null,
                ]);
                return redirect($response['data']['attributes']['checkout_url']);
            } else {
                \Log::error('RCBC Payment Error: ' . $response->body());
                return back()->with('error', 'An error occured while preparing payment. Please try again.');
            }
        } catch (\Exception $e) {
            \Log::error('RCBC Payment Exception: ' . $e->getMessage());
            return back()->with('error', 'An error occured while preparing payment. Please try again.');
        }
    }

    public function processPayment(Request $request, $orderId)
    {
        $order = Order::findOrFail($orderId);

        // Verify the order belongs to the authenticated user
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to order.');
        }

        // Determine payment method for validation
        $paymentMethod = $order->payment_method;
        if ($paymentMethod === 'gcash') {
            $request->validate([
                'gcash_number' => ['required', 'regex:/^09\\d{9}$/'],
                'payment_pin' => ['required', 'digits:4'],
            ], [
                'gcash_number.required' => 'GCash number is required.',
                'gcash_number.regex' => 'GCash number must be 11 digits and start with 09.',
                'payment_pin.required' => 'PIN is required.',
                'payment_pin.digits' => 'PIN must be exactly 4 digits.',
            ]);
        } elseif ($paymentMethod === 'paymaya') {
            $request->validate([
                'paymaya_number' => ['required', 'regex:/^09\\d{9}$/'],
                'payment_pin' => ['required', 'digits:4'],
            ], [
                'paymaya_number.required' => 'PayMaya number is required.',
                'paymaya_number.regex' => 'PayMaya number must be 11 digits and start with 09.',
                'payment_pin.required' => 'PIN is required.',
                'payment_pin.digits' => 'PIN must be exactly 4 digits.',
            ]);
        } elseif ($paymentMethod === 'seabank') {
            $request->validate([
                'seabank_number' => ['required', 'regex:/^09\\d{9}$/'],
                'payment_pin' => ['required', 'digits:4'],
            ], [
                'seabank_number.required' => 'Seabank number is required.',
                'seabank_number.regex' => 'Seabank number must be 11 digits and start with 09.',
                'payment_pin.required' => 'PIN is required.',
                'payment_pin.digits' => 'PIN must be exactly 4 digits.',
            ]);
        } elseif ($paymentMethod === 'rcbc') {
            $request->validate([
                'rcbc_number' => ['required', 'regex:/^09\\d{9}$/'],
                'payment_pin' => ['required', 'digits:4'],
            ], [
                'rcbc_number.required' => 'RCBC number is required.',
                'rcbc_number.regex' => 'RCBC number must be 11 digits and start with 09.',
                'payment_pin.required' => 'PIN is required.',
                'payment_pin.digits' => 'PIN must be exactly 4 digits.',
            ]);
        }

        try {
            // Simulate payment processing
            // In production, you would verify payment with the gateway
            $paymentSuccess = $request->input('payment_status') === 'success';

            if ($paymentSuccess) {
                // Use OrderStatusService to handle payment completion
                OrderStatusService::handlePaymentCompleted($order);

                // Clear session data
                session()->forget(['pending_order_id', 'payment_data']);

                // Create notifications
                $this->createNotifications($order);

                return redirect()->route('customer.orders.show', $order->id)
                                ->with('success', 'Payment successful! Your order has been confirmed and is now ready for shipping. Order #' . $order->id);
            } else {
                // Payment failed
                $order->update([
                    'status' => 'cancelled',
                    'payment_status' => 'failed',
                ]);

                // Clear session data
                session()->forget(['pending_order_id', 'payment_data']);

                return redirect()->route('customer.cart.index')
                                ->with('error', 'Payment failed. Please try again.');
            }

        } catch (\Exception $e) {
            \Log::error('Payment processing error', [
                'error' => $e->getMessage(),
                'order_id' => $order->id
            ]);

            return redirect()->route('customer.cart.index')
                            ->with('error', 'An error occurred during payment processing. Please try again.');
        }
    }

    public function showProcessing(Request $request, $orderId)
    {
        $order = Order::findOrFail($orderId);
        // Only allow if order belongs to user
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to order.');
        }
        // Collect all payment data except _token
        $paymentData = $request->except(['_token']);
        return view('customer.payment.processing', compact('order', 'paymentData'));
    }

    public function paymongoCallback(Request $request, $orderId)
    {
        \Log::info('PayMongo callback hit', ['order_id' => $orderId, 'user_id' => Auth::id()]);
        
        $order = Order::findOrFail($orderId);
        // Optional: check if the order belongs to the user
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to order.');
        }

        // DEMO: Allow simulated success without calling PayMongo
        if ($request->boolean('simulate') && env('DEMO_PAYMENTS', false)) {
            $order->update([
                'payment_status' => 'paid',
                'status' => 'pending',
            ]);
            
            // Clear only the purchased items from cart after successful payment
            $selectedItemIds = $order->selected_cart_item_ids;
            if (!empty($selectedItemIds)) {
                // Only delete the selected items that were purchased
                Auth::user()->cartItems()->whereIn('id', $selectedItemIds)->delete();
            } else {
                // If no selected items, clear all (fallback for old behavior)
                Auth::user()->cartItems()->delete();
            }
            
            // Loyalty: don't issue stamp here - wait for order completion
            // Stamps will be issued when order is completed/received by customer

            \Log::info('Demo payment simulated as paid', ['order_id' => $order->id]);
            return redirect()->route('customer.orders.show', $order->id)
                ->with('success', 'Demo payment successful! Your order is now pending approval.');
        }

        // Check payment status from PayMongo
        $checkoutSessionId = $order->paymongo_checkout_session_id;
        $sourceId = $order->paymongo_source_id;
        
        if ($checkoutSessionId) {
            // Handle Checkout Sessions (new method)
            \Log::info('Checking checkout session', ['session_id' => $checkoutSessionId]);
            try {
                $response = \Illuminate\Support\Facades\Http::withBasicAuth(env('PAYMONGO_SECRET_KEY'), '')
                    ->get(env('PAYMONGO_BASE_URL', 'https://api.paymongo.com/v1') . '/checkout_sessions/' . $checkoutSessionId);
                
                \Log::info('Checkout session response', ['status' => $response->status(), 'body' => $response->body()]);
                
                if ($response->successful()) {
                    $session = $response->json('data');
                    $sessionStatus = $session['attributes']['status'] ?? 'unknown';
                    $payments = $session['attributes']['payments'] ?? [];
                    
                    \Log::info('Checkout session status', ['session_status' => $sessionStatus, 'payments_count' => count($payments)]);
                    
                    // Check if any payment is paid
                    $isPaid = false;
                    foreach ($payments as $payment) {
                        if (($payment['attributes']['status'] ?? '') === 'paid') {
                            $isPaid = true;
                            break;
                        }
                    }
                    
                    if ($isPaid) {
                        $order->update([
                            'payment_status' => 'paid',
                            'status' => 'pending', // still needs approval
                        ]);
                        
                        // Clear only the purchased items from cart after successful payment
                        $selectedItemIds = $order->selected_cart_item_ids;
                        if (!empty($selectedItemIds)) {
                            // Only delete the selected items that were purchased
                            Auth::user()->cartItems()->whereIn('id', $selectedItemIds)->delete();
                        } else {
                            // If no selected items, clear all (fallback for old behavior)
                            Auth::user()->cartItems()->delete();
                        }
                        
                        // Loyalty: redeem if discount used (but don't issue stamp yet - wait for order completion)
                        try {
                            $loyalty = new LoyaltyService();
                            $order->loadMissing('products');
                            $discount = (float) (session('loyalty_discount_pending', 0));
                            if ($discount > 0) {
                                $card = $loyalty->getActiveCardForUser($order->user_id);
                                if ($loyalty->canRedeem($card)) {
                                    $loyalty->redeem($card, $order, $discount);
                                }
                                session()->forget('loyalty_discount_pending');
                            }
                            // Don't issue stamp here - wait for order completion
                        } catch (\Throwable $e) {
                            \Log::error('Loyalty redeem failed (checkout session)', ['order_id' => $order->id, 'error' => $e->getMessage()]);
                        }

                        \Log::info('Order updated to paid', ['order_id' => $order->id]);
                        return redirect()->route('customer.orders.show', $order->id)
                            ->with('success', 'Payment successful! Your order is now pending approval.');
                    } else {
                        \Log::info('Payment not completed', ['session_status' => $sessionStatus, 'payments' => $payments]);
                        return redirect()->route('customer.orders.show', $order->id)
                            ->with('error', 'Payment not completed. Please try again.');
                    }
                } else {
                    \Log::error('PayMongo checkout session error', ['response' => $response->body()]);
                    return redirect()->route('customer.orders.show', $order->id)
                        ->with('error', 'An error occurred while verifying payment.');
                }
            } catch (\Exception $e) {
                \Log::error('PayMongo checkout session exception', ['error' => $e->getMessage()]);
                return redirect()->route('customer.orders.show', $order->id)
                    ->with('error', 'An error occurred while verifying payment.');
            }
        } elseif ($sourceId) {
            // Handle Sources API (old method for backward compatibility)
            $paymongo = new PayMongoService();
            try {
                $status = $paymongo->getSourceStatus($sourceId);
                if ($status === 'chargeable' || $status === 'paid') {
                    $order->update([
                        'payment_status' => 'paid',
                        'status' => 'pending', // still needs approval
                    ]);
                    
                    // Clear only the purchased items from cart after successful payment
                    $selectedItemIds = $order->selected_cart_item_ids;
                    if (!empty($selectedItemIds)) {
                        // Only delete the selected items that were purchased
                        Auth::user()->cartItems()->whereIn('id', $selectedItemIds)->delete();
                    } else {
                        // If no selected items, clear all (fallback for old behavior)
                        Auth::user()->cartItems()->delete();
                    }
                    
                    // Loyalty: redeem if discount used (but don't issue stamp yet - wait for order completion)
                    try {
                        $loyalty = new LoyaltyService();
                        $order->loadMissing('products');
                        $discount = (float) (session('loyalty_discount_pending', 0));
                        if ($discount > 0) {
                            $card = $loyalty->getActiveCardForUser($order->user_id);
                            if ($loyalty->canRedeem($card)) {
                                $loyalty->redeem($card, $order, $discount);
                            }
                            session()->forget('loyalty_discount_pending');
                        }
                        // Don't issue stamp here - wait for order completion
                    } catch (\Throwable $e) {
                        \Log::error('Loyalty redeem failed (source)', ['order_id' => $order->id, 'error' => $e->getMessage()]);
                    }

                    return redirect()->route('customer.orders.show', $order->id)
                        ->with('success', 'Payment successful! Your order is now pending approval.');
                } else {
                    return redirect()->route('customer.orders.show', $order->id)
                        ->with('error', 'Payment not completed. Please try again.');
                }
            } catch (\Exception $e) {
                \Log::error('PayMongo callback error', ['error' => $e->getMessage()]);
                return redirect()->route('customer.orders.show', $order->id)
                    ->with('error', 'An error occurred while verifying payment.');
            }
        } else {
            return redirect()->route('customer.orders.show', $order->id)
                ->with('error', 'No PayMongo payment session found for this order.');
        }
    }

    private function createNotifications($order)
    {
        // Create notification for admin/clerk about new order
        // This would typically send an email or create a notification record
        \Log::info('Payment completed notification should be sent for order', ['order_id' => $order->id]);
    }
}
