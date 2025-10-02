<?php

namespace App\Http\Controllers\Clerk;

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
        
        return view('clerk.customize.index', compact('items','categories'));
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
        $product->image = $path; // ensure model has fillable or accessor used elsewhere
        $product->description = $validated['description'] ?? null;
        $product->save();

        return back()->with('success','Item added.');
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
            if ($product->image) { \Storage::disk('public')->delete($product->image); }
            $product->image = $request->file('image')->store('customize','public');
        }

        $product->name = $validated['name'];
        $product->category = $validated['category'];
        $product->price = $validated['price'] ?? 0;
        $product->description = $validated['description'] ?? null;
        $product->save();

        return back()->with('success','Item updated.');
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        if ($product->image) { \Storage::disk('public')->delete($product->image); }
        $product->delete();
        return back()->with('success','Item deleted.');
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:products,id'
        ]);

        $deletedCount = 0;
        foreach ($request->ids as $id) {
            $product = Product::find($id);
            if ($product) {
                if ($product->image) { 
                    \Storage::disk('public')->delete($product->image); 
                }
                $product->delete();
                $deletedCount++;
            }
        }

        return back()->with('success', "Successfully deleted {$deletedCount} item(s).");
    }
}


