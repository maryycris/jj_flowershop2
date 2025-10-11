<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\OrderStatusService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function dashboard()
    {
        $orderStatusService = new OrderStatusService();
        $orderCounts = $orderStatusService->getOrderCounts();

        // Aggregate totals for dashboard analytics
        $totalOrders = Order::count();
        $totalProducts = Product::count();
        $totalCustomers = User::where('role', 'customer')->count();

        // Most popular products (all-time by quantity sold)
        $popularProducts = \DB::table('order_product')
            ->join('products', 'order_product.product_id', '=', 'products.id')
            ->select('products.id', 'products.name', \DB::raw('SUM(order_product.quantity) as total_quantity'))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get();

        return view('admin.dashboard', [
            'pendingOrdersCount' => $orderCounts['pending'],
            'approvedOrdersCount' => $orderCounts['approved'],
            'onDeliveryCount' => $orderCounts['on_delivery'],
            'completedTodayCount' => $orderCounts['completed_today'],
            'totalOrders' => $totalOrders,
            'totalProducts' => $totalProducts,
            'totalCustomers' => $totalCustomers,
            'popularProducts' => $popularProducts,
        ]);
    }

    public function chatbox(Request $request)
    {
        $users = \App\Models\User::where('id', '!=', auth()->id())->get();
        $selectedUserId = $request->input('user_id') ?? $users->first()->id ?? null;
        $messages = [];
        if ($selectedUserId) {
            $messages = \App\Models\Message::where(function($q) use ($selectedUserId) {
                $q->where('sender_id', auth()->id())->where('receiver_id', $selectedUserId);
            })->orWhere(function($q) use ($selectedUserId) {
                $q->where('sender_id', $selectedUserId)->where('receiver_id', auth()->id());
            })->orderBy('created_at')->get();
        }
        return view('admin.chatbox', compact('users', 'selectedUserId', 'messages'));
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string',
        ]);
        \App\Models\Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
        ]);
        return redirect()->route('admin.chatbox', ['user_id' => $request->receiver_id]);
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'contact_number' => 'nullable|string|max:20',
            'profile_picture' => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['name', 'email', 'contact_number']);

        if ($request->hasFile('profile_picture')) {
            try {
                // Delete old picture if exists
                if ($user->profile_picture) {
                    \Storage::disk('public')->delete($user->profile_picture);
                }
                
                // Store new picture
                $path = $request->file('profile_picture')->store('profile_pictures', 'public');
                $data['profile_picture'] = $path;
                
                // Log successful upload
                \Log::info('Profile picture uploaded successfully', [
                    'user_id' => $user->id,
                    'file_path' => $path,
                    'file_size' => $request->file('profile_picture')->getSize(),
                    'file_type' => $request->file('profile_picture')->getMimeType()
                ]);
                
            } catch (\Exception $e) {
                \Log::error('Profile picture upload failed', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage()
                ]);
                
                return back()->withErrors(['profile_picture' => 'Failed to upload profile picture. Please try again.']);
            }
        }

        $user->update($data);

        return back()->with('success', 'Profile updated successfully!');
    }

    public function editProfile()
    {
        $user = auth()->user();
        return view('admin.profile', compact('user'));
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = auth()->user();

        if (!\Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->password = bcrypt($request->password);
        $user->save();

        return back()->with('success', 'Password updated successfully!');
    }

    /**
     * Approve an order
     */
    public function approveOrder(Request $request, $orderId)
    {
        try {
            $order = Order::findOrFail($orderId);
            $orderStatusService = new OrderStatusService();
            
            if ($orderStatusService->approveOrder($order, auth()->id())) {
                return response()->json([
                    'success' => true,
                    'message' => 'Order approved successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to approve order'
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error approving order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Assign driver to order
     */
    public function assignDriver(Request $request, $orderId)
    {
        $request->validate([
            'driver_id' => 'required|exists:users,id'
        ]);

        try {
            $order = Order::findOrFail($orderId);
            $orderStatusService = new OrderStatusService();
            
            if ($orderStatusService->assignDriver($order, $request->driver_id, auth()->id())) {
                return response()->json([
                    'success' => true,
                    'message' => 'Driver assigned successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to assign driver'
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error assigning driver: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Complete an order
     */
    public function completeOrder(Request $request, $orderId)
    {
        try {
            $order = Order::findOrFail($orderId);
            $orderStatusService = new OrderStatusService();
            
            if ($orderStatusService->completeOrder($order, auth()->id())) {
                return response()->json([
                    'success' => true,
                    'message' => 'Order completed successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to complete order'
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error completing order: ' . $e->getMessage()
            ], 500);
        }
    }

    public function assignDelivery(Request $request, Order $order)
    {
        $request->validate([
            'driver_id' => 'required|exists:users,id'
        ]);

        try {
            $orderStatusService = new OrderStatusService();
            
            if ($orderStatusService->assignDriver($order, $request->driver_id, auth()->id())) {
                return redirect()->back()->with('success', 'Driver assigned successfully! Order is now on delivery.');
            } else {
                return redirect()->back()->with('error', 'Failed to assign driver. Please try again.');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error assigning driver: ' . $e->getMessage());
        }
    }
} 