<?php

namespace App\Http\Controllers\Clerk;

use App\Http\Controllers\Controller;
use App\Traits\CustomizeFilterTrait;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\CustomizeItem;

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
            'category' => 'required|in:Fresh Flowers,Artificial Flowers,Greenery,Ribbon,Wrappers',
            'price' => 'nullable|numeric|min:0',
            'image' => 'required|image|max:4096',
            'inventory_item_id' => 'nullable|exists:products,id'
        ]);

        $path = $request->file('image')->store('customize', 'public');

        $customizeItem = new CustomizeItem();
        $customizeItem->name = $validated['name'];
        $customizeItem->category = $validated['category'];
        $customizeItem->price = $validated['price'] ?? 0;
        $customizeItem->image = $path;
        $customizeItem->inventory_item_id = $validated['inventory_item_id'] ?? null;
        $customizeItem->is_approved = false; // Clerk needs admin approval
        $customizeItem->status = true;
        $customizeItem->save();

        return back()->with('success','Item added.');
    }

    public function update(Request $request, $id)
    {
        $customizeItem = CustomizeItem::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:Fresh Flowers,Artificial Flowers,Greenery,Ribbon,Wrappers',
            'price' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|max:4096',
            'inventory_item_id' => 'nullable|exists:products,id'
        ]);

        if ($request->hasFile('image')) {
            if ($customizeItem->image) { \Storage::disk('public')->delete($customizeItem->image); }
            $customizeItem->image = $request->file('image')->store('customize','public');
        }

        $customizeItem->name = $validated['name'];
        $customizeItem->category = $validated['category'];
        $customizeItem->price = $validated['price'] ?? 0;
        $customizeItem->inventory_item_id = $validated['inventory_item_id'] ?? null;
        $customizeItem->save();

        return back()->with('success','Item updated.');
    }

    public function destroy($id)
    {
        $customizeItem = CustomizeItem::findOrFail($id);
        if ($customizeItem->image) { \Storage::disk('public')->delete($customizeItem->image); }
        $customizeItem->delete();
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


