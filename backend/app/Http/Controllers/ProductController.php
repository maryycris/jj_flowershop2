<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\CatalogProduct;
use App\Models\PendingProductChange;
use App\Services\ProductAvailabilityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        // Only load approved products for the main display
        // Pending products will be loaded via AJAX
        $query = CatalogProduct::where('is_approved', true);
        
        // Filter by category - only Bouquets, Packages, Gifts
        if ($request->has('category') && $request->category !== 'all') {
            $categoryMapping = [
                'bouquets' => 'Bouquets',
                'packages' => 'Packages', 
                'gifts' => 'Gifts'
            ];
            
            if (isset($categoryMapping[$request->category])) {
                $query->where('category', $categoryMapping[$request->category]);
            }
        }
        
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
        
        // Get approved products ordered by newest first
        $products = $query->orderBy('created_at', 'desc')->paginate(20); // 20 products per page
        $categories = ['Bouquets', 'Packages', 'Gifts']; // Only these 3 categories for catalog
        $promotedProducts = CatalogProduct::where('is_approved', true)->orderBy('created_at', 'desc')->take(3)->get();

        // Check availability for all products
        $availabilityService = new ProductAvailabilityService();
        $productIds = $products->pluck('id')->toArray();
        $promotedProductIds = $promotedProducts->pluck('id')->toArray();
        
        $productAvailability = $availabilityService->getBulkCatalogAvailability($productIds);
        $promotedProductAvailability = $availabilityService->getBulkCatalogAvailability($promotedProductIds);

        return view('admin.products.index', compact('products', 'categories', 'promotedProducts', 'productAvailability', 'promotedProductAvailability'));
    }

    /**
     * Approve a pending product change
     */
    public function approveProductChange(Request $request, $id)
    {
        $change = PendingProductChange::findOrFail($id);
        
        try {
            if ($change->action === 'edit') {
                // Apply the changes to the product
                $product = $change->product;
                $changes = $change->changes;
                
                // Update basic fields
                $product->update([
                    'name' => $changes['name'],
                    'price' => $changes['price'],
                    'category' => $changes['category'],
                    'description' => $changes['description'],
                ]);
                
                // Handle image update
                if (isset($changes['image'])) {
                    // Delete old image if exists
                    if ($product->image) {
                        \Storage::disk('public')->delete($product->image);
                    }
                    $product->update(['image' => $changes['image']]);
                }
                
                // Handle compositions update
                if (isset($changes['compositions'])) {
                    // Delete existing compositions
                    $product->compositions()->delete();
                    
                    // Add new compositions
                    foreach ($changes['compositions'] as $composition) {
                        $product->compositions()->create($composition);
                    }
                }
                
            } elseif ($change->action === 'delete') {
                // Delete the product
                $product = $change->product;
                if ($product->image) {
                    \Storage::disk('public')->delete($product->image);
                }
                $product->delete();
            }
            
            // Mark the change as approved
            $change->update([
                'status' => 'approved',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
                'admin_notes' => $request->input('admin_notes', ''),
            ]);
            
            // Notify the clerk who requested the change
            if ($change->requested_by) {
                try {
                    $clerk = \App\Models\User::find($change->requested_by);
                    if ($clerk && $clerk->role === 'clerk') {
                        $product = $change->product;
                        $productName = $product ? $product->name : 'Product';
                        $clerk->notify(new \App\Notifications\ProductChangesApprovedNotification($product ?? $change->product_id, $productName));
                    }
                } catch (\Throwable $e) {
                    \Log::error("Failed to send product approval notification to clerk: {$e->getMessage()}");
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Product change approved successfully!'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error approving change: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject a pending product change
     */
    public function rejectProductChange(Request $request, $id)
    {
        $change = PendingProductChange::findOrFail($id);
        
        try {
            $change->update([
                'status' => 'rejected',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
                'admin_notes' => $request->input('admin_notes', ''),
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Product change rejected successfully!'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error rejecting change: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get details of a pending product change
     */
    public function getProductChangeDetails($id)
    {
        try {
            $change = PendingProductChange::with(['product', 'requestedBy'])
                ->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'change' => $change
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching change details: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all pending product changes
     */
    public function getPendingProductChanges()
    {
        try {
            $changes = PendingProductChange::with(['product', 'requestedBy'])
                ->pending()
                ->orderBy('created_at', 'desc')
                ->get();
            
            return response()->json($changes);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching pending changes: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available categories from inventory
     */
    public function getCategories()
    {
        // Only return the specific inventory categories used for material selection
        $inventoryCategories = [
            'Fresh Flowers',
            'Dried Flowers', 
            'Artificial Flowers',
            'Greenery',
            'Floral Supplies',
            'Packaging Materials',
            'Wrappers',
            'Ribbon',
            'Other Offers'
        ];
        
        // Filter to only include categories that actually exist in the database
        $existingCategories = Product::select('category')
            ->whereIn('category', $inventoryCategories)
            ->where('status', true)
            ->distinct()
            ->pluck('category')
            ->toArray();
        
        // Return the intersection of expected and existing categories
        $categories = array_intersect($inventoryCategories, $existingCategories);
        
        return response()->json(array_values($categories));
    }

    /**
     * Get inventory items by category for product composition
     */
    public function getInventoryByCategory($category = null)
    {
        $query = Product::where('category', 'NOT LIKE', '%Office Supplies%')
            ->where('status', true);
            // Removed ->where('is_approved', true) to include non-approved items
        
        if ($category) {
            $query->where('category', $category);
        }
        
        $items = $query->orderBy('name')
            ->get()
            ->map(function($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'category' => $product->category,
                    'stock' => $product->stock ?? 0,
                    'price' => $product->price ?? 0,
                    'description' => $product->description ?? '',
                    'unit' => $this->getDefaultUnit($product->category),
                    'is_approved' => $product->is_approved
                ];
            });
        
        return response()->json($items);
    }
    
    /**
     * Get default unit for category
     */
    private function getDefaultUnit($category)
    {
        switch ($category) {
            case 'Fresh Flowers':
            case 'Dried Flowers':
            case 'Artificial Flowers':
                return 'stems';
            case 'Floral Supplies':
            case 'Packaging Materials':
                return 'pieces';
            default:
                return 'pieces';
        }
    }

    public function show(Product $product)
    {
        return view('customer.products.show', compact('product'));
    }

    public function store(Request $request)
    {
        // Debug: Log the incoming request data
        \Log::info('Product store request data:', $request->all());
        
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
            \Log::error('Validation failed:', $e->errors());
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($e->errors())->withInput();
        }

        $productData = [
            'name' => $validated['name'],
            'price' => $validated['price'],
            'category' => $validated['category'],
            'description' => $validated['description'] ?? null,
            'status' => true,
            'is_approved' => true, // Admin products are auto-approved
            'created_by' => auth()->id(), // Track who created the product
            'approved_by' => auth()->id(), // Admin auto-approves their own products
            'approved_at' => now(),
        ];

        if ($request->hasFile('image')) {
            try {
                // Check if Cloudinary is configured, if not use local storage
                $driver = config('filesystems.disks.public.driver');
                if ($driver === 'cloudinary') {
                    // Verify Cloudinary credentials are actually set
                    $cloudName = env('CLOUDINARY_CLOUD_NAME');
                    $apiKey = env('CLOUDINARY_API_KEY');
                    $apiSecret = env('CLOUDINARY_API_SECRET');
                    
                    if (!$cloudName || !$apiKey || !$apiSecret) {
                        // Fall back to local storage if credentials are missing
                        \Log::warning('Cloudinary driver set but credentials missing, using local storage');
                        $productData['image'] = $request->file('image')->store('catalog_products', 'local');
                    } else {
                        $productData['image'] = $request->file('image')->store('catalog_products', 'public');
                    }
                } else {
                    $productData['image'] = $request->file('image')->store('catalog_products', 'public');
                }
                \Log::info('Image uploaded successfully', ['path' => $productData['image'], 'driver' => $driver]);
            } catch (\Exception $e) {
                \Log::error('Failed to upload image', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                // If Cloudinary error, try falling back to local storage
                if (strpos($e->getMessage(), 'Invalid configuration') !== false || 
                    strpos($e->getMessage(), 'Cloudinary') !== false) {
                    try {
                        \Log::info('Cloudinary failed, attempting local storage fallback');
                        $productData['image'] = $request->file('image')->store('catalog_products', 'local');
                        \Log::info('Image uploaded to local storage as fallback', ['path' => $productData['image']]);
                    } catch (\Exception $fallbackError) {
                        \Log::error('Fallback to local storage also failed', ['error' => $fallbackError->getMessage()]);
                        if ($request->expectsJson() || $request->wantsJson()) {
                            return response()->json([
                                'success' => false,
                                'message' => 'Failed to upload image. Please check storage configuration.'
                            ], 500);
                        }
                        return redirect()->back()->withErrors(['image' => 'Failed to upload image. Please try again.'])->withInput();
                    }
                } else {
                    if ($request->expectsJson() || $request->wantsJson()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Failed to upload image: ' . $e->getMessage()
                        ], 500);
                    }
                    return redirect()->back()->withErrors(['image' => 'Failed to upload image. Please try again.'])->withInput();
                }
            }
        }

        try {
            $catalogProduct = CatalogProduct::create($productData);
            \Log::info('Catalog product created:', ['id' => $catalogProduct->id, 'name' => $catalogProduct->name]);

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
                \Log::info('Compositions saved for product:', ['product_id' => $catalogProduct->id, 'compositions_count' => count($request->compositions)]);
            }

            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Product added successfully to catalog.',
                    'product' => $catalogProduct
                ]);
            }

            return Redirect::route('admin.products.index')->with('success', 'Product added successfully to catalog.');
        } catch (\Exception $e) {
            \Log::error('Error creating product:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while adding the product: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->withErrors(['error' => 'An error occurred while adding the product.'])->withInput();
        }
    }

    public function edit(Product $product)
    {
        return view('admin.products.edit', compact('product'));
    }

    public function update(Request $request, CatalogProduct $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'category' => 'required|string|in:Bouquets,Packages,Gifts',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            try {
                // Delete old image if exists
                if ($product->image) {
                    try {
                        Storage::disk('public')->delete($product->image);
                        \Log::info('Old image deleted', ['path' => $product->image]);
                    } catch (\Exception $e) {
                        \Log::warning('Failed to delete old image (non-critical)', [
                            'path' => $product->image,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
                
                // Check if Cloudinary is configured, if not use local storage
                $driver = config('filesystems.disks.public.driver');
                if ($driver === 'cloudinary') {
                    // Verify Cloudinary credentials are actually set
                    $cloudName = env('CLOUDINARY_CLOUD_NAME');
                    $apiKey = env('CLOUDINARY_API_KEY');
                    $apiSecret = env('CLOUDINARY_API_SECRET');
                    
                    if (!$cloudName || !$apiKey || !$apiSecret) {
                        // Fall back to local storage if credentials are missing
                        \Log::warning('Cloudinary driver set but credentials missing, using local storage');
                        $newImagePath = $request->file('image')->store('catalog_products', 'local');
                    } else {
                        $newImagePath = $request->file('image')->store('catalog_products', 'public');
                    }
                } else {
                    $newImagePath = $request->file('image')->store('catalog_products', 'public');
                }
                
                $validated['image'] = $newImagePath;
                \Log::info('New image uploaded successfully', ['path' => $newImagePath, 'driver' => $driver]);
            } catch (\Exception $e) {
                \Log::error('Failed to upload image during update', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                // If Cloudinary error, try falling back to local storage
                if (strpos($e->getMessage(), 'Invalid configuration') !== false || 
                    strpos($e->getMessage(), 'Cloudinary') !== false) {
                    try {
                        \Log::info('Cloudinary failed, attempting local storage fallback');
                        $newImagePath = $request->file('image')->store('catalog_products', 'local');
                        $validated['image'] = $newImagePath;
                        \Log::info('Image uploaded to local storage as fallback', ['path' => $newImagePath]);
                    } catch (\Exception $fallbackError) {
                        \Log::error('Fallback to local storage also failed', ['error' => $fallbackError->getMessage()]);
                        if ($request->expectsJson()) {
                            return response()->json([
                                'success' => false,
                                'message' => 'Failed to upload image. Please check storage configuration.'
                            ], 500);
                        }
                        return redirect()->back()->withErrors(['image' => 'Failed to upload image. Please try again.'])->withInput();
                    }
                } else {
                    if ($request->expectsJson()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Failed to upload image: ' . $e->getMessage()
                        ], 500);
                    }
                    return redirect()->back()->withErrors(['image' => 'Failed to upload image. Please try again.'])->withInput();
                }
            }
        }

        // Handle compositions
        if ($request->has('compositions')) {
            // Delete existing compositions
            $product->compositions()->delete();
            
            // Add new compositions
            foreach ($request->compositions as $composition) {
                if (!empty($composition['component_id']) && !empty($composition['quantity']) && !empty($composition['unit'])) {
                    $product->compositions()->create([
                        'component_id' => $composition['component_id'],
                        'component_name' => $composition['component_name'],
                        'category' => $composition['category'],
                        'quantity' => $composition['quantity'],
                        'unit' => $composition['unit'],
                    ]);
                }
            }
        }

        $product->update($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Product updated successfully.',
                'product' => $product
            ]);
        }

        return Redirect::route('admin.products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Request $request, CatalogProduct $product)
    {
        // Delete all associated images from storage
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        if ($product->image2) {
            Storage::disk('public')->delete($product->image2);
        }
        if ($product->image3) {
            Storage::disk('public')->delete($product->image3);
        }

        $product->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Product deleted successfully.'
            ]);
        }

        return Redirect::route('admin.products.index')->with('success', 'Product deleted successfully.');
    }

    public function updateImages(Request $request, CatalogProduct $product)
    {
        $request->validate([
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $newImagePath = $request->file('image')->store('catalog_products', 'public');
            $product->image = $newImagePath;
        }

        $product->save();

        return Redirect::back()->with('success', 'Product image updated successfully.');
    }

    public function deleteImage(CatalogProduct $product)
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
            $product->image = null;
            $product->save();
        }

        return Redirect::back()->with('success', 'Product image deleted successfully.');
    }

    public function bestsellers() {
        return view('products.bestsellers');
    }

    public function customize() {
        return view('products.customize');
    }

    public function submitCustomization(Request $request)
    {
        // You can add validation and saving logic here
        return back()->with('success', 'Customization submitted!');
    }

    /**
     * Show the admin inventory page.
     */
    public function inventory(Request $request)
    {
        // Show ALL flower-related products and materials in inventory
        // Include finished products + raw materials (exclude only office supplies)
        // Include pending products for admin approval
        $excludeCategories = ['Office Supplies'];
        $products = Product::whereNotIn('category', $excludeCategories)
            ->where('status', true)
            ->get();
        return view('admin.inventory', compact('products'));
    }

    public function storeInventory(Request $request)
    {
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

        Product::create([
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
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Product added successfully!',
                'product' => Product::latest()->first()
            ]);
        }

        return redirect()->route('admin.inventory.index')->with('success', 'Product added successfully!');
    }

    public function updateInventory(Request $request, Product $product)
    {
        $request->validate([
            'code' => 'nullable|string|max:255|unique:products,code,' . $product->id,
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'reorder_min' => 'nullable|integer|min:0',
            'reorder_max' => 'nullable|integer|min:0',
            'stock' => 'nullable|integer|min:0',
            'qty_consumed' => 'nullable|integer|min:0',
            'qty_damaged' => 'nullable|integer|min:0',
            'qty_sold' => 'nullable|integer|min:0',
        ]);

        $product->update([
            'code' => $request->input('code', $product->code),
            'name' => $request->name,
            'category' => $request->category,
            'price' => $request->price,
            'cost_price' => $request->cost_price ?? 0,
            'reorder_min' => $request->reorder_min ?? 0,
            'reorder_max' => $request->reorder_max ?? 0,
            'stock' => $request->stock ?? 0,
            'qty_consumed' => $request->qty_consumed ?? $product->qty_consumed,
            'qty_damaged' => $request->qty_damaged ?? $product->qty_damaged,
            'qty_sold' => $request->qty_sold ?? $product->qty_sold,
        ]);

        // Return JSON response for AJAX requests
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Material updated successfully!',
                'product' => $product
            ]);
        }

        return redirect()->route('admin.inventory.index')->with('success', 'Material updated successfully!');
    }

    public function destroyInventory(Product $product)
    {
        try {
            // Check if product is used in any catalog compositions
            $usedInCompositions = \App\Models\CatalogProductComposition::where('component_id', $product->id)->exists();
            
            if ($usedInCompositions) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete product. It is being used in product compositions.'
                ], 400);
            }
            
            // Check if request is from clerk (mark/unmark for deletion) or admin (hard delete)
            if (auth()->user()->role === 'clerk') {
                // Clerk toggle mark for deletion - add/remove red border for admin oversight
                $isCurrentlyMarked = $product->is_marked_for_deletion;
                
                $product->update([
                    'is_marked_for_deletion' => !$isCurrentlyMarked,
                    'marked_for_deletion_by' => !$isCurrentlyMarked ? auth()->id() : null,
                    'marked_for_deletion_at' => !$isCurrentlyMarked ? now() : null
                ]);
                
                $message = !$isCurrentlyMarked ? 
                    'Product marked for deletion! Admin will review.' : 
                    'Product unmarked for deletion.';

                // Also persist a pending record in inventory_logs (and pending_inventory_changes) so it appears in Admin Reports
                try {
                    if (!$isCurrentlyMarked) {
                        // Marking for deletion → create pending log and pending change
                        $oldValues = [
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
                        ];
                        $log = \App\Models\InventoryLog::create([
                            'product_id' => $product->id,
                            'user_id' => auth()->id(),
                            'action' => 'delete',
                            'old_values' => $oldValues,
                            'new_values' => null,
                            'description' => 'Clerk marked product for deletion',
                            'ip_address' => request()->ip(),
                            'user_agent' => request()->userAgent(),
                            'status' => \Illuminate\Support\Facades\Schema::hasColumn('inventory_logs','status') ? 'pending' : null,
                        ]);
                        // Create parallel pending change record if table exists
                        if (\Illuminate\Support\Facades\Schema::hasTable('pending_inventory_changes')) {
                            \App\Models\PendingInventoryChange::create([
                                'product_id' => $product->id,
                                'action' => 'delete',
                                'changes' => null,
                                'submitted_by' => auth()->id(),
                                'status' => 'pending',
                            ]);
                        }
                    } else {
                        // Unmark → mark existing pending delete log as rejected (if any), and pending change too
                        if (\Illuminate\Support\Facades\Schema::hasColumn('inventory_logs','status')) {
                            \App\Models\InventoryLog::where('product_id', $product->id)
                                ->where('action','delete')
                                ->where('status','pending')
                                ->orderByDesc('created_at')
                                ->limit(1)
                                ->update(['status' => 'rejected']);
                        }
                        if (\Illuminate\Support\Facades\Schema::hasTable('pending_inventory_changes')) {
                            \App\Models\PendingInventoryChange::where('product_id', $product->id)
                                ->where('action','delete')
                                ->where('status','pending')
                                ->update(['status' => 'rejected', 'reviewed_by' => auth()->id(), 'reviewed_at' => now(), 'admin_notes' => 'Unmarked by clerk']);
                        }
                    }
                } catch (\Throwable $t) {
                    // swallow logging issues to not block UI toggle
                }
                
                return response()->json([
                    'success' => true,
                    'message' => $message
                ]);
            } else {
                // Admin hard delete
                $product->forceDelete();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Product deleted permanently!'
                ]);
            }
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting product: ' . $e->getMessage()
            ], 500);
        }
    }


    public function reviews($id)
    {
        try {
            \Log::info('Reviews endpoint called for ID: ' . $id);
            
            // First, try to find as CatalogProduct (since this is the customer context)
            $catalogProduct = \App\Models\CatalogProduct::find($id);
            $productId = $id;
            
            if ($catalogProduct) {
                \Log::info('Found CatalogProduct: ' . $catalogProduct->name);
                // Find corresponding Product with same name/price/category
                $product = Product::where('name', $catalogProduct->name)
                    ->where('price', $catalogProduct->price)
                    ->where('category', $catalogProduct->category)
                    ->first();
                if ($product) {
                    $productId = $product->id;
                    \Log::info('Found corresponding Product ID: ' . $productId . ' for ' . $product->name);
                } else {
                    \Log::info('No corresponding Product found for CatalogProduct');
                    return response()->json([
                        'reviews' => [],
                        'average_rating' => 0,
                        'total_reviews' => 0
                    ]);
                }
            } else {
                // Fallback: try to find as Product ID directly
                \Log::info('Not found as CatalogProduct, checking Product...');
                $product = Product::find($id);
                if ($product) {
                    \Log::info('Found as Product: ' . $product->name);
                    $productId = $id;
                } else {
                    \Log::info('Not found as Product either');
                    return response()->json([
                        'reviews' => [],
                        'average_rating' => 0,
                        'total_reviews' => 0
                    ]);
                }
            }
            
            // Get reviews from order_product pivot table using the resolved product ID
            $reviews = DB::table('order_product')
                ->join('orders', 'order_product.order_id', '=', 'orders.id')
                ->join('users', 'orders.user_id', '=', 'users.id')
                ->where('order_product.product_id', $productId)
                ->where('order_product.reviewed', true)
                ->whereNotNull('order_product.rating')
                ->select([
                    'users.name as user_name',
                    'order_product.rating',
                    'order_product.review_comment as comment',
                    'order_product.reviewed_at as created_at'
                ])
                ->orderBy('order_product.reviewed_at', 'desc')
                ->get();

            \Log::info('Reviews found: ' . $reviews->count());
            \Log::info('Reviews data: ' . json_encode($reviews->toArray()));

            // Calculate average rating
            $averageRating = $reviews->avg('rating') ?? 0;
            $totalReviews = $reviews->count();

            return response()->json([
                'reviews' => $reviews,
                'average_rating' => round($averageRating, 1),
                'total_reviews' => $totalReviews
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'reviews' => [],
                'average_rating' => 0,
                'total_reviews' => 0
            ]);
        }
    }
} 