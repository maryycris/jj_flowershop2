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

            // Create status history only if the same status wasn't created recently (within 1 minute)
            $recentHistory = $order->statusHistories()
                ->where('status', 'approved')
                ->where('created_at', '>=', now()->subMinute())
                ->first();
                
            if (!$recentHistory) {
                $order->statusHistories()->create([
                    'status' => 'approved',
                    'message' => 'Order approved by ' . auth()->user()->name,
                ]);
            }

            // Create inventory movements for approved order
            try {
                $inventoryService = new \App\Services\InventoryManagementService();
                $inventoryService->createOrderMovement($order->fresh('products'), $approvedBy);
            } catch (\Throwable $e) {
                Log::error("Inventory movement creation failed for order {$order->id}: {$e->getMessage()}");
                // Don't fail the approval if inventory tracking fails
            }

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
     * Assign driver and update order to assigned status (pending acceptance)
     */
    public function assignDriver(Order $order, $driverId, $assignedBy)
    {
        DB::beginTransaction();
        try {
            // Update order status to 'assigned' (pending driver acceptance)
            $order->update([
                'order_status' => 'assigned',
                'assigned_driver_id' => $driverId,
                'assigned_at' => now(),
            ]);

            // Update delivery record
            if ($order->delivery) {
                $order->delivery->update([
                    'driver_id' => $driverId,
                    'status' => 'assigned',
                ]);
            }

            // Create status history only if the same status wasn't created recently (within 1 minute)
            $recentHistory = $order->statusHistories()
                ->where('status', 'assigned')
                ->where('created_at', '>=', now()->subMinute())
                ->first();
                
            if (!$recentHistory) {
                $order->statusHistories()->create([
                    'status' => 'assigned',
                    'message' => 'Driver assigned - pending acceptance',
                ]);
            }

            DB::commit();
            
            Log::info("Order {$order->id} assigned to driver {$driverId} by user {$assignedBy} - pending acceptance");
            
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to assign driver to order {$order->id}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Driver accepts the assigned order
     */
    public function acceptOrder(Order $order, $driverId)
    {
        DB::beginTransaction();
        try {
            // Ensure the order is assigned to this driver and in 'assigned' status
            if ($order->assigned_driver_id !== $driverId || $order->order_status !== 'assigned') {
                return false;
            }

            // Update order status to 'on_delivery'
            $order->update([
                'order_status' => 'on_delivery',
                'on_delivery_at' => now(),
            ]);

            // Update delivery record
            if ($order->delivery) {
                $order->delivery->update([
                    'status' => 'on_delivery',
                ]);
            }

            // Create status history only if the same status wasn't created recently (within 1 minute)
            $recentHistory = $order->statusHistories()
                ->where('status', 'on_delivery')
                ->where('created_at', '>=', now()->subMinute())
                ->first();
                
            if (!$recentHistory) {
                $order->statusHistories()->create([
                    'status' => 'on_delivery',
                    'message' => 'Driver accepted the order and is now on delivery',
                ]);
            }

            DB::commit();
            
            Log::info("Order {$order->id} accepted by driver {$driverId}");
            
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to accept order {$order->id} by driver {$driverId}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Driver declines the assigned order
     */
    public function declineOrder(Order $order, $driverId, $reason = null)
    {
        DB::beginTransaction();
        try {
            // Ensure the order is assigned to this driver and in 'assigned' status
            if ($order->assigned_driver_id !== $driverId || $order->order_status !== 'assigned') {
                return false;
            }

            // Update order status back to 'approved' (ready for reassignment)
            $order->update([
                'order_status' => 'approved',
                'assigned_driver_id' => null,
                'assigned_at' => null,
            ]);

            // Update delivery record
            if ($order->delivery) {
                $order->delivery->update([
                    'driver_id' => null,
                    'status' => 'pending',
                ]);
            }

            // Create status history only if the same status wasn't created recently (within 1 minute)
            $recentHistory = $order->statusHistories()
                ->where('status', 'approved')
                ->where('created_at', '>=', now()->subMinute())
                ->first();
                
            if (!$recentHistory) {
                $order->statusHistories()->create([
                    'status' => 'approved',
                    'message' => 'Driver declined the order' . ($reason ? " - Reason: {$reason}" : ''),
                ]);
            }

            DB::commit();
            
            Log::info("Order {$order->id} declined by driver {$driverId}" . ($reason ? " - Reason: {$reason}" : ''));
            
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to decline order {$order->id} by driver {$driverId}: " . $e->getMessage());
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

            // Create status history only if the same status wasn't created recently (within 1 minute)
            $recentHistory = $order->statusHistories()
                ->where('status', 'completed')
                ->where('created_at', '>=', now()->subMinute())
                ->first();
                
            if (!$recentHistory) {
                $order->statusHistories()->create([
                    'status' => 'completed',
                    'message' => 'Order completed',
                ]);
            }

            DB::commit();

            // Trigger inventory decrease when order is completed/received
            try {
                $inventoryService = new \App\Services\InventoryManagementService();
                $inventoryService->createOrderMovement($order->fresh('products'), $completedBy);
            } catch (\Throwable $e) {
                Log::error("Inventory update on completion failed for order {$order->id}: {$e->getMessage()}");
            }

            // Issue loyalty stamp if eligible once order is completed/received
            try {
                $loyaltyService = new \App\Services\LoyaltyService();
                $loyaltyService->issueStampIfEligible($order->fresh(['products']));
            } catch (\Throwable $e) {
                Log::error("Loyalty issuance on completion failed for order {$order->id}: {$e->getMessage()}");
            }
            
            // Trigger sales report update when order is completed/received
            try {
                $this->updateSalesReport($order);
            } catch (\Throwable $e) {
                Log::error("Sales report update failed for order {$order->id}: {$e->getMessage()}");
            }
            
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

            // Create status history only if the same status wasn't created recently (within 1 minute)
            $recentHistory = $order->statusHistories()
                ->where('status', 'paid')
                ->where('created_at', '>=', now()->subMinute())
                ->first();
                
            if (!$recentHistory) {
                $order->statusHistories()->create([
                    'status' => 'paid',
                    'message' => 'Payment registered: ' . strtoupper($paymentData['payment_method']) . ' - ₱' . number_format($paymentData['amount'], 2),
                ]);
            }

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
     * Update sales report when order is completed/received
     */
    public function updateSalesReport(Order $order)
    {
        try {
            // Update order status to ensure it's marked as completed in sales reports
            $order->update([
                'status' => 'completed', // Update legacy status field for sales reports
            ]);
            
            // Log the sales report update
            Log::info("Sales report updated for completed order {$order->id} - Total: ₱{$order->total_price}");
            
            // You can add additional sales report logic here, such as:
            // - Updating daily/monthly sales totals
            // - Sending notifications to management
            // - Updating analytics dashboards
            // - Creating sales summaries
            
        } catch (\Exception $e) {
            Log::error("Failed to update sales report for order {$order->id}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get customer display status for order
     */
    public static function getCustomerDisplayStatus($status)
    {
        switch (strtolower($status)) {
            case 'pending':
                return 'Pending Approval';
            case 'approved':
                return 'Approved';
            case 'assigned':
                return 'Driver Assigned';
            case 'on_delivery':
                return 'On Delivery';
            case 'delivered':
                return 'Delivered';
            case 'completed':
                return 'Completed';
            case 'cancelled':
                return 'Cancelled';
            case 'returned':
                return 'Returned';
            default:
                return 'Pending Approval';
        }
    }

    /**
     * Get status label for display
     */
    public static function getStatusLabel($status)
    {
        switch (strtolower($status)) {
            case 'pending':
                return 'Pending Approval';
            case 'approved':
                return 'Approved';
            case 'on_delivery':
                return 'On Delivery';
            case 'delivered':
                return 'Received';
            case 'completed':
                return 'Received';
            case 'cancelled':
                return 'Cancelled';
            default:
                return 'Pending Approval';
        }
    }
}