<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\NotificationService;

class ReturnOrderController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Show the return order form
     */
    public function show(Order $order)
    {
        // Ensure the order belongs to the current driver
        if ($order->assigned_driver_id !== Auth::id()) {
            abort(403, 'You are not authorized to return this order.');
        }

        // Ensure the order is in a returnable state
        if (!in_array($order->status, ['on_delivery', 'processing'])) {
            return redirect()->back()->with('error', 'This order cannot be returned in its current status.');
        }

        return view('driver.orders.return', compact('order'));
    }

    /**
     * Process the return order request
     */
    public function store(Request $request, Order $order)
    {
        $request->validate([
            'return_reason' => 'required|string|max:255',
            'return_notes' => 'nullable|string|max:1000',
        ]);

        // Ensure the order belongs to the current driver
        if ($order->assigned_driver_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to return this order.'
            ], 403);
        }

        // Ensure the order is in a returnable state
        if (!in_array($order->status, ['on_delivery', 'processing'])) {
            return response()->json([
                'success' => false,
                'message' => 'This order cannot be returned in its current status.'
            ], 400);
        }

        try {
            // Update order status and return information
            $order->update([
                'status' => 'returned',
                'return_reason' => $request->return_reason,
                'return_notes' => $request->return_notes,
                'returned_at' => now(),
                'returned_by' => Auth::id(),
                'return_status' => 'pending'
            ]);

            // Create status history entry
            $order->statusHistories()->create([
                'status' => 'returned',
                'notes' => "Order returned by driver. Reason: {$request->return_reason}",
                'changed_by' => Auth::id(),
                'changed_at' => now()
            ]);

            // Send notifications
            $this->sendReturnNotifications($order);

            return response()->json([
                'success' => true,
                'message' => 'Return notification sent successfully! Order status updated to returned.',
                'order_id' => $order->id
            ]);

        } catch (\Exception $e) {
            \Log::error('Error processing order return: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to process return. Please try again.'
            ], 500);
        }
    }

    /**
     * Send notifications for returned order
     */
    private function sendReturnNotifications(Order $order)
    {
        try {
            // Notify customer
            $customer = $order->user;
            if ($customer) {
                $this->notificationService->sendOrderReturnNotification($order, $customer);
            }

            // Notify admin
            $adminUsers = User::where('role', 'admin')->get();
            foreach ($adminUsers as $admin) {
                $this->notificationService->sendAdminReturnNotification($order, $admin);
            }

            // Notify clerks
            $clerkUsers = User::where('role', 'clerk')->get();
            foreach ($clerkUsers as $clerk) {
                $this->notificationService->sendClerkReturnNotification($order, $clerk);
            }

        } catch (\Exception $e) {
            \Log::error('Error sending return notifications: ' . $e->getMessage());
        }
    }
}