<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\CustomizeFilterTrait;
use Illuminate\Http\Request;
use App\Models\Product;

class CustomizeController extends Controller
{
    use CustomizeFilterTrait;
    public function index(Request $request)
    {
        $items = $this->getCustomizeItems();
        $categories = $this->getCustomizeCategories();
        
        return view('admin.customize.index', compact('items','categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:Fresh Flowers,Dried Flowers,Artificial Flowers,Floral Supplies,Packaging Materials',
            'price' => 'nullable|numeric|min:0',
            'image' => 'required|image|max:4096',
            'description' => 'nullable|string|max:1000'
        ]);

        $path = $request->file('image')->store('customize', 'public');

        $product = new Product();
        $product->name = $validated['name'];
        $product->category = $validated['category'];
        $product->price = $validated['price'] ?? 0;
        $product->image = $path;
        $product->description = $validated['description'] ?? null;
        $product->is_approved = true; // Admin can directly approve
        $product->status = true;
        $product->save();

        return back()->with('success','Item added successfully.');
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:Fresh Flowers,Dried Flowers,Artificial Flowers,Floral Supplies,Packaging Materials',
            'price' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|max:4096',
            'description' => 'nullable|string|max:1000'
        ]);

        if ($request->hasFile('image')) {
            // Delete old image
            if ($product->image && \Storage::disk('public')->exists($product->image)) {
                \Storage::disk('public')->delete($product->image);
            }
            $path = $request->file('image')->store('customize', 'public');
            $product->image = $path;
        }

        $product->name = $validated['name'];
        $product->category = $validated['category'];
        $product->price = $validated['price'] ?? 0;
        $product->description = $validated['description'] ?? null;
        $product->save();

        return back()->with('success','Item updated successfully.');
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        
        // Delete image if exists
        if ($product->image && \Storage::disk('public')->exists($product->image)) {
            \Storage::disk('public')->delete($product->image);
        }
        
        $product->delete();
        return back()->with('success','Item deleted successfully.');
    }
}