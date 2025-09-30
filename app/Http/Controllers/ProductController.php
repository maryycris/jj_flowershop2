<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\CatalogProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;

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
        
        // Get approved products ordered by newest first
        $products = $query->orderBy('created_at', 'desc')->get();
        $categories = ['Bouquets', 'Packages', 'Gifts']; // Only these 3 categories for catalog
        $promotedProducts = CatalogProduct::where('is_approved', true)->orderBy('created_at', 'desc')->take(3)->get();

        return view('admin.products.index', compact('products', 'categories', 'promotedProducts'));
    }

    /**
     * Get available categories from inventory
     */
    public function getCategories()
    {
        $categories = Product::select('category')
            ->where('category', 'NOT LIKE', '%Office Supplies%')
            ->where('status', true)
            ->distinct()
            ->orderBy('category')
            ->pluck('category');
        
        return response()->json($categories);
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
            $productData['image'] = $request->file('image')->store('catalog_products', 'public');
        }

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

        return Redirect::route('admin.products.index')->with('success', 'Product added successfully to catalog.');
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
        ]);

        $product->update($validated);

        return Redirect::route('admin.products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(CatalogProduct $product)
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
        $excludeCategories = ['Office Supplies'];
        $products = Product::whereNotIn('category', $excludeCategories)->get();
        return view('admin.inventory', compact('products'));
    }

    public function storeInventory(Request $request)
    {
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

        Product::create([
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

        return redirect()->route('admin.inventory.index')->with('success', 'Product added successfully!');
    }

    public function updateInventory(Request $request, Product $product)
    {
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

        return redirect()->route('admin.inventory.index')->with('success', 'Product updated successfully!');
    }
} 