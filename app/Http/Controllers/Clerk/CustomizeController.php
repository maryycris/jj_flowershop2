<?php

namespace App\Http\Controllers\Clerk;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class CustomizeController extends Controller
{
    public function index(Request $request)
    {
        $categories = ['Wrapper','Focal','Greeneries','Ribbons','Fillers'];
        $items = Product::whereIn('category', $categories)->orderBy('category')->orderBy('name')->get()->groupBy('category');
        return view('clerk.customize.index', compact('items','categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:Wrapper,Focal,Greeneries,Ribbons,Fillers',
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
            'category' => 'required|in:Wrapper,Focal,Greeneries,Ribbons,Fillers',
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
}


