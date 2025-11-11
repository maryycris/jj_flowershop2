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
    /**
     * Extract public_id from Cloudinary URL
     * Example: https://res.cloudinary.com/cloud/image/upload/catalog_products/abc123.png
     * Returns: catalog_products/abc123
     */
    private function extractPublicIdFromCloudinaryUrl($url)
    {
        if (!str_contains($url, 'cloudinary.com')) {
            return null;
        }
        
        $urlParts = parse_url($url);
        $path = trim($urlParts['path'] ?? '', '/');
        
        // Find the position of '/image/upload/' in the path
        $uploadPos = strpos($path, '/image/upload/');
        if ($uploadPos === false) {
            // Try alternative format
            $uploadPos = strpos($path, 'image/upload/');
            if ($uploadPos === false) {
                return null;
            }
            $publicId = substr($path, $uploadPos + strlen('image/upload/'));
        } else {
            $publicId = substr($path, $uploadPos + strlen('/image/upload/'));
        }
        
        // Remove file extension
        $publicId = preg_replace('/\.(png|jpg|jpeg|gif|webp)$/i', '', $publicId);
        
        return $publicId;
    }
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
                    // Delete old image if exists using direct Cloudinary API
                    if ($product->image) {
                        try {
                            $cloudName = env('CLOUDINARY_CLOUD_NAME');
                            $apiKey = env('CLOUDINARY_API_KEY');
                            $apiSecret = env('CLOUDINARY_API_SECRET');
                            
                            if (str_contains($product->image, 'cloudinary.com') && $cloudName && $apiKey && $apiSecret) {
                                $cloudinary = new \Cloudinary\Cloudinary([
                                    'cloud' => [
                                        'cloud_name' => $cloudName,
                                        'api_key' => $apiKey,
                                        'api_secret' => $apiSecret,
                                    ],
                                    'url' => ['secure' => true],
                                ]);
                                
                                $publicId = $this->extractPublicIdFromCloudinaryUrl($product->image);
                                if ($publicId) {
                                    $cloudinary->uploadApi()->destroy($publicId, ['resource_type' => 'image']);
                                } else {
                                    \Log::warning('Could not extract public_id from Cloudinary URL during approval', ['url' => $product->image]);
                                }
                            } else {
                                $fullPath = storage_path('app/public/' . $product->image);
                                if (file_exists($fullPath)) {
                                    unlink($fullPath);
                                }
                            }
                        } catch (\Exception $e) {
                            \Log::warning('Failed to delete old image during approval (non-critical)', [
                                'error' => $e->getMessage()
                            ]);
                        }
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
                    try {
                        $cloudName = env('CLOUDINARY_CLOUD_NAME');
                        $apiKey = env('CLOUDINARY_API_KEY');
                        $apiSecret = env('CLOUDINARY_API_SECRET');
                        
                        if (str_contains($product->image, 'cloudinary.com') && $cloudName && $apiKey && $apiSecret) {
                            $cloudinary = new \Cloudinary\Cloudinary([
                                'cloud' => [
                                    'cloud_name' => $cloudName,
                                    'api_key' => $apiKey,
                                    'api_secret' => $apiSecret,
                                ],
                                'url' => ['secure' => true],
                            ]);
                            
                            $publicId = $this->extractPublicIdFromCloudinaryUrl($product->image);
                            if ($publicId) {
                                $cloudinary->uploadApi()->destroy($publicId, ['resource_type' => 'image']);
                            } else {
                                \Log::warning('Could not extract public_id from Cloudinary URL during product deletion', ['url' => $product->image]);
                            }
                        } else {
                            $fullPath = storage_path('app/public/' . $product->image);
                            if (file_exists($fullPath)) {
                                unlink($fullPath);
                            }
                        }
                    } catch (\Exception $e) {
                        \Log::warning('Failed to delete image during product deletion (non-critical)', [
                            'error' => $e->getMessage()
                        ]);
                    }
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
                // Only validate composition fields if component_id is present (meaning it's a complete entry)
                'compositions.*.component_id' => 'nullable|integer|exists:products,id',
                'compositions.*.component_name' => 'nullable|string|max:255',
                'compositions.*.quantity' => 'nullable|numeric|min:1',
                'compositions.*.unit' => 'nullable|string|max:50',
            ]);
            
            // Filter out incomplete composition entries (where component_id is null)
            if (isset($validated['compositions']) && is_array($validated['compositions'])) {
                $validated['compositions'] = array_filter($validated['compositions'], function($comp) {
                    return !empty($comp['component_id']) && !empty($comp['component_name']);
                });
                // Re-index array after filtering
                $validated['compositions'] = array_values($validated['compositions']);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed:', $e->errors());
            if ($request->expectsJson() || $request->wantsJson() || $request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed: ' . implode(', ', array_map(function($errors) {
                        return implode(', ', $errors);
                    }, $e->errors())),
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
            $driver = config('filesystems.disks.public.driver');
            \Log::info('Attempting image upload', [
                'driver' => $driver,
                'file_size' => $request->file('image')->getSize(),
                'file_name' => $request->file('image')->getClientOriginalName()
            ]);
            
            try {
                // Check if Cloudinary is configured
                $cloudName = env('CLOUDINARY_CLOUD_NAME');
                $apiKey = env('CLOUDINARY_API_KEY');
                $apiSecret = env('CLOUDINARY_API_SECRET');
                
                if ($driver === 'cloudinary' && $cloudName && $apiKey && $apiSecret) {
                    // Use Cloudinary API directly to bypass Storage facade issues
                    \Log::info('Uploading directly to Cloudinary using API', [
                        'file_path' => $request->file('image')->getPathname(),
                        'file_valid' => $request->file('image')->isValid()
                    ]);
                    
                    try {
                        $cloudinary = new \Cloudinary\Cloudinary([
                            'cloud' => [
                                'cloud_name' => $cloudName,
                                'api_key' => $apiKey,
                                'api_secret' => $apiSecret,
                            ],
                            'url' => [
                                'secure' => true,
                            ],
                        ]);
                        
                        // Use getPathname() instead of getRealPath() for uploaded files
                        $filePath = $request->file('image')->getPathname();
                        
                        // Upload to Cloudinary with folder
                        $uploadResult = $cloudinary->uploadApi()->upload(
                            $filePath,
                            [
                                'folder' => 'catalog_products',
                                'resource_type' => 'image',
                            ]
                        );
                        
                        // Get the secure URL from the upload result
                        $fullUrl = $uploadResult['secure_url'];
                        $publicId = $uploadResult['public_id'];
                        
                        // Store the full Cloudinary URL
                        $productData['image'] = $fullUrl;
                        
                        \Log::info('Image uploaded successfully to Cloudinary (PERMANENT)', [
                            'public_id' => $publicId,
                            'full_url' => $fullUrl,
                            'driver' => $driver,
                            'note' => 'This image will persist across all deployments'
                        ]);
                    } catch (\Exception $cloudinaryError) {
                        \Log::error('Direct Cloudinary API upload failed', [
                            'error' => $cloudinaryError->getMessage(),
                            'error_class' => get_class($cloudinaryError),
                            'trace' => $cloudinaryError->getTraceAsString()
                        ]);
                        throw $cloudinaryError; // Re-throw to be caught by outer catch
                    }
                } else {
                    // Fallback to Storage facade (for local or if Cloudinary not configured)
                    $imagePath = $request->file('image')->store('catalog_products', 'public');
                    $productData['image'] = $imagePath;
                    \Log::info('Image uploaded successfully to local storage', [
                        'path' => $imagePath,
                        'driver' => $driver
                    ]);
                }
            } catch (\Exception $e) {
                $errorMessage = $e->getMessage();
                $errorClass = get_class($e);
                
                \Log::error('Failed to upload image to Cloudinary', [
                    'error' => $errorMessage,
                    'error_class' => $errorClass,
                    'driver' => $driver,
                    'file_name' => $request->file('image')->getClientOriginalName(),
                    'file_size' => $request->file('image')->getSize(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                // Check if it's a Cloudinary-related error
                $isCloudinaryError = strpos(strtolower($errorMessage), 'cloudinary') !== false || 
                                    strpos(strtolower($errorMessage), 'invalid configuration') !== false ||
                                    strpos(strtolower($errorMessage), 'authentication') !== false ||
                                    $errorClass === 'Cloudinary\Api\ApiError';
                
                if ($isCloudinaryError) {
                    \Log::warning('Cloudinary upload failed, attempting local storage fallback (TEMPORARY)', [
                        'original_error' => $errorMessage
                    ]);
                    
                    try {
                        // Force use of local disk as fallback
                        $productData['image'] = $request->file('image')->store('catalog_products', 'local');
                        \Log::info('Image uploaded to local storage as fallback (will be lost on deployment)', [
                            'path' => $productData['image']
                        ]);
                    } catch (\Exception $fallbackError) {
                        \Log::error('Fallback to local storage also failed', [
                            'error' => $fallbackError->getMessage(),
                            'error_class' => get_class($fallbackError)
                        ]);
                        if ($request->expectsJson() || $request->wantsJson() || $request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                            return response()->json([
                                'success' => false,
                                'message' => 'Failed to upload image. Cloudinary error: ' . $errorMessage . '. Local fallback also failed: ' . $fallbackError->getMessage()
                            ], 500);
                        }
                        return redirect()->back()->withErrors(['image' => 'Failed to upload image: ' . $fallbackError->getMessage()])->withInput();
                    }
                } else {
                    // Non-Cloudinary error (file size, validation, etc.)
                    if ($request->expectsJson() || $request->wantsJson() || $request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                        return response()->json([
                            'success' => false,
                            'message' => 'Failed to upload image: ' . $errorMessage
                        ], 500);
                    }
                    return redirect()->back()->withErrors(['image' => 'Failed to upload image: ' . $errorMessage])->withInput();
                }
            }
        }

        try {
            $catalogProduct = CatalogProduct::create($productData);
            \Log::info('Catalog product created:', ['id' => $catalogProduct->id, 'name' => $catalogProduct->name]);

            // Save product compositions (materials from inventory)
            // Use validated compositions (already filtered to remove incomplete entries)
            if (!empty($validated['compositions']) && is_array($validated['compositions'])) {
                foreach ($validated['compositions'] as $composition) {
                    if (!empty($composition['component_id']) && !empty($composition['component_name']) && !empty($composition['quantity'])) {
                        $catalogProduct->compositions()->create([
                            'component_id' => $composition['component_id'],
                            'component_name' => $composition['component_name'],
                            'quantity' => $composition['quantity'],
                            'unit' => $composition['unit'] ?? 'pieces',
                            'description' => $composition['description'] ?? null,
                        ]);
                    }
                }
                \Log::info('Compositions saved for product:', [
                    'product_id' => $catalogProduct->id, 
                    'compositions_count' => count($validated['compositions'])
                ]);
            } else {
                \Log::info('No compositions to save for product:', ['product_id' => $catalogProduct->id]);
            }

            // Check if request is AJAX/JSON (multiple ways to detect)
            if ($request->expectsJson() || $request->wantsJson() || $request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => true,
                    'message' => 'Product added successfully to catalog.',
                    'product' => $catalogProduct,
                    'image_url' => $catalogProduct->image_url ?? null
                ]);
            }

            return Redirect::route('admin.products.index')->with('success', 'Product added successfully to catalog.');
        } catch (\Exception $e) {
            \Log::error('Error creating product:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->expectsJson() || $request->wantsJson() || $request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
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
            $driver = config('filesystems.disks.public.driver');
            \Log::info('Attempting image update', [
                'driver' => $driver,
                'file_size' => $request->file('image')->getSize(),
                'file_name' => $request->file('image')->getClientOriginalName()
            ]);
            
            try {
                // Delete old image if exists (only if it's a Cloudinary URL, extract public_id)
                if ($product->image) {
                    try {
                        // If it's a Cloudinary URL, extract public_id and delete
                        if (str_contains($product->image, 'cloudinary.com')) {
                            $cloudName = env('CLOUDINARY_CLOUD_NAME');
                            $apiKey = env('CLOUDINARY_API_KEY');
                            $apiSecret = env('CLOUDINARY_API_SECRET');
                            
                            if ($cloudName && $apiKey && $apiSecret) {
                                $cloudinary = new \Cloudinary\Cloudinary([
                                    'cloud' => [
                                        'cloud_name' => $cloudName,
                                        'api_key' => $apiKey,
                                        'api_secret' => $apiSecret,
                                    ],
                                    'url' => ['secure' => true],
                                ]);
                                
                                // Extract public_id from URL
                                $publicId = $this->extractPublicIdFromCloudinaryUrl($product->image);
                                if ($publicId) {
                                    $cloudinary->uploadApi()->destroy($publicId, ['resource_type' => 'image']);
                                    \Log::info('Old image deleted from Cloudinary', ['public_id' => $publicId]);
                                } else {
                                    \Log::warning('Could not extract public_id from Cloudinary URL during update', ['url' => $product->image]);
                                }
                            } else {
                                // Local storage - try to delete using file system directly
                                $fullPath = storage_path('app/public/' . $product->image);
                                if (file_exists($fullPath)) {
                                    unlink($fullPath);
                                    \Log::info('Old image deleted from local storage', ['path' => $product->image]);
                                }
                            }
                        }
                    } catch (\Exception $e) {
                        \Log::warning('Failed to delete old image (non-critical)', [
                            'path' => $product->image,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
                
                // Check if Cloudinary is configured
                $cloudName = env('CLOUDINARY_CLOUD_NAME');
                $apiKey = env('CLOUDINARY_API_KEY');
                $apiSecret = env('CLOUDINARY_API_SECRET');
                
                if ($driver === 'cloudinary' && $cloudName && $apiKey && $apiSecret) {
                    // Use Cloudinary API directly to bypass Storage facade issues
                    \Log::info('Uploading directly to Cloudinary using API (update)', [
                        'file_path' => $request->file('image')->getPathname(),
                        'file_valid' => $request->file('image')->isValid()
                    ]);
                    
                    try {
                        $cloudinary = new \Cloudinary\Cloudinary([
                            'cloud' => [
                                'cloud_name' => $cloudName,
                                'api_key' => $apiKey,
                                'api_secret' => $apiSecret,
                            ],
                            'url' => [
                                'secure' => true,
                            ],
                        ]);
                        
                        // Use getPathname() instead of getRealPath() for uploaded files
                        $filePath = $request->file('image')->getPathname();
                        
                        // Upload to Cloudinary with folder
                        $uploadResult = $cloudinary->uploadApi()->upload(
                            $filePath,
                            [
                                'folder' => 'catalog_products',
                                'resource_type' => 'image',
                            ]
                        );
                        
                        // Get the secure URL from the upload result
                        $fullUrl = $uploadResult['secure_url'];
                        $publicId = $uploadResult['public_id'];
                        
                        // Store the full Cloudinary URL
                        $validated['image'] = $fullUrl;
                        
                        \Log::info('New image uploaded successfully to Cloudinary (PERMANENT)', [
                            'public_id' => $publicId,
                            'full_url' => $fullUrl,
                            'driver' => $driver,
                            'note' => 'This image will persist across all deployments'
                        ]);
                    } catch (\Exception $cloudinaryError) {
                        \Log::error('Direct Cloudinary API upload failed during update', [
                            'error' => $cloudinaryError->getMessage(),
                            'error_class' => get_class($cloudinaryError),
                            'trace' => $cloudinaryError->getTraceAsString()
                        ]);
                        throw $cloudinaryError; // Re-throw to be caught by outer catch
                    }
                } else {
                    // Fallback to Storage facade (for local or if Cloudinary not configured)
                    $newImagePath = $request->file('image')->store('catalog_products', 'public');
                    $validated['image'] = $newImagePath;
                    \Log::info('New image uploaded successfully to local storage', [
                        'path' => $newImagePath,
                        'driver' => $driver
                    ]);
                }
            } catch (\Exception $e) {
                \Log::error('Failed to upload image to Cloudinary during update', [
                    'error' => $e->getMessage(),
                    'error_class' => get_class($e),
                    'driver' => $driver,
                    'trace' => $e->getTraceAsString()
                ]);
                
                // If Cloudinary error, try falling back to local storage
                if (strpos($e->getMessage(), 'Invalid configuration') !== false || 
                    strpos($e->getMessage(), 'Cloudinary') !== false ||
                    strpos($e->getMessage(), 'cloudinary') !== false) {
                    try {
                        \Log::warning('Cloudinary failed, attempting local storage fallback (TEMPORARY)');
                        $newImagePath = $request->file('image')->store('catalog_products', 'local');
                        $validated['image'] = $newImagePath;
                        \Log::info('Image uploaded to local storage as fallback (will be lost on deployment)', [
                            'path' => $newImagePath
                        ]);
                    } catch (\Exception $fallbackError) {
                        \Log::error('Fallback to local storage also failed', [
                            'error' => $fallbackError->getMessage(),
                            'error_class' => get_class($fallbackError)
                        ]);
                        if ($request->expectsJson()) {
                            return response()->json([
                                'success' => false,
                                'message' => 'Failed to upload image. Please check storage configuration. Error: ' . $fallbackError->getMessage()
                            ], 500);
                        }
                        return redirect()->back()->withErrors(['image' => 'Failed to upload image: ' . $fallbackError->getMessage()])->withInput();
                    }
                } else {
                    if ($request->expectsJson()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Failed to upload image: ' . $e->getMessage()
                        ], 500);
                    }
                    return redirect()->back()->withErrors(['image' => 'Failed to upload image: ' . $e->getMessage()])->withInput();
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

        // Check if request is AJAX/JSON (multiple ways to detect)
        if ($request->expectsJson() || $request->wantsJson() || $request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'success' => true,
                'message' => 'Product updated successfully.',
                'product' => $product,
                'image_url' => $product->image_url ?? null
            ]);
        }

        return Redirect::route('admin.products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Request $request, CatalogProduct $product)
    {
        try {
            // Delete all associated images from storage using direct Cloudinary API
            $cloudName = env('CLOUDINARY_CLOUD_NAME');
            $apiKey = env('CLOUDINARY_API_KEY');
            $apiSecret = env('CLOUDINARY_API_SECRET');
            
            $imagesToDelete = array_filter([$product->image, $product->image2, $product->image3]);
            
            if (!empty($imagesToDelete) && $cloudName && $apiKey && $apiSecret) {
                try {
                    $cloudinary = new \Cloudinary\Cloudinary([
                        'cloud' => [
                            'cloud_name' => $cloudName,
                            'api_key' => $apiKey,
                            'api_secret' => $apiSecret,
                        ],
                        'url' => ['secure' => true],
                    ]);
                    
                    foreach ($imagesToDelete as $image) {
                        if (!$image) continue;
                        
                        try {
                            // If it's a Cloudinary URL, extract public_id and delete
                            if (str_contains($image, 'cloudinary.com')) {
                                $publicId = $this->extractPublicIdFromCloudinaryUrl($image);
                                if ($publicId) {
                                    $cloudinary->uploadApi()->destroy($publicId, ['resource_type' => 'image']);
                                    \Log::info('Image deleted from Cloudinary (destroy)', ['public_id' => $publicId]);
                                } else {
                                    \Log::warning('Could not extract public_id from Cloudinary URL', ['url' => $image]);
                                }
                            } else {
                                // If it's a path (old format), try to delete from Cloudinary using path as public_id
                                // Remove file extension for public_id
                                $publicId = preg_replace('/\.(png|jpg|jpeg|gif|webp)$/i', '', $image);
                                
                                try {
                                    $cloudinary->uploadApi()->destroy($publicId, ['resource_type' => 'image']);
                                    \Log::info('Image deleted from Cloudinary using path as public_id (destroy)', [
                                        'path' => $image,
                                        'public_id' => $publicId
                                    ]);
                                } catch (\Exception $cloudinaryError) {
                                    // If Cloudinary delete fails, try local storage
                                    \Log::warning('Cloudinary delete failed, trying local storage', [
                                        'path' => $image,
                                        'error' => $cloudinaryError->getMessage()
                                    ]);
                                    $fullPath = storage_path('app/public/' . $image);
                                    if (file_exists($fullPath)) {
                                        unlink($fullPath);
                                        \Log::info('Image deleted from local storage (destroy)', ['path' => $image]);
                                    }
                                }
                            }
                        } catch (\Exception $e) {
                            \Log::warning('Failed to delete image (non-critical)', [
                                'image' => $image,
                                'error' => $e->getMessage()
                            ]);
                        }
                    }
                } catch (\Exception $e) {
                    \Log::warning('Failed to initialize Cloudinary for image deletion (non-critical)', [
                        'error' => $e->getMessage()
                    ]);
                }
            }

            $product->delete();

            if ($request->expectsJson() || $request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => true,
                    'message' => 'Product deleted successfully.'
                ]);
            }

            return Redirect::route('admin.products.index')->with('success', 'Product deleted successfully.');
        } catch (\Exception $e) {
            \Log::error('Error deleting product', [
                'product_id' => $product->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->expectsJson() || $request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete product: ' . $e->getMessage()
                ], 500);
            }
            
            return Redirect::route('admin.products.index')->withErrors(['error' => 'Failed to delete product: ' . $e->getMessage()]);
        }
    }

    public function updateImages(Request $request, CatalogProduct $product)
    {
        $request->validate([
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $driver = config('filesystems.disks.public.driver');
            
            // Delete old image if exists using direct Cloudinary API
            if ($product->image) {
                try {
                    $cloudName = env('CLOUDINARY_CLOUD_NAME');
                    $apiKey = env('CLOUDINARY_API_KEY');
                    $apiSecret = env('CLOUDINARY_API_SECRET');
                    
                    if (str_contains($product->image, 'cloudinary.com') && $cloudName && $apiKey && $apiSecret) {
                        $cloudinary = new \Cloudinary\Cloudinary([
                            'cloud' => [
                                'cloud_name' => $cloudName,
                                'api_key' => $apiKey,
                                'api_secret' => $apiSecret,
                            ],
                            'url' => ['secure' => true],
                        ]);
                        
                        $publicId = $this->extractPublicIdFromCloudinaryUrl($product->image);
                        if ($publicId) {
                            $cloudinary->uploadApi()->destroy($publicId, ['resource_type' => 'image']);
                        } else {
                            \Log::warning('Could not extract public_id from Cloudinary URL in updateImages', ['url' => $product->image]);
                        }
                    } else {
                        $fullPath = storage_path('app/public/' . $product->image);
                        if (file_exists($fullPath)) {
                            unlink($fullPath);
                        }
                    }
                } catch (\Exception $e) {
                    \Log::warning('Failed to delete old image (non-critical)', [
                        'path' => $product->image,
                        'error' => $e->getMessage()
                    ]);
                }
            }
            
            // Upload new image using direct Cloudinary API
            $cloudName = env('CLOUDINARY_CLOUD_NAME');
            $apiKey = env('CLOUDINARY_API_KEY');
            $apiSecret = env('CLOUDINARY_API_SECRET');
            
            if ($driver === 'cloudinary' && $cloudName && $apiKey && $apiSecret) {
                try {
                    $cloudinary = new \Cloudinary\Cloudinary([
                        'cloud' => [
                            'cloud_name' => $cloudName,
                            'api_key' => $apiKey,
                            'api_secret' => $apiSecret,
                        ],
                        'url' => ['secure' => true],
                    ]);
                    
                    $filePath = $request->file('image')->getPathname();
                    
                    $uploadResult = $cloudinary->uploadApi()->upload(
                        $filePath,
                        [
                            'folder' => 'catalog_products',
                            'resource_type' => 'image',
                        ]
                    );
                    
                    $product->image = $uploadResult['secure_url'];
                    \Log::info('Image uploaded to Cloudinary in updateImages', [
                        'url' => $product->image
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Failed to upload image to Cloudinary in updateImages', [
                        'error' => $e->getMessage()
                    ]);
                    // Fallback to local storage
                    $newImagePath = $request->file('image')->store('catalog_products', 'local');
                    $product->image = $newImagePath;
                }
            } else {
                $newImagePath = $request->file('image')->store('catalog_products', 'local');
                $product->image = $newImagePath;
            }
        }

        $product->save();

        return Redirect::back()->with('success', 'Product image updated successfully.');
    }

    public function deleteImage(Request $request, CatalogProduct $product)
    {
        try {
            if ($product->image) {
                // Delete image using direct Cloudinary API (bypass Storage facade)
                $cloudName = env('CLOUDINARY_CLOUD_NAME');
                $apiKey = env('CLOUDINARY_API_KEY');
                $apiSecret = env('CLOUDINARY_API_SECRET');
                
                try {
                    // If it's a Cloudinary URL, extract public_id and delete
                    if (str_contains($product->image, 'cloudinary.com') && $cloudName && $apiKey && $apiSecret) {
                        $cloudinary = new \Cloudinary\Cloudinary([
                            'cloud' => [
                                'cloud_name' => $cloudName,
                                'api_key' => $apiKey,
                                'api_secret' => $apiSecret,
                            ],
                            'url' => ['secure' => true],
                        ]);
                        
                        // Extract public_id from URL
                        $publicId = $this->extractPublicIdFromCloudinaryUrl($product->image);
                        if ($publicId) {
                            $cloudinary->uploadApi()->destroy($publicId, ['resource_type' => 'image']);
                            \Log::info('Image deleted from Cloudinary (deleteImage)', ['public_id' => $publicId]);
                        } else {
                            \Log::warning('Could not extract public_id from Cloudinary URL in deleteImage', ['url' => $product->image]);
                        }
                    } else {
                        // Local storage - try to delete using file system directly
                        $fullPath = storage_path('app/public/' . $product->image);
                        if (file_exists($fullPath)) {
                            unlink($fullPath);
                            \Log::info('Image deleted from local storage (deleteImage)', ['path' => $product->image]);
                        }
                    }
                } catch (\Exception $e) {
                    \Log::warning('Failed to delete image from storage (non-critical)', [
                        'path' => $product->image,
                        'error' => $e->getMessage()
                    ]);
                }
                
                // Clear image field in database
                $product->image = null;
                $product->save();
            }

            if ($request->expectsJson() || $request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => true,
                    'message' => 'Product image deleted successfully.'
                ]);
            }

            return Redirect::back()->with('success', 'Product image deleted successfully.');
        } catch (\Exception $e) {
            \Log::error('Error deleting product image', [
                'product_id' => $product->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->expectsJson() || $request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete image: ' . $e->getMessage()
                ], 500);
            }

            return Redirect::back()->withErrors(['error' => 'Failed to delete image: ' . $e->getMessage()]);
        }
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