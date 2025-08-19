<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->has('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        $products = $query->latest()->get(); // Get all products or filtered produ,cts
        $categories = Product::select('category')->distinct()->pluck('category');
        $promotedProducts = Product::orderBy('created_at', 'desc')->take(3)->get();

        return view('admin.products.index', compact('products', 'categories', 'promotedProducts'));
    }

    public function show(Product $product)
    {
        return view('customer.products.show', compact('product'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'category' => 'required|string|max:255',
            'image' => 'required|image|max:2048', // Assuming primary image is required for creation
            'description' => 'nullable|string',
            'compositions' => 'nullable|array',
            'compositions.*.component_name' => 'required_with:compositions|string|max:255',
            'compositions.*.quantity' => 'required_with:compositions|numeric|min:1',
            'compositions.*.unit' => 'required_with:compositions|string|max:50',
        ]);

        $productData = [
            'name' => $validated['name'],
            'price' => $validated['price'],
            'category' => $validated['category'],
            'description' => $validated['description'] ?? null,
            'status' => true, // Default to active
        ];

        if ($request->hasFile('image')) {
            $productData['image'] = $request->file('image')->store('products', 'public');
        }

        // Set approval status based on user role
        $user = auth()->user();
        if ($user && $user->hasRole('admin')) {
            $productData['is_approved'] = true;
        } else {
            $productData['is_approved'] = false;
        }

        $product = Product::create($productData);

        // Save product compositions
        if ($request->has('compositions') && is_array($request->compositions)) {
            foreach ($request->compositions as $composition) {
                if (!empty($composition['component_name']) && !empty($composition['quantity'])) {
                    $product->compositions()->create([
                        'component_name' => $composition['component_name'],
                        'quantity' => $composition['quantity'],
                        'unit' => $composition['unit'],
                        'description' => $composition['description'] ?? null,
                    ]);
                }
            }
        }

        // Notify all admins if a clerk added the product
        if ($user && $user->hasRole('clerk')) {
            $admins = \App\Models\User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                $admin->notify(new \App\Notifications\ProductApprovalNotification($product, $user, 'added'));
            }
        }

        // Redirect to the correct page based on user role
        if (request()->routeIs('clerk.product_catalog.store')) {
            return Redirect::route('clerk.product_catalog.index')->with('success', 'Product added successfully.');
        }
        return Redirect::route('admin.products.index')->with('success', 'Product added successfully.');
    }

    public function edit(Product $product)
    {
        return view('admin.products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'category' => 'required|string|max:255',
            // Image fields are handled by updateImages method or are optional for direct update
        ]);

        $product->update($validated);

        return Redirect::route('admin.products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
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

    public function updateImages(Request $request, Product $product)
    {
        // Debug: Log what's being received
        \Log::info('UpdateImages called for product: ' . $product->id);
        \Log::info('Request has file: ' . ($request->hasFile('image') ? 'YES' : 'NO'));
        \Log::info('Request all data: ' . json_encode($request->all()));
        
        $request->validate([
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            \Log::info('Processing file upload...');
            // Delete old image if exists
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
                \Log::info('Deleted old image: ' . $product->image);
            }
            $newImagePath = $request->file('image')->store('products', 'public');
            $product->image = $newImagePath;
            \Log::info('New image saved to: ' . $newImagePath);
        } else {
            \Log::info('No file uploaded');
        }

        $product->save();
        \Log::info('Product saved successfully');

        return Redirect::back()->with('success', 'Product image updated successfully.');
    }

    public function deleteImage(Product $product)
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
        $products = Product::all();
        $totalProducts = $products->count();
        $lowStock = $products->where('stock', '<=', 10)->count();
        $wellStocked = $products->where('stock', '>', 10)->count();
        $categories = $products->unique('category')->count();

        $filter = $request->input('filter', 'all');
        $filteredProducts = $products;
        if ($filter === 'low_stock') {
            $filteredProducts = $products->where('stock', '<=', 10);
        } elseif ($filter === 'well_stocked') {
            $filteredProducts = $products->where('stock', '>', 10);
        } elseif ($filter === 'categories') {
            // Optionally, could filter by a specific category
            // For now, show all products
            $filteredProducts = $products;
        }

        return view('admin.inventory', [
            'products' => $filteredProducts,
            'totalProducts' => $totalProducts,
            'lowStock' => $lowStock,
            'wellStocked' => $wellStocked,
            'categories' => $categories,
        ]);
    }
} 