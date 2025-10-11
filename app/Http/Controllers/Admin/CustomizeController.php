<?php

namespace App\Http\Controllers\Admin;

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
        
        return view('admin.customize.index', compact('items','categories'));
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
        $customizeItem->is_approved = true; // Admin can directly approve
        $customizeItem->status = true;
        $customizeItem->save();

        return back()->with('success','Item added successfully.');
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
            // Delete old image
            if ($customizeItem->image && \Storage::disk('public')->exists($customizeItem->image)) {
                \Storage::disk('public')->delete($customizeItem->image);
            }
            $path = $request->file('image')->store('customize', 'public');
            $customizeItem->image = $path;
        }

        $customizeItem->name = $validated['name'];
        $customizeItem->category = $validated['category'];
        $customizeItem->price = $validated['price'] ?? 0;
        $customizeItem->inventory_item_id = $validated['inventory_item_id'] ?? null;
        $customizeItem->save();

        return back()->with('success','Item updated successfully.');
    }

    public function destroy($id)
    {
        $customizeItem = CustomizeItem::findOrFail($id);
        
        // Delete image if exists
        if ($customizeItem->image && \Storage::disk('public')->exists($customizeItem->image)) {
            \Storage::disk('public')->delete($customizeItem->image);
        }
        
        $customizeItem->delete();
        return back()->with('success','Item deleted successfully.');
    }

    public function bulkDelete(Request $request)
    {
        try {
            // Debug: Log the request details
            \Log::info('Bulk delete request received', [
                'method' => $request->method(),
                'url' => $request->url(),
                'data' => $request->all(),
                'ids' => $request->input('ids', [])
            ]);

            $request->validate([
                'ids' => 'required|array|min:1',
                'ids.*' => 'integer|exists:customize_items,id'
            ]);

            $deletedCount = 0;
            foreach ($request->ids as $id) {
                $customizeItem = CustomizeItem::find($id);
                if ($customizeItem) {
                    if ($customizeItem->image && \Storage::disk('public')->exists($customizeItem->image)) { 
                        \Storage::disk('public')->delete($customizeItem->image); 
                    }
                    $customizeItem->delete();
                    $deletedCount++;
                }
            }

            \Log::info('Bulk delete completed', ['deleted_count' => $deletedCount]);

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => "Successfully deleted {$deletedCount} item(s)."]);
            }
            
            return redirect()->route('admin.customize.index')->with('success', "Successfully deleted {$deletedCount} item(s).");
            
        } catch (\Exception $e) {
            \Log::error('Bulk delete error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Error deleting items: ' . $e->getMessage()], 500);
            }
            
            return redirect()->route('admin.customize.index')->with('error', 'Error deleting items: ' . $e->getMessage());
        }
    }
}