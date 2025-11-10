<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PayMongoWebhookController extends Controller
{
    public function handle(Request $request)
    {
        Log::info('PayMongo webhook received', ['data' => $request->all()]);

        // Verify webhook signature (optional but recommended for security)
        $signature = $request->header('PayMongo-Signature');
        if (!$this->verifyWebhookSignature($request, $signature)) {
            Log::error('Invalid PayMongo webhook signature');
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        $eventType = $request->input('data.type');
        $attributes = $request->input('data.attributes');

        Log::info('PayMongo webhook event', ['type' => $eventType, 'attributes' => $attributes]);

        switch ($eventType) {
            case 'checkout_session.payment.paid':
                $this->handlePaymentPaid($attributes);
                break;
            case 'checkout_session.payment.failed':
                $this->handlePaymentFailed($attributes);
                break;
            case 'source.chargeable':
                $this->handleSourceChargeable($attributes);
                break;
            default:
                Log::info('Unhandled PayMongo webhook event', ['type' => $eventType]);
        }

        return response()->json(['status' => 'success']);
    }

    private function handlePaymentPaid($attributes)
    {
        $checkoutSessionId = $attributes['checkout_session_id'] ?? null;
        $paymentId = $attributes['id'] ?? null;
        $amount = $attributes['amount'] ?? 0;
        $description = $attributes['description'] ?? '';

        Log::info('Payment paid webhook', [
            'checkout_session_id' => $checkoutSessionId,
            'payment_id' => $paymentId,
            'amount' => $amount,
            'description' => $description
        ]);

        // Find order by checkout session ID
        if ($checkoutSessionId) {
            $order = Order::where('paymongo_checkout_session_id', $checkoutSessionId)->first();
            
            if ($order) {
                $order->update([
                    'payment_status' => 'paid',
                    'status' => 'pending', // Still needs admin approval
                    'paymongo_payment_id' => $paymentId,
                ]);

                Log::info('Order updated via webhook', ['order_id' => $order->id]);
            } else {
                Log::warning('Order not found for checkout session', ['checkout_session_id' => $checkoutSessionId]);
            }
        }
    }

    private function handlePaymentFailed($attributes)
    {
        $checkoutSessionId = $attributes['checkout_session_id'] ?? null;
        $paymentId = $attributes['id'] ?? null;

        Log::info('Payment failed webhook', [
            'checkout_session_id' => $checkoutSessionId,
            'payment_id' => $paymentId
        ]);

        if ($checkoutSessionId) {
            $order = Order::where('paymongo_checkout_session_id', $checkoutSessionId)->first();
            
            if ($order) {
                $order->update([
                    'payment_status' => 'failed',
                    'status' => 'cancelled',
                ]);

                Log::info('Order updated to failed via webhook', ['order_id' => $order->id]);
            }
        }
    }

    private function handleSourceChargeable($attributes)
    {
        $sourceId = $attributes['id'] ?? null;
        $amount = $attributes['amount'] ?? 0;

        Log::info('Source chargeable webhook', [
            'source_id' => $sourceId,
            'amount' => $amount
        ]);

        // Handle source-based payments (legacy method)
        if ($sourceId) {
            $order = Order::where('paymongo_source_id', $sourceId)->first();
            
            if ($order) {
                $order->update([
                    'payment_status' => 'paid',
                    'status' => 'pending',
                ]);

                Log::info('Order updated via source webhook', ['order_id' => $order->id]);
            }
        }
    }

    private function verifyWebhookSignature(Request $request, $signature)
    {
        // For now, skip signature verification
        // In production, implement proper signature verification
        // using PayMongo's webhook secret
        return true;
    }
}
