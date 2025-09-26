<?php

namespace App\Http\Controllers\Clerk;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Delivery;
use App\Models\User;
use App\Models\PendingInventoryChange;
use App\Services\OrderStatusService;
use Illuminate\Http\Request;

class ClerkController extends Controller
{
    public function dashboard()
    {
        $orderStatusService = new OrderStatusService();
        $orderCounts = $orderStatusService->getOrderCounts();
        
        $restockProducts = \App\Models\Product::whereColumn('stock', '<=', 'reorder_min')->get();
        
        return view('clerk.dashboard', [
            'pendingOrdersCount' => $orderCounts['pending'],
            'approvedOrdersCount' => $orderCounts['approved'],
            'onDeliveryCount' => $orderCounts['on_delivery'],
            'completedTodayCount' => $orderCounts['completed_today'],
            'restockProducts' => $restockProducts
        ]);
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
        // Show ALL flower-related products and materials in inventory
        // Include finished products + raw materials (exclude only office supplies)
        $excludeCategories = ['Office Supplies'];
        $products = \App\Models\Product::whereNotIn('category', $excludeCategories)->get();
        return view('clerk.inventory.index', compact('products'));
    }

    public function storeProduct(Request $request) {
        $request->validate([
            'code' => 'required|string|max:255|unique:products,code',
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'reorder_min' => 'nullable|integer|min:0',
            'reorder_max' => 'nullable|integer|min:0',
            'stock' => 'nullable|integer|min:0',
        ]);

        \App\Models\Product::create([
            'code' => $request->code,
            'name' => $request->name,
            'category' => $request->category,
            'price' => $request->price,
            'cost_price' => $request->cost_price ?? 0,
            'reorder_min' => $request->reorder_min ?? 0,
            'reorder_max' => $request->reorder_max ?? 0,
            'stock' => $request->stock ?? 0,
            'description' => 'Inventory item from ' . $request->category,
            'qty_consumed' => 0,
            'qty_damaged' => 0,
            'qty_sold' => 0,
            'status' => true,
        ]);

        return redirect()->route('clerk.inventory.index')->with('success', 'Product added successfully!');
    }

    public function updateProduct(Request $request, \App\Models\Product $product) {
        $request->validate([
            'code' => 'required|string|max:255|unique:products,code,' . $product->id,
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'reorder_min' => 'nullable|integer|min:0',
            'reorder_max' => 'nullable|integer|min:0',
            'stock' => 'nullable|integer|min:0',
        ]);

        $product->update([
            'code' => $request->code,
            'name' => $request->name,
            'category' => $request->category,
            'price' => $request->price,
            'cost_price' => $request->cost_price ?? 0,
            'reorder_min' => $request->reorder_min ?? 0,
            'reorder_max' => $request->reorder_max ?? 0,
            'stock' => $request->stock ?? 0,
        ]);

        // Return JSON response for AJAX requests
        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Product updated successfully!']);
        }

        return redirect()->route('clerk.inventory.index')->with('success', 'Product updated successfully!');
    }
    public function orders(Request $request) {
        $status = $request->input('status', 'pending');
        $onlineOrders = Order::with('user')
            ->where('type', 'online')
            ->when($status, function ($q) use ($status) { $q->where('status', $status); })
            ->latest()->get();
        $walkInOrders = Order::with('user')
            ->where('type', 'walk-in')
            ->when($status, function ($q) use ($status) { $q->where('status', $status); })
            ->latest()->get();
        return view('clerk.orders.index', compact('onlineOrders', 'walkInOrders', 'status'));
    }
    public function notifications(Request $request) {
        $query = auth()->user()->notifications();
        
        // Filter by status (read/unread)
        if ($request->has('status') && $request->status !== '') {
            if ($request->status === 'read') {
                $query->whereNotNull('read_at');
            } elseif ($request->status === 'unread') {
                $query->whereNull('read_at');
            }
        }
        
        // Filter by type
        if ($request->has('type') && $request->type !== '') {
            $query->whereJsonContains('data->type', $request->type);
        }
        
        // Filter by date range
        if ($request->has('date_from') && $request->date_from !== '') {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to !== '') {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        // Order by latest first
        $notifications = $query->orderBy('created_at', 'desc')->get();
        
        return view('clerk.notifications.index', compact('notifications'));
    }

    public function deleteAllNotifications() {
        auth()->user()->notifications()->delete();
        return redirect()->route('clerk.notifications.index')->with('success', 'All notifications deleted successfully!');
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



    public function pendingOrders()
    {
        $pendingOrders = \App\Models\Order::where('status', 'pending')->with('user')->get();
        return view('clerk.orders.pending', compact('pendingOrders'));
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

    public function productCatalog(Request $request) {
        $query = \App\Models\Product::query();
        
        // Only show FINISHED PRODUCTS that are approved and active (same as customer catalog)
        // Finished product categories shown in catalog
        $includeCategories = ['Bouquets', 'Flowers', 'Fresh Flowers', 'Artificial Flowers', 'Gifts', 'Arrangements'];
        $query->where('status', true)
              ->where('is_approved', true)
              ->whereIn('category', $includeCategories);
        
        // Filter by category
        if ($request->has('category') && $request->category !== '') {
            $query->where('category', $request->category);
        }
        
        // Filter by price range
        if ($request->has('price_min') && $request->price_min !== '' && $request->price_min !== null) {
            $query->where('price', '>=', $request->price_min);
        }
        
        if ($request->has('price_max') && $request->price_max !== '' && $request->price_max !== null) {
            $query->where('price', '<=', $request->price_max);
        }
        
        // Sort products
        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }
        
        $products = $query->get();
        $promotedProducts = \App\Models\Product::where('status', true)
                                              ->where('is_approved', true)
                                              ->whereIn('category', $includeCategories)
                                              ->orderBy('created_at', 'desc')
                                              ->take(3)->get();
        
        return view('clerk.product_catalog.index', compact('products', 'promotedProducts'));
    }

    public function destroyProduct($id)
    {
        $product = \App\Models\Product::findOrFail($id);
        if ($product->image) { \Storage::disk('public')->delete($product->image); }
        $product->delete();
        return redirect()->route('clerk.product_catalog.index')->with('success', 'Product deleted successfully!');
    }

    public function destroyProductByForm(\Illuminate\Http\Request $request)
    {
        $id = $request->input('id');
        if (!$id) { return redirect()->back()->withErrors(['delete' => 'Missing product id']); }
        return $this->destroyProduct($id);
    }

    public function submitInventoryChanges(Request $request)
    {
        try {
            $editedProducts = json_decode($request->input('edited_products', '[]'), true);
            $deletedProducts = json_decode($request->input('deleted_products', '[]'), true);
            $stagedEdits = json_decode($request->input('staged_edits', '{}'), true);

            $submittedBy = auth()->id();
            $changesCount = 0;

            // Process edited products
            foreach ($editedProducts as $productId) {
                if (isset($stagedEdits[$productId])) {
                    $changes = $stagedEdits[$productId];
                    
                    PendingInventoryChange::create([
                        'product_id' => $productId,
                        'action' => 'edit',
                        'changes' => $changes,
                        'submitted_by' => $submittedBy,
                        'status' => 'pending'
                    ]);
                    $changesCount++;
                }
            }

            // Process deleted products
            foreach ($deletedProducts as $productId) {
                PendingInventoryChange::create([
                    'product_id' => $productId,
                    'action' => 'delete',
                    'changes' => null,
                    'submitted_by' => $submittedBy,
                    'status' => 'pending'
                ]);
                $changesCount++;
            }

            return response()->json([
                'success' => true,
                'message' => "Successfully submitted {$changesCount} inventory changes for admin review.",
                'changes_count' => $changesCount
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error submitting inventory changes: ' . $e->getMessage()
            ], 500);
        }
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
} 