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
        // Only count deliveries assigned to this driver with status 'Out_for_delivery' for today
        $toDeliver = $driver->deliveries()
            ->where('status', 'Out_for_delivery')
            ->whereDate('delivery_date', now()->toDateString())
            ->with('order.user')
            ->latest()
            ->get();
        // Completed deliveries
        $completedDeliveries = $driver->deliveries()
            ->where('status', 'Completed')
            ->with('order.user')
            ->latest()
            ->get();

        return view('driver.dashboard', compact('toDeliver', 'completedDeliveries'));
    }

    public function orders()
    {
        $driver = Auth::user();
        $deliveries = $driver->deliveries()->with('order.user')->latest()->get();
        
        return view('driver.orders.index', compact('deliveries'));
    }

    public function showOrder(Delivery $delivery)
    {
        // Ensure the delivery belongs to the authenticated driver
        if ($delivery->driver_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this delivery.');
        }

        return view('driver.orders.show', compact('delivery'));
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
                    $product->stock = max(0, $product->stock - $qty);
                    $product->qty_sold = ($product->qty_sold ?? 0) + $qty;
                    $product->save();
                }
            }
        }

        return response()->json(['success' => true, 'message' => 'Delivery status updated successfully']);
    }
} 