<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderStatusHistory;
use Illuminate\Support\Facades\Log;

class OrderStatusService
{
    /**
     * Update order status based on payment completion
     */
    public static function handlePaymentCompleted(Order $order)
    {
        $oldStatus = $order->status;
        
        // Update order status to 'approved' (ready to ship) when payment is completed
        $order->update([
            'status' => 'approved',
            'payment_status' => 'paid'
        ]);

        // Record status change
        if ($oldStatus !== $order->status) {
            OrderStatusHistory::create([
                'order_id' => $order->id,
                'status' => $order->status,
                'message' => 'Payment completed. Order is now ready for shipping.',
            ]);
        }

        Log::info('Order status updated to approved after payment', [
            'order_id' => $order->id,
            'old_status' => $oldStatus,
            'new_status' => $order->status
        ]);
    }

    /**
     * Update order status when delivery is assigned to driver
     */
    public static function handleDeliveryAssigned(Order $order)
    {
        $oldStatus = $order->status;
        
        // Update order status to 'processing' (to receive) when delivery is assigned
        $order->update(['status' => 'processing']);

        // Record status change
        if ($oldStatus !== $order->status) {
            OrderStatusHistory::create([
                'order_id' => $order->id,
                'status' => $order->status,
                'message' => 'Delivery assigned to driver. Order is now in transit.',
            ]);
        }

        Log::info('Order status updated to processing after delivery assignment', [
            'order_id' => $order->id,
            'old_status' => $oldStatus,
            'new_status' => $order->status
        ]);
    }

    /**
     * Update order status when delivery is completed
     */
    public static function handleDeliveryCompleted(Order $order)
    {
        $oldStatus = $order->status;
        
        // Update order status to 'completed' (ready for review) when delivery is completed
        $order->update(['status' => 'completed']);

        // Record status change
        if ($oldStatus !== $order->status) {
            OrderStatusHistory::create([
                'order_id' => $order->id,
                'status' => $order->status,
                'message' => 'Delivery completed. Order is now ready for customer review.',
            ]);
        }

        Log::info('Order status updated to completed after delivery', [
            'order_id' => $order->id,
            'old_status' => $oldStatus,
            'new_status' => $order->status
        ]);
    }

    /**
     * Get the display status for customer view
     */
    public static function getCustomerDisplayStatus($orderStatus)
    {
        switch ($orderStatus) {
            case 'pending':
                return 'to_pay';
            case 'approved':
                return 'to_ship';
            case 'processing':
                return 'to_receive';
            case 'completed':
                return 'to_review';
            case 'cancelled':
                return 'cancelled';
            default:
                return $orderStatus;
        }
    }

    /**
     * Get status label for display
     */
    public static function getStatusLabel($status)
    {
        switch ($status) {
            case 'to_pay':
                return 'To Pay';
            case 'to_ship':
                return 'To Ship';
            case 'to_receive':
                return 'To Receive';
            case 'to_review':
                return 'To Review';
            case 'cancelled':
                return 'Cancelled';
            default:
                return ucfirst($status);
        }
    }
} 