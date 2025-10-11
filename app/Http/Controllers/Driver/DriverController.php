<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Models\Delivery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class DriverController extends Controller
{
    public function dashboard()
    {
        $driver = Auth::user();
        
        // Get orders assigned to this driver with 'assigned' status (pending acceptance)
        $pendingAcceptance = \App\Models\Order::where('assigned_driver_id', $driver->id)
            ->where('order_status', 'assigned')
            ->with(['user', 'products', 'delivery'])
            ->latest()
            ->get();
        
        // Get orders assigned to this driver with 'on_delivery' status
        $toDeliver = \App\Models\Order::where('assigned_driver_id', $driver->id)
            ->where('order_status', 'on_delivery')
            ->with(['user', 'products', 'delivery'])
            ->latest()
            ->get();
            
        // Get completed orders assigned to this driver
        $completedDeliveries = \App\Models\Order::where('assigned_driver_id', $driver->id)
            ->where('order_status', 'completed')
            ->with(['user', 'products', 'delivery'])
            ->latest()
            ->get();

        return view('driver.dashboard', compact('pendingAcceptance', 'toDeliver', 'completedDeliveries'));
    }

    public function orders()
    {
        $driver = Auth::user();
        
        // Get orders that are assigned (pending acceptance) and on delivery (accepted)
        $orders = \App\Models\Order::where('assigned_driver_id', $driver->id)
            ->whereIn('order_status', ['assigned', 'on_delivery', 'completed'])
            ->with(['user', 'products', 'delivery'])
            ->latest()
            ->get();
        
        return view('driver.orders.index', compact('orders'));
    }

    public function showOrder($orderId)
    {
        $order = \App\Models\Order::findOrFail($orderId);
        
        // Ensure the order is assigned to the authenticated driver
        if ($order->assigned_driver_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this order.');
        }

        return view('driver.orders.show', compact('order'));
    }

    public function history()
    {
        $driver = Auth::user();
        $completedDeliveries = $driver->deliveries()
            ->where('status', 'completed')
            ->with('order.user')
            ->latest()
            ->paginate(10);
        
        return view('driver.history.index', compact('completedDeliveries'));
    }

    public function showHistory(Delivery $delivery)
    {
        // Ensure the delivery belongs to the authenticated driver
        if ($delivery->driver_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this delivery.');
        }

        return view('driver.history.show', compact('delivery'));
    }

    public function profile()
    {
        return view('driver.profile');
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'contact_number' => 'required|string|max:20',
            'sex' => 'required|in:M,F',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $user = Auth::user();
        $user->name = $request->name;
        $user->contact_number = $request->contact_number;
        $user->sex = $request->sex;

        if ($request->hasFile('profile_picture')) {
            // Delete old profile picture if exists
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }
            
            // Store new profile picture
            $path = $request->file('profile_picture')->store('profile-pictures', 'public');
            $user->profile_picture = $path;
        }

        $user->save();

        return redirect()->route('driver.profile')->with('success', 'Profile updated successfully!');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();
        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()->route('driver.profile')->with('success', 'Password changed successfully!');
    }

    public function acceptOrder(Request $request, $orderId)
    {
        $order = \App\Models\Order::findOrFail($orderId);
        
        // Ensure the order is assigned to the authenticated driver
        if ($order->assigned_driver_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access'], 403);
        }

        // Ensure the order is in 'assigned' status
        if ($order->order_status !== 'assigned') {
            return response()->json(['success' => false, 'message' => 'Order is not pending acceptance'], 400);
        }

        try {
            // Use OrderStatusService to accept the order
            $orderStatusService = new \App\Services\OrderStatusService();
            
            if ($orderStatusService->acceptOrder($order, Auth::id())) {
                // Update delivery record with driver decision
                if ($order->delivery) {
                    $order->delivery->update([
                        'driver_decision' => 'accepted',
                        'decision_at' => now(),
                    ]);
                }
                
                return response()->json(['success' => true, 'message' => 'Order accepted successfully']);
            } else {
                return response()->json(['success' => false, 'message' => 'Failed to accept order'], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error accepting order: ' . $e->getMessage()], 500);
        }
    }

    public function declineOrder(Request $request, $orderId)
    {
        $request->validate([
            'reason' => 'required|string|max:255'
        ]);

        $order = \App\Models\Order::findOrFail($orderId);
        
        // Ensure the order is assigned to the authenticated driver
        if ($order->assigned_driver_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access'], 403);
        }

        // Ensure the order is in 'assigned' status
        if ($order->order_status !== 'assigned') {
            return response()->json(['success' => false, 'message' => 'Order is not pending acceptance'], 400);
        }

        try {
            // Use OrderStatusService to decline the order
            $orderStatusService = new \App\Services\OrderStatusService();
            
            if ($orderStatusService->declineOrder($order, Auth::id(), $request->reason)) {
                // Update delivery record with driver decision
                if ($order->delivery) {
                    $order->delivery->update([
                        'driver_decision' => 'declined',
                        'decline_reason' => $request->reason,
                        'decision_at' => now(),
                    ]);
                }
                
                return response()->json(['success' => true, 'message' => 'Order declined successfully']);
            } else {
                return response()->json(['success' => false, 'message' => 'Failed to decline order'], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error declining order: ' . $e->getMessage()], 500);
        }
    }

    public function completeOrder(Request $request, $orderId)
    {
        $order = \App\Models\Order::findOrFail($orderId);
        
        // Ensure the order is assigned to the authenticated driver
        if ($order->assigned_driver_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access'], 403);
        }

        // Ensure the order is in 'on_delivery' status
        if ($order->order_status !== 'on_delivery') {
            return response()->json(['success' => false, 'message' => 'Order is not in delivery status'], 400);
        }

        try {
            // Use OrderStatusService to complete the order
            $orderStatusService = new \App\Services\OrderStatusService();
            
            if ($orderStatusService->completeOrder($order, Auth::id())) {
                return response()->json(['success' => true, 'message' => 'Order completed successfully']);
            } else {
                return response()->json(['success' => false, 'message' => 'Failed to complete order'], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error completing order: ' . $e->getMessage()], 500);
        }
    }

    public function returnOrder(Request $request, $orderId)
    {
        $order = \App\Models\Order::findOrFail($orderId);
        
        // Ensure the order is assigned to the authenticated driver
        if ($order->assigned_driver_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access'], 403);
        }

        // Ensure the order is in 'on_delivery' status
        if ($order->order_status !== 'on_delivery') {
            return response()->json(['success' => false, 'message' => 'Order is not in delivery status'], 400);
        }

        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        try {
            // Get order details for notification
            $orderDetails = [
                'order_id' => $order->id,
                'customer_name' => $order->user->first_name . ' ' . $order->user->last_name,
                'customer_contact' => $order->user->contact_number,
                'delivery_address' => $order->delivery_address,
                'total_amount' => '₱' . number_format($order->total_price, 2),
                'order_date' => $order->created_at->format('M d, Y'),
                'return_reason' => $request->reason,
                'driver_name' => Auth::user()->first_name . ' ' . Auth::user()->last_name,
                'returned_at' => now()->format('M d, Y H:i')
            ];

            // Get order products
            $products = $order->products->map(function($product) {
                return [
                    'name' => $product->name,
                    'quantity' => $product->pivot->quantity,
                    'price' => '₱' . number_format($product->price, 2)
                ];
            });

            // Create notification for admin
            $notification = \App\Models\Message::create([
                'sender_id' => Auth::id(),
                'receiver_id' => \App\Models\User::where('role', 'admin')->first()->id,
                'message' => "🚨 ORDER RETURN NOTIFICATION\n\n" .
                           "Order #{$order->id} has been returned by driver.\n\n" .
                           "Customer: {$orderDetails['customer_name']}\n" .
                           "Contact: {$orderDetails['customer_contact']}\n" .
                           "Address: {$orderDetails['delivery_address']}\n" .
                           "Total: {$orderDetails['total_amount']}\n" .
                           "Order Date: {$orderDetails['order_date']}\n" .
                           "Driver: {$orderDetails['driver_name']}\n" .
                           "Returned: {$orderDetails['returned_at']}\n\n" .
                           "Products:\n" . $products->map(function($product) {
                               return "• {$product['name']} x{$product['quantity']} - {$product['price']}";
                           })->join("\n") . "\n\n" .
                           "Return Reason: {$request->reason}",
                'type' => 'return_notification',
                'is_read' => false,
                'created_at' => now()
            ]);

            // Update order status to 'returned'
            $order->update([
                'order_status' => 'returned',
                'returned_at' => now(),
                'return_reason' => $request->reason,
                'returned_by' => Auth::id()
            ]);

            // Create status history only if the same status wasn't created recently (within 1 minute)
            $recentHistory = $order->statusHistories()
                ->where('status', 'returned')
                ->where('created_at', '>=', now()->subMinute())
                ->first();
                
            if (!$recentHistory) {
                $order->statusHistories()->create([
                    'status' => 'returned',
                    'message' => "Order returned by driver. Reason: {$request->reason}",
                ]);
            }

            return response()->json([
                'success' => true, 
                'message' => 'Return notification sent to admin successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Error sending return notification: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateDeliveryStatus(Request $request, Delivery $delivery)
    {
        // Ensure the delivery belongs to the authenticated driver
        if ($delivery->driver_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access'], 403);
        }

        $request->validate([
            'status' => 'required|in:pending,in_progress,completed'
        ]);

        $delivery->status = $request->status;
        $delivery->save();

        // If delivery is completed, update the related order status using OrderStatusService
        if ($request->status === 'completed') {
            $order = $delivery->order;
            if ($order && $order->status !== 'completed') {
                // Use OrderStatusService to handle delivery completion
                \App\Services\OrderStatusService::handleDeliveryCompleted($order);
                
                // Inventory deduction logic
                foreach ($order->products as $product) {
                    $qty = $product->pivot->quantity;
                    
                    // Check if product has composition (materials needed)
                    if ($product->compositions()->exists()) {
                        // Use InventoryService to deduct materials
                        $result = \App\Services\InventoryService::deductMaterialsForProduct($product, $qty);
                        
                        if (!$result['success']) {
                            // Log the error but don't stop the process
                            \Log::error("Failed to deduct materials for product {$product->name}: " . $result['message']);
                        }
                    } else {
                        // For products without composition, just deduct from stock directly
                        $product->stock = max(0, $product->stock - $qty);
                    }
                    
                    $product->qty_sold = ($product->qty_sold ?? 0) + $qty;
                    $product->save();
                }
            }
        }

        return response()->json(['success' => true, 'message' => 'Delivery status updated successfully']);
    }
} 