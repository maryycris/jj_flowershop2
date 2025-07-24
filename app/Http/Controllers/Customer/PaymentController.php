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

        return view('customer.payment.paymaya', compact('order'));
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
        $order = Order::findOrFail($orderId);
        // Optional: check if the order belongs to the user
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to order.');
        }

        // Check payment status from PayMongo
        $paymongo = new PayMongoService();
        $sourceId = $order->paymongo_source_id;
        if (!$sourceId) {
            return redirect()->route('customer.orders.show', $order->id)
                ->with('error', 'No PayMongo source found for this order.');
        }
        try {
            $status = $paymongo->getSourceStatus($sourceId);
            if ($status === 'chargeable' || $status === 'paid') {
                $order->update([
                    'payment_status' => 'paid',
                    'status' => 'pending', // still needs approval
                ]);
                // Optionally notify admin/clerk here
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
    }

    private function createNotifications($order)
    {
        // Create notification for admin/clerk about new order
        // This would typically send an email or create a notification record
        \Log::info('Payment completed notification should be sent for order', ['order_id' => $order->id]);
    }
} 