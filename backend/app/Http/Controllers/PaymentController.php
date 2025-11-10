<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\OrderStatusService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    protected $orderStatusService;

    public function __construct(OrderStatusService $orderStatusService)
    {
        $this->orderStatusService = $orderStatusService;
    }

    /**
     * Show payment registration form for COD orders
     */
    public function showPaymentForm(Order $order)
    {
        // Only allow payment registration for COD orders with ready invoice status
        if (strtolower($order->payment_method) !== 'cod' || $order->invoice_status !== 'ready') {
            return redirect()->back()->with('error', 'Payment registration is only available for COD orders with ready invoice status.');
        }

        $order->load(['user', 'products', 'delivery', 'paymentTracking']);
        
        return view('clerk.orders.payment-form', compact('order'));
    }

    /**
     * Register payment for COD orders
     */
    public function registerPayment(Request $request, Order $order)
    {
        \Log::info('Payment registration request', [
            'order_id' => $order->id,
            'request_data' => $request->all(),
            'user_id' => auth()->id()
        ]);
        
        // Simple test response
        if ($request->has('test')) {
            return response()->json(['success' => true, 'message' => 'Test successful']);
        }

        $validator = Validator::make($request->all(), [
            'payment_method' => 'required|string|in:cash,gcash,bank,Cash,GCash,Bank',
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date|before_or_equal:today',
            'memo' => 'nullable|string|max:500',
        ], [
            'payment_method.required' => 'Payment method is required.',
            'payment_method.in' => 'Please select a valid payment method.',
            'amount.required' => 'Amount is required.',
            'amount.numeric' => 'Amount must be a valid number.',
            'amount.min' => 'Amount must be greater than 0.',
            'payment_date.required' => 'Payment date is required.',
            'payment_date.date' => 'Please select a valid payment date.',
            'payment_date.before_or_equal' => 'Payment date cannot be in the future.',
            'memo.max' => 'Memo cannot exceed 500 characters.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors()
            ], 422);
        }

        // Only allow payment registration for COD orders
        if (strtolower($order->payment_method) !== 'cod') {
            return response()->json([
                'success' => false,
                'message' => 'Payment registration is only available for COD orders.'
            ], 400);
        }

        try {
            $paymentData = [
                'payment_method' => strtolower($request->payment_method),
                'amount' => $request->amount,
                'payment_date' => $request->payment_date,
                'memo' => $request->memo,
            ];

            $paymentTracking = $this->orderStatusService->registerPayment($order, $paymentData, auth()->id());

            if ($paymentTracking) {
                return response()->json([
                    'success' => true,
                    'message' => 'Payment registered successfully!',
                    'payment' => $paymentTracking
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to register payment. Please try again.'
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while registering payment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get payment history for an order
     */
    public function getPaymentHistory(Order $order)
    {
        $payments = $order->paymentTracking()->with('recordedBy')->orderBy('created_at', 'desc')->get();
        
        return response()->json([
            'success' => true,
            'payments' => $payments
        ]);
    }
}