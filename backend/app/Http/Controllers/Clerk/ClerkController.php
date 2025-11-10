<?php

namespace App\Http\Controllers\Clerk;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\CatalogProduct;
use App\Models\Delivery;
use App\Models\User;
use App\Models\PendingInventoryChange;
use App\Models\PendingInventoryAddition;
use App\Models\InventoryLog;
use App\Models\PendingProductChange;
use App\Services\ProductAvailabilityService;
use Illuminate\Support\Facades\Schema;
use App\Services\OrderStatusService;
use Illuminate\Http\Request;

class ClerkController extends Controller
{
    public function dashboard()
    {
        try {
            $orderStatusService = new OrderStatusService();
            $orderCounts = $orderStatusService->getOrderCounts();
        
        // Get low stock alerts using InventoryService (with error handling)
        $lowStockAlerts = [];
        $restockRecommendations = [];
        try {
            $inventoryService = new \App\Services\InventoryService();
            $lowStockAlerts = $inventoryService->checkLowStock();
            $restockRecommendations = $inventoryService->getRestockRecommendations();
        } catch (\Exception $e) {
            \Log::error('InventoryService error in clerk dashboard', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            error_log("INVENTORY SERVICE ERROR: " . $e->getMessage());
            // Continue with empty arrays if inventory service fails
        }
        
        // Most popular products (all-time by quantity sold) - with error handling
        $popularProducts = [];
        try {
            $popularProducts = \DB::table('order_product')
                ->join('products', 'order_product.product_id', '=', 'products.id')
                ->select('products.id', 'products.name', \DB::raw('SUM(order_product.quantity) as total_quantity'))
                ->groupBy('products.id', 'products.name')
                ->orderByDesc('total_quantity')
                ->limit(5)
                ->get();
        } catch (\Exception $e) {
            \Log::error('Error fetching popular products', ['error' => $e->getMessage()]);
            error_log("POPULAR PRODUCTS ERROR: " . $e->getMessage());
        }

        // Top selling products this month - with error handling
        $topProductsThisMonth = [];
        try {
            $topProductsThisMonth = \DB::table('order_product')
                ->join('products', 'order_product.product_id', '=', 'products.id')
                ->join('orders', 'order_product.order_id', '=', 'orders.id')
                ->whereMonth('orders.created_at', \Carbon\Carbon::now()->month)
                ->whereYear('orders.created_at', \Carbon\Carbon::now()->year)
                ->whereIn('orders.order_status', ['completed', 'delivered', 'paid'])
                ->select('products.name', \DB::raw('SUM(order_product.quantity) as total_sold'), \DB::raw('SUM(order_product.quantity * products.price) as total_revenue'))
                ->groupBy('products.name')
                ->orderByDesc('total_sold')
                ->limit(5)
                ->get();
        } catch (\Exception $e) {
            \Log::error('Error fetching top products this month', ['error' => $e->getMessage()]);
            error_log("TOP PRODUCTS ERROR: " . $e->getMessage());
        }

        // Order type distribution - with error handling
        $onlineOrdersCount = 0;
        $walkinOrdersCount = 0;
        try {
            $onlineOrdersCount = Order::where('type', 'online')->count();
            $walkinOrdersCount = Order::where('type', 'walkin')->count();
        } catch (\Exception $e) {
            \Log::error('Error counting order types', ['error' => $e->getMessage()]);
            error_log("ORDER TYPE COUNT ERROR: " . $e->getMessage());
        }

        // Recent activity (inventory movements) - with error handling
        $recentMovements = [];
        try {
            $recentMovements = \App\Models\InventoryMovement::with(['product', 'user', 'order'])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
        } catch (\Exception $e) {
            \Log::error('Error fetching recent movements', ['error' => $e->getMessage()]);
            error_log("RECENT MOVEMENTS ERROR: " . $e->getMessage());
        }
        
        return view('clerk.dashboard', [
            'pendingOrdersCount' => $orderCounts['pending'],
            'approvedOrdersCount' => $orderCounts['approved'],
            'onDeliveryCount' => $orderCounts['on_delivery'],
            'completedTodayCount' => $orderCounts['completed_today'],
            'restockProducts' => $restockRecommendations,
            'lowStockAlerts' => $lowStockAlerts,
            'popularProducts' => $popularProducts,
            'topProductsThisMonth' => $topProductsThisMonth,
            'onlineOrdersCount' => $onlineOrdersCount,
            'walkinOrdersCount' => $walkinOrdersCount,
            'recentMovements' => $recentMovements,
        ]);
        } catch (\Exception $e) {
            \Log::error('Clerk dashboard error', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('clerk.dashboard')->with('error', 'An error occurred loading the dashboard. Please try again.');
        }
    }

    public function products(Request $request) {
        $query = Product::query();

        if ($request->has('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        $products = $query->latest()->paginate(20); // 20 products per page
        $promotedProducts = Product::orderBy('created_at', 'desc')->take(3)->get();
        
        return view('clerk.products.index', compact('products', 'promotedProducts'));
    }
    public function inventory() {
        // Show ALL flower-related products and materials in inventory
        // Include finished products + raw materials (exclude only office supplies)
        // Include products marked for deletion (with red border)
        $excludeCategories = ['Office Supplies'];
        $products = \App\Models\Product::whereNotIn('category', $excludeCategories)
            ->where('status', true)
            ->get();
        return view('clerk.inventory.index', compact('products'));
    }

    public function checkApprovalStatus() {
        try {
            $userId = auth()->id();
            
            if (!$userId) {
                return response()->json(['error' => 'User not authenticated'], 401);
            }
            
            // Check if there are any pending inventory changes for this clerk
            $pendingChanges = \App\Models\PendingInventoryChange::where('submitted_by', $userId)
                ->where('status', 'pending')
                ->exists();
                
            // Check if there are any pending inventory additions for this clerk
            $pendingAdditions = \App\Models\PendingInventoryAddition::where('submitted_by', $userId)
                ->where('status', 'pending')
                ->exists();
                
            // Check if there are any pending product changes for this clerk
            $pendingProductChanges = \App\Models\PendingProductChange::where('requested_by', $userId)
                ->where('status', 'pending')
                ->exists();
            
            // Check if there are any rejected changes
            $rejectedChanges = \App\Models\PendingInventoryChange::where('submitted_by', $userId)
                ->where('status', 'rejected')
                ->exists() ||
                \App\Models\PendingInventoryAddition::where('submitted_by', $userId)
                ->where('status', 'rejected')
                ->exists() ||
                \App\Models\PendingProductChange::where('requested_by', $userId)
                ->where('status', 'rejected')
                ->exists();
            
            // If no pending changes exist, it means they've been approved
            $approved = !$pendingChanges && !$pendingAdditions && !$pendingProductChanges;
            
            return response()->json([
                'approved' => $approved,
                'rejected' => $rejectedChanges,
                'pending_changes' => $pendingChanges,
                'pending_additions' => $pendingAdditions,
                'pending_product_changes' => $pendingProductChanges,
                'user_id' => $userId
            ]);
        } catch (\Exception $e) {
            \Log::error('Error checking approval status: ' . $e->getMessage());
            return response()->json(['error' => 'Server error: ' . $e->getMessage()], 500);
        }
    }

    public function storeProduct(Request $request) {
        // Debug: Log the incoming request data
        \Log::info('Clerk product store request data:', $request->all());
        
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'price' => 'required|numeric|min:0',
                'category' => 'required|string|in:Bouquets,Packages,Gifts',
                'image' => 'required|image|max:2048',
                'description' => 'nullable|string',
                'compositions' => 'nullable|array',
                'compositions.*.component_id' => 'required_with:compositions|integer|exists:products,id',
                'compositions.*.component_name' => 'required_with:compositions|string|max:255',
                'compositions.*.quantity' => 'required_with:compositions|numeric|min:1',
                'compositions.*.unit' => 'required_with:compositions|string|max:50',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Clerk validation failed:', $e->errors());
            return redirect()->back()->withErrors($e->errors())->withInput();
        }

        $productData = [
            'name' => $validated['name'],
            'price' => $validated['price'],
            'category' => $validated['category'],
            'description' => $validated['description'] ?? null,
            'status' => true,
            'is_approved' => false, // Clerk products need admin approval
            'created_by' => auth()->id(), // Track who created the product
        ];

        if ($request->hasFile('image')) {
            $productData['image'] = $request->file('image')->store('catalog_products', 'public');
        }

        $catalogProduct = \App\Models\CatalogProduct::create($productData);
        \Log::info('Clerk catalog product created:', ['id' => $catalogProduct->id, 'name' => $catalogProduct->name]);

        // Save product compositions (materials from inventory)
        if ($request->has('compositions') && is_array($request->compositions)) {
            foreach ($request->compositions as $composition) {
                if (!empty($composition['component_id']) && !empty($composition['component_name']) && !empty($composition['quantity'])) {
                    $catalogProduct->compositions()->create([
                        'component_id' => $composition['component_id'],
                        'component_name' => $composition['component_name'],
                        'quantity' => $composition['quantity'],
                        'unit' => $composition['unit'],
                        'description' => $composition['description'] ?? null,
                    ]);
                }
            }
            \Log::info('Compositions saved for clerk product:', ['product_id' => $catalogProduct->id, 'compositions_count' => count($request->compositions)]);
        }

        // Notify all admins that a clerk added a product
        $admins = \App\Models\User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new \App\Notifications\ProductApprovalNotification($catalogProduct, auth()->user(), 'added'));
        }

        return redirect()->route('clerk.product_catalog.index')->with('success', 'Product added successfully! It will be reviewed by an admin before being published.');
    }

    public function getInventoryItems()
    {
        return \App\Services\InventoryService::getAvailableInventoryItems();
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
        // Handle tab selection
        $activeTab = $request->get('tab', 'online');
        $search = $request->get('search');
        $status = $request->input('status', 'pending');
        $todayOnly = (bool) $request->boolean('today');

        $applyStatusFilter = function($query) use ($status) {
            if ($status) {
                $query->where(function($qq) use ($status) {
                    $qq->where('order_status', $status)
                       ->orWhere(function($sub) use ($status){
                           $sub->whereNull('order_status')->where('status', $status);
                       });
                });
            }
        };

        $applyTodayFilter = function($query) use ($todayOnly, $status) {
            if ($todayOnly && $status === 'completed') {
                $query->whereDate('updated_at', now()->toDateString());
            }
        };

        // Get online orders with search
        $onlineOrdersQuery = Order::with('user')
            ->where('type', 'online')
            ->tap($applyStatusFilter)
            ->tap($applyTodayFilter);
        
        if ($search) {
            $onlineOrdersQuery->where(function($q) use ($search) {
                $q->whereHas('user', function($uq) use ($search) {
                    $uq->where('name', 'like', "%$search%");
                })
                ->orWhere('id', 'like', "%$search%");
            });
        }
        
        $onlineOrders = $onlineOrdersQuery->latest()->get();

        // Get walk-in orders with search
        $walkInOrdersQuery = Order::with('user')
            ->where('type', 'walk-in')
            ->tap($applyStatusFilter)
            ->tap($applyTodayFilter);
            
        if ($search) {
            $walkInOrdersQuery->where(function($q) use ($search) {
                $q->whereHas('user', function($uq) use ($search) {
                    $uq->where('name', 'like', "%$search%");
                })
                ->orWhere('id', 'like', "%$search%");
            });
        }
        
        $walkInOrders = $walkInOrdersQuery->latest()->get();

        // Get completed orders for history with pagination
        $completedOrdersQuery = Order::where(function($q) {
                $q->whereIn('order_status', ['completed', 'delivered'])
                  ->orWhere(function($sub) {
                      $sub->whereNull('order_status')->where('status', 'completed');
                  });
            })
            ->with(['user', 'assignedDriver', 'delivery.driver', 'products']);
        
        // Search functionality for completed orders
        if ($search) {
            $completedOrdersQuery->where(function($q) use ($search) {
                $q->whereHas('user', function($uq) use ($search) {
                    $uq->where('name', 'like', "%$search%");
                })
                ->orWhere('id', 'like', "%$search%");
            });
        }
        
        $completedOrders = $completedOrdersQuery->orderBy('updated_at', 'desc')->paginate(5);

        return view('clerk.orders.index', compact(
            'onlineOrders', 
            'walkInOrders', 
            'completedOrders',
            'activeTab',
            'search',
            'status'
        ));
    }
    public function notifications(Request $request) {
        $query = auth()->user()->notifications();
        
        // Exclude notifications that are not for clerk
        // - driver_assigned_order: Only for drivers
        // - order_approved: Only for customers
        // Filter by both notification class type and data->type
        $query->where(function($q) {
            $q->where(function($subQ) {
                // Exclude by data->type
                $subQ->where(function($sq) {
                    $sq->whereJsonDoesntContain('data->type', 'driver_assigned_order')
                       ->whereJsonDoesntContain('data->type', 'order_approved');
                });
            })
            // Also exclude by notification class type
            ->where('type', '!=', 'App\\Notifications\\DriverAssignedOrderNotification')
            ->where('type', '!=', 'App\\Notifications\\OrderApprovedNotification');
        });
        
        // Simple search functionality
        if ($request->filled('search')) {
            $searchTerm = $request->get('search');
            $query->where(function($q) use ($searchTerm) {
                // Search in notification data (message, type, title)
                $q->where('data->message', 'like', "%{$searchTerm}%")
                  ->orWhere('data->type', 'like', "%{$searchTerm}%")
                  ->orWhere('data->title', 'like', "%{$searchTerm}%")
                  // Search by date
                  ->orWhereDate('created_at', 'like', "%{$searchTerm}%");
            });
        }
        
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

    public function markNotificationAsRead(Request $request, $notificationId) {
        $notification = auth()->user()->notifications()->findOrFail($notificationId);
        $notification->markAsRead();

        return response()->json(['success' => true]);
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
            'driver_id' => 'required|exists:users,id'
        ]);

        try {
            $orderStatusService = new \App\Services\OrderStatusService();
            
            if ($orderStatusService->assignDriver($order, $request->driver_id, auth()->id())) {
                return redirect()->back()->with('success', 'Driver assigned successfully! Order is now on delivery.');
            } else {
                return redirect()->back()->with('error', 'Failed to assign driver. Please try again.');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error assigning driver: ' . $e->getMessage());
        }
    }

    public function productCatalog(Request $request) {
        $query = \App\Models\CatalogProduct::query();
        
        // Only show catalog products that are approved and active (same as customer catalog)
        // Catalog product categories shown in catalog
        $includeCategories = ['Bouquets', 'Packages', 'Gifts'];
        $query->where('status', true)
              ->where('is_approved', true)
              ->whereIn('category', $includeCategories);
        
        // Add search functionality
        if ($request->has('search') && $request->search !== '') {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->has('min_price') && $request->min_price !== '') {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->has('max_price') && $request->max_price !== '') {
            $query->where('price', '<=', $request->max_price);
        }

        // Sort products by newest first
        $query->orderBy('created_at', 'desc');
        
        $products = $query->paginate(20); // Use pagination instead of get()
        $promotedProducts = \App\Models\CatalogProduct::where('status', true)
                                              ->where('is_approved', true)
                                              ->whereIn('category', $includeCategories)
                                              ->orderBy('created_at', 'desc')
                                              ->take(3)->get();
        
        // Check availability for all products
        $availabilityService = new ProductAvailabilityService();
        $productIds = $products->pluck('id')->toArray();
        $promotedProductIds = $promotedProducts->pluck('id')->toArray();
        
        $productAvailability = $availabilityService->getBulkCatalogAvailability($productIds);
        $promotedProductAvailability = $availabilityService->getBulkCatalogAvailability($promotedProductIds);
        
        return view('clerk.product_catalog.index', compact('products', 'promotedProducts', 'productAvailability', 'promotedProductAvailability'));
    }

    public function updateCatalogProduct(Request $request, $id)
    {
        $product = CatalogProduct::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'category' => 'required|string|in:Bouquets,Packages,Gifts',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'reason' => 'required|string|max:500',
        ]);

        // Handle image upload
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('catalog_products', 'public');
        }

        // Prepare changes data
        $changes = [
            'name' => $validated['name'],
            'price' => $validated['price'],
            'category' => $validated['category'],
            'description' => $validated['description'],
        ];

        if ($imagePath) {
            $changes['image'] = $imagePath;
        }

        // Handle compositions
        if ($request->has('compositions')) {
            $compositions = [];
            foreach ($request->compositions as $composition) {
                if (!empty($composition['component_id']) && !empty($composition['quantity']) && !empty($composition['unit'])) {
                    $compositions[] = [
                        'component_id' => $composition['component_id'],
                        'component_name' => $composition['component_name'],
                        'category' => $composition['category'],
                        'quantity' => $composition['quantity'],
                        'unit' => $composition['unit'],
                    ];
                }
            }
            $changes['compositions'] = $compositions;
        }

        // Create pending change request
        $pendingChange = PendingProductChange::create([
            'product_id' => $product->id,
            'requested_by' => auth()->id(),
            'action' => 'edit',
            'changes' => $changes,
            'reason' => $validated['reason'],
            'status' => 'pending',
        ]);

        // Notify all admins about the product change request
        try {
            $adminUsers = \App\Models\User::where('role', 'admin')->get();
            $clerk = auth()->user();
            foreach ($adminUsers as $admin) {
                $admin->notify(new \App\Notifications\ProductChangeRequestNotification($pendingChange, $product, $clerk));
            }
        } catch (\Throwable $e) {
            \Log::error("Failed to send product change request notification: {$e->getMessage()}");
        }

        return redirect()->route('clerk.product_catalog.index')->with('success', 'Product change request submitted for admin approval!');
    }

    /**
     * Get product details for review
     */
    public function getProductDetails($productId)
    {
        try {
            $product = CatalogProduct::with(['compositions.componentProduct'])
                ->findOrFail($productId);

            // Add category to each composition from component product
            $product->compositions->transform(function($composition) {
                if ($composition->componentProduct) {
                    $composition->category = $composition->componentProduct->category;
                }
                return $composition;
            });

            return response()->json([
                'success' => true,
                'product' => $product
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching product details: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get product compositions for edit modal
     */
    public function getProductCompositions($productId)
    {
        try {
            $product = CatalogProduct::with(['compositions.componentProduct'])
                ->findOrFail($productId);

            // Add category to each composition from component product
            $compositions = $product->compositions->map(function($composition) {
                if ($composition->componentProduct) {
                    $composition->category = $composition->componentProduct->category;
                }
                return $composition;
            });

            return response()->json([
                'success' => true,
                'compositions' => $compositions
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching product compositions: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroyProduct($id)
    {
        $product = CatalogProduct::findOrFail($id);
        
        // Create pending change request for deletion
        $pendingChange = PendingProductChange::create([
            'product_id' => $product->id,
            'requested_by' => auth()->id(),
            'action' => 'delete',
            'changes' => null,
            'reason' => request('reason', 'Product deletion requested'),
            'status' => 'pending',
        ]);

        // Notify all admins about the product change request
        try {
            $adminUsers = \App\Models\User::where('role', 'admin')->get();
            $clerk = auth()->user();
            foreach ($adminUsers as $admin) {
                $admin->notify(new \App\Notifications\ProductChangeRequestNotification($pendingChange, $product, $clerk));
            }
        } catch (\Throwable $e) {
            \Log::error("Failed to send product change request notification: {$e->getMessage()}");
        }

        return redirect()->route('clerk.product_catalog.index')->with('success', 'Product deletion request submitted for admin approval!');
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
                    
                    // Get current product data for logging
                    $product = \App\Models\Product::find($productId);
                    $oldValues = $product ? [
                        'name' => $product->name,
                        'category' => $product->category,
                        'price' => $product->price,
                        'cost_price' => $product->cost_price,
                        'reorder_min' => $product->reorder_min,
                        'reorder_max' => $product->reorder_max,
                        'stock' => $product->stock,
                        'qty_consumed' => $product->qty_consumed,
                        'qty_damaged' => $product->qty_damaged,
                        'qty_sold' => $product->qty_sold
                    ] : [];
                    
                    PendingInventoryChange::create([
                        'product_id' => $productId,
                        'action' => 'edit',
                        'changes' => $changes,
                        'submitted_by' => $submittedBy,
                        'status' => 'pending'
                    ]);

                    // Create inventory log for admin review
                    $logData = [
                        'product_id' => $productId,
                        'user_id' => $submittedBy,
                        'action' => 'edit',
                        'old_values' => $oldValues,
                        'new_values' => $changes,
                        'description' => "Product edited by " . auth()->user()->name,
                        'ip_address' => request()->ip(),
                        'user_agent' => request()->userAgent(),
                    ];
                    if (\Illuminate\Support\Facades\Schema::hasColumn('inventory_logs','status')) {
                        $logData['status'] = 'pending';
                    }
                    InventoryLog::create($logData);
                    
                    $changesCount++;
                }
            }

            // Process newly added, staged products (no product_id yet)
            $newProducts = json_decode($request->input('new_products', '[]'), true);
            foreach ($newProducts as $newProduct) {
                if (is_array($newProduct) && !empty($newProduct)) {
                    // Only write to PendingInventoryAddition if the table exists
                    if (Schema::hasTable('pending_inventory_additions')) {
                        PendingInventoryAddition::create([
                            'changes' => $newProduct,
                            'submitted_by' => $submittedBy,
                            'status' => 'pending',
                        ]);
                    }
                    // Also write as pending create in logs (use product_id=0 to avoid NOT NULL issues)
                    $logCreate = [
                        'product_id' => null,
                        'user_id' => $submittedBy,
                        'action' => 'create',
                        'old_values' => null,
                        'new_values' => $newProduct,
                        'description' => 'Clerk staged new product',
                        'ip_address' => request()->ip(),
                        'user_agent' => request()->userAgent(),
                    ];
                    if (\Illuminate\Support\Facades\Schema::hasColumn('inventory_logs','status')) {
                        $logCreate['status'] = 'pending';
                    }
                    InventoryLog::create($logCreate);
                    $changesCount++;
                }
            }

            // Process deleted products
            foreach ($deletedProducts as $productId) {
                // Get current product data for logging
                $product = \App\Models\Product::find($productId);
                $oldValues = $product ? [
                    'name' => $product->name,
                    'category' => $product->category,
                    'price' => $product->price,
                    'cost_price' => $product->cost_price,
                    'reorder_min' => $product->reorder_min,
                    'reorder_max' => $product->reorder_max,
                    'stock' => $product->stock,
                    'qty_consumed' => $product->qty_consumed,
                    'qty_damaged' => $product->qty_damaged,
                    'qty_sold' => $product->qty_sold
                ] : [];
                
                PendingInventoryChange::create([
                    'product_id' => $productId,
                    'action' => 'delete',
                    'changes' => null,
                    'submitted_by' => $submittedBy,
                    'status' => 'pending'
                ]);
                
                // Create inventory log
                $logDelete = [
                    'product_id' => $productId,
                    'user_id' => $submittedBy,
                    'action' => 'delete',
                    'old_values' => $oldValues,
                    'new_values' => null,
                    'description' => "Product marked for deletion by " . auth()->user()->name,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent()
                ];
                if (\Illuminate\Support\Facades\Schema::hasColumn('inventory_logs','status')) {
                    $logDelete['status'] = 'pending';
                }
                \App\Models\InventoryLog::create($logDelete);
                
                $changesCount++;
            }

            // Create notification for admin if there are changes
            if ($changesCount > 0) {
                $admin = \App\Models\User::where('role', 'admin')->first();
                if ($admin) {
                    $admin->notify(new \App\Notifications\InventoryChangeNotification($changesCount, auth()->user()->name));
                }
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

    /**
     * Mark order as ready for delivery
     */
    public function markReady(Order $order)
    {
        try {
            $orderStatusService = new OrderStatusService();
            
            // First approve the order if not already approved
            if ($order->order_status !== 'approved') {
                $orderStatusService->approveOrder($order, auth()->id());
            }
            
            // Get available drivers
            $drivers = User::where('role', 'driver')->get();
            if ($drivers->isEmpty()) {
                $drivers = User::where('id', '!=', auth()->id())->take(5)->get();
            }
            
            // Auto-assign driver if available
            if ($drivers->isNotEmpty()) {
                $driver = $drivers->first();
                $orderStatusService->assignDriver($order, $driver->id, auth()->id());
            }
            
            return redirect()->back()->with('success', 'Order marked as ready and driver assigned for delivery.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to mark order as ready: ' . $e->getMessage());
        }
    }

    /**
     * Mark order as done (completed processing)
     */
    public function markDone(Order $order)
    {
        try {
            $orderStatusService = new OrderStatusService();
            
            // Complete the order
            if ($orderStatusService->completeOrder($order, auth()->id())) {
                return redirect()->back()->with('success', 'Order processing completed successfully.');
            } else {
                return redirect()->back()->with('error', 'Failed to complete order processing.');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to complete order: ' . $e->getMessage());
        }
    }

    public function storeInventory(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'reorder_min' => 'nullable|integer|min:0',
            'reorder_max' => 'nullable|integer|min:0',
            'stock' => 'nullable|integer|min:0',
        ]);

        // Generate a unique code based on category and name
        $code = strtoupper(substr($request->category, 0, 3)) . '-' . strtoupper(str_replace(' ', '', substr($request->name, 0, 5))) . '-' . rand(100, 999);

        \App\Models\Product::create([
            'code' => $code,
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
            'is_approved' => true, // Revert to auto-approve
        ]);

        return redirect()->route('clerk.inventory.index')->with('success', 'Product added successfully!');
    }
} 