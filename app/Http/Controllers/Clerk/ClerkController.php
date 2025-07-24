<?php

namespace App\Http\Controllers\Clerk;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Delivery;
use App\Models\User;
use Illuminate\Http\Request;

class ClerkController extends Controller
{
    public function dashboard()
    {
        $pendingOrdersCount = \App\Models\Order::where('status', 'Pending')->count();
        $approvedOrdersCount = \App\Models\Order::where('status', 'Approved')->count();
        $onDeliveryCount = \App\Models\Order::where('status', 'Out_for_delivery')->count();
        $completedTodayCount = \App\Models\Order::where('status', 'Completed')
            ->whereDate('updated_at', now()->toDateString())
            ->count();
        $restockProducts = \App\Models\Product::whereColumn('stock', '<=', 'reorder_min')->get();
        return view('clerk.dashboard', compact('pendingOrdersCount', 'approvedOrdersCount', 'onDeliveryCount', 'completedTodayCount', 'restockProducts'));
    }

    public function products(Request $request) {
        $query = Product::query();

        if ($request->has('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        $products = $query->latest()->get();
        $promotedProducts = Product::orderBy('created_at', 'desc')->take(3)->get();
        
        return view('clerk.products.index', compact('products', 'promotedProducts'));
    }
    public function inventory() {
        $products = \App\Models\Product::all();
        return view('clerk.inventory.index', compact('products'));
    }
    public function orders(Request $request) {
        $onlineOrders = Order::with('user')->where('type', 'online')->latest()->get();
        $walkInOrders = Order::with('user')->where('type', 'walk-in')->latest()->get();
        return view('clerk.orders.index', compact('onlineOrders', 'walkInOrders'));
    }
    public function notifications() {
        $notifications = auth()->user()->notifications ?? collect();
        return view('clerk.notifications.index', compact('notifications'));
    }
    public function editProfile() {
        $user = auth()->user();
        return view('clerk.profile.edit', compact('user'));
    }
    public function sales() { return view('clerk.sales.index'); }

    public function updateProfile(Request $request) {
        $user = auth()->user();
        $request->validate([
            'name' => 'required|string|max:255',
            'contact_number' => 'required|string|max:20',
            'profile_picture' => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['name', 'contact_number']);

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
                \Log::info('Clerk profile picture uploaded successfully', [
                    'user_id' => $user->id,
                    'file_path' => $path,
                    'file_size' => $request->file('profile_picture')->getSize(),
                    'file_type' => $request->file('profile_picture')->getMimeType()
                ]);
                
            } catch (\Exception $e) {
                \Log::error('Clerk profile picture upload failed', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage()
                ]);
                
                return back()->withErrors(['profile_picture' => 'Failed to upload profile picture. Please try again.']);
            }
        }

        $user->update($data);
        return redirect()->route('clerk.profile.edit')->with('success', 'Profile updated successfully!');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|confirmed|min:8',
        ]);

        $user = auth()->user();

        if (!\Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->password = bcrypt($request->new_password);
        $user->save();

        return back()->with('success', 'Password updated successfully!');
    }

    public function storeProduct(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50',
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'reorder_min' => 'nullable|integer|min:0',
            'reorder_max' => 'nullable|integer|min:0',
            'stock' => 'nullable|integer|min:0',
            'qty_consumed' => 'nullable|integer|min:0',
            'qty_damaged' => 'nullable|integer|min:0',
            'qty_sold' => 'nullable|integer|min:0',
        ]);

        $product = new \App\Models\Product();
        $product->code = $validated['code'];
        $product->name = $validated['name'];
        $product->category = $validated['category'];
        $product->price = $validated['price'];
        $product->cost_price = $validated['cost_price'] ?? null;
        $product->reorder_min = $validated['reorder_min'] ?? null;
        $product->reorder_max = $validated['reorder_max'] ?? null;
        $product->stock = $validated['stock'] ?? 0;
        $product->qty_consumed = $validated['qty_consumed'] ?? 0;
        $product->qty_damaged = $validated['qty_damaged'] ?? 0;
        $product->qty_sold = $validated['qty_sold'] ?? 0;
        $product->save();

        // Notify admin if stock is at or below minimum
        if ($product->stock <= ($product->reorder_min ?? 0)) {
            $admins = \App\Models\User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                $admin->notify(new \App\Models\LowStockNotification($product));
            }
        }

        return redirect()->route('clerk.inventory.index')->with('success', 'Product added successfully!');
    }

    public function updateProduct(Request $request, $id)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50',
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'reorder_min' => 'nullable|integer|min:0',
            'reorder_max' => 'nullable|integer|min:0',
            'stock' => 'nullable|integer|min:0',
            'qty_consumed' => 'nullable|integer|min:0',
            'qty_damaged' => 'nullable|integer|min:0',
            'qty_sold' => 'nullable|integer|min:0',
        ]);

        $product = \App\Models\Product::findOrFail($id);
        $product->code = $validated['code'];
        $product->name = $validated['name'];
        $product->category = $validated['category'];
        $product->price = $validated['price'];
        $product->cost_price = $validated['cost_price'] ?? null;
        $product->reorder_min = $validated['reorder_min'] ?? null;
        $product->reorder_max = $validated['reorder_max'] ?? null;
        $product->stock = $validated['stock'] ?? 0;
        $product->qty_consumed = $validated['qty_consumed'] ?? 0;
        $product->qty_damaged = $validated['qty_damaged'] ?? 0;
        $product->qty_sold = $validated['qty_sold'] ?? 0;
        $product->save();

        // Notify admin if stock is at or below minimum
        if ($product->stock <= ($product->reorder_min ?? 0)) {
            $admins = \App\Models\User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                $admin->notify(new \App\Models\LowStockNotification($product));
            }
        }

        return redirect()->route('clerk.inventory.index')->with('success', 'Product updated successfully!');
    }

    public function pendingOrders()
    {
        $pendingOrders = \App\Models\Order::where('status', 'pending')->with('user')->get();
        return view('clerk.orders.pending', compact('pendingOrders'));
    }

    public function approveOrder(\App\Models\Order $order)
    {
        $order->status = 'approved';
        $order->save();
        return redirect()->route('clerk.orders.pending')->with('success', 'Order approved!');
    }

    public function assignDelivery(Request $request, Order $order)
    {
        $request->validate([
            'driver_id' => 'required|exists:users,id',
            'delivery_date' => 'required|date',
        ]);

        // Update order status to out_for_delivery
        $order->status = 'out_for_delivery';
        $order->save();

        // Create or update delivery record
        $delivery = $order->delivery;
        if (!$delivery) {
            $delivery = new \App\Models\Delivery();
            $delivery->order_id = $order->id;
        }
        $delivery->driver_id = $request->driver_id;
        $delivery->delivery_date = $request->delivery_date;
        $delivery->status = 'out_for_delivery';
        $delivery->recipient_name = $order->user->name;
        $delivery->recipient_phone = $order->user->contact_number ?? 'N/A';
        $delivery->delivery_address = $order->delivery_address ?? 'N/A';
        $delivery->save();

        return redirect()->route('clerk.orders.index')->with('success', 'Order assigned for delivery. Status updated to "Out for Delivery".');
    }

    public function productCatalog() {
        $products = \App\Models\Product::all();
        $promotedProducts = \App\Models\Product::orderBy('created_at', 'desc')->take(3)->get();
        return view('clerk.product_catalog.index', compact('products', 'promotedProducts'));
    }
} 