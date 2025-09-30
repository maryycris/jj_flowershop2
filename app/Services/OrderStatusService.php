<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Delivery;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderStatusService
{
    /**
     * Approve an order and update its status
     */
    public function approveOrder(Order $order, $approvedBy)
    {
        DB::beginTransaction();
        try {
            // Determine invoice status based on payment method
            $invoiceStatus = 'ready'; // Default for COD
            if (in_array(strtolower($order->payment_method), ['gcash', 'paymaya', 'rcbc'])) {
                $invoiceStatus = 'paid'; // Already paid via online payment
            }

            $order->update([
                'order_status' => 'approved',
                'approved_at' => now(),
                'approved_by' => $approvedBy,
                'invoice_status' => $invoiceStatus,
                'invoice_generated_at' => now(),
                'invoice_paid_at' => $invoiceStatus === 'paid' ? now() : null,
            ]);

            // Create payment tracking for online payments
            if ($invoiceStatus === 'paid') {
                $order->paymentTracking()->create([
                    'payment_method' => strtolower($order->payment_method),
                    'amount' => $order->total_price,
                    'payment_date' => now()->toDateString(),
                    'status' => 'completed',
                    'recorded_by' => $approvedBy,
                    'memo' => 'Payment processed via ' . strtoupper($order->payment_method),
                ]);
            }

            // Create status history
            $order->statusHistories()->create([
                'status' => 'approved',
                'notes' => 'Order approved by ' . auth()->user()->name,
                'changed_by' => $approvedBy,
            ]);

            DB::commit();
            
            Log::info("Order {$order->id} approved by user {$approvedBy} with invoice status: {$invoiceStatus}");
            
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to approve order {$order->id}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Assign driver and update order to on_delivery status
     */
    public function assignDriver(Order $order, $driverId, $assignedBy)
    {
        DB::beginTransaction();
        try {
            // Update order status
            $order->update([
                'order_status' => 'on_delivery',
                'on_delivery_at' => now(),
                'assigned_driver_id' => $driverId,
            ]);

            // Update delivery record
            if ($order->delivery) {
                $order->delivery->update([
                    'driver_id' => $driverId,
                    'status' => 'on_delivery',
                ]);
            }

            // Create status history
            $order->statusHistories()->create([
                'status' => 'on_delivery',
                'notes' => 'Driver assigned and order is now on delivery',
                'changed_by' => $assignedBy,
            ]);

            DB::commit();
            
            Log::info("Order {$order->id} assigned to driver {$driverId} by user {$assignedBy}");
            
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to assign driver to order {$order->id}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Mark order as completed
     */
    public function completeOrder(Order $order, $completedBy)
    {
        DB::beginTransaction();
        try {
            $order->update([
                'order_status' => 'completed',
                'completed_at' => now(),
            ]);

            // Update delivery status
            if ($order->delivery) {
                $order->delivery->update([
                    'status' => 'delivered',
                ]);
            }

            // Create status history
            $order->statusHistories()->create([
                'status' => 'completed',
                'notes' => 'Order completed',
                'changed_by' => $completedBy,
            ]);

            DB::commit();
            
            Log::info("Order {$order->id} completed by user {$completedBy}");
            
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to complete order {$order->id}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Register payment for COD orders
     */
    public function registerPayment(Order $order, $paymentData, $recordedBy)
    {
        DB::beginTransaction();
        try {
            // Create payment tracking record
            $paymentTracking = $order->paymentTracking()->create([
                'payment_method' => $paymentData['payment_method'],
                'amount' => $paymentData['amount'],
                'payment_date' => $paymentData['payment_date'],
                'memo' => $paymentData['memo'] ?? null,
                'status' => 'completed',
                'recorded_by' => $recordedBy,
            ]);

            // Update order invoice status to paid
            $order->update([
                'invoice_status' => 'paid',
                'invoice_paid_at' => now(),
                'payment_status' => 'paid',
            ]);

            // Create status history
            $order->statusHistories()->create([
                'status' => 'paid',
                'notes' => 'Payment registered: ' . strtoupper($paymentData['payment_method']) . ' - ₱' . number_format($paymentData['amount'], 2),
                'changed_by' => $recordedBy,
            ]);

            DB::commit();
            
            Log::info("Payment registered for order {$order->id}: {$paymentData['payment_method']} - ₱{$paymentData['amount']}");
            
            return $paymentTracking;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to register payment for order {$order->id}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get order counts for dashboard
     */
    public function getOrderCounts()
    {
        return [
            'pending' => Order::where('order_status', 'pending')->count(),
            'approved' => Order::where('order_status', 'approved')->count(),
            'on_delivery' => Order::where('order_status', 'on_delivery')->count(),
            'completed_today' => Order::where('order_status', 'completed')
                ->whereDate('completed_at', now()->toDateString())
                ->count(),
        ];
    }

    /**
     * Get customer display status for order
     */
    public static function getCustomerDisplayStatus($status)
    {
        switch (strtolower($status)) {
            case 'pending':
                return 'pending';
            case 'approved':
                return 'approved';
            case 'on_delivery':
                return 'on_delivery';
            case 'completed':
                return 'completed';
            case 'cancelled':
                return 'cancelled';
            default:
                return 'pending';
        }
    }
}