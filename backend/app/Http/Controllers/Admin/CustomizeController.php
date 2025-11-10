<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomizeItem;
use App\Models\Product;
use App\Models\Setting;
use App\Traits\CustomizeFilterTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class CustomizeController extends Controller
{
    use CustomizeFilterTrait;
    
    public function index(Request $request)
    {
        // Admin can see all items (approved and unapproved) for management
        $items = $this->getCustomizeItems(false);
        $categories = $this->getCustomizeCategories();
        $assemblingFee = Setting::get('assembling_fee', 150);
        
        // Log Cloudinary status for debugging
        $cloudinaryConfigured = !empty(env('CLOUDINARY_CLOUD_NAME')) && 
                               !empty(env('CLOUDINARY_API_KEY')) && 
                               !empty(env('CLOUDINARY_API_SECRET'));
        Log::info('Customize page loaded', [
            'cloudinary_configured' => $cloudinaryConfigured,
            'items_count' => $items->flatten()->count(),
            'storage_driver' => config('filesystems.disks.public.driver')
        ]);
        
        return view('admin.customize.index', compact('items','categories', 'assemblingFee'));
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

        try {
            // Check Cloudinary configuration
            $cloudinaryConfigured = !empty(env('CLOUDINARY_CLOUD_NAME')) && 
                                   !empty(env('CLOUDINARY_API_KEY')) && 
                                   !empty(env('CLOUDINARY_API_SECRET'));
            
            Log::info('Uploading customize item image', [
                'cloudinary_configured' => $cloudinaryConfigured,
                'storage_driver' => config('filesystems.disks.public.driver'),
                'file_name' => $request->file('image')->getClientOriginalName()
            ]);
            
            // Store image - will use Cloudinary if configured, otherwise local
            $path = $request->file('image')->store('customize', 'public');
            
            // If using Cloudinary, get the full URL
            if ($cloudinaryConfigured && config('filesystems.disks.public.driver') === 'cloudinary') {
                $imageUrl = Storage::disk('public')->url($path);
                Log::info('Image uploaded to Cloudinary', [
                    'path' => $path,
                    'url' => $imageUrl
                ]);
            } else {
                Log::warning('Image uploaded to LOCAL storage (will be lost on deployment)', [
                    'path' => $path,
                    'note' => 'Configure Cloudinary to persist images'
                ]);
            }

            $customizeItem = new CustomizeItem();
            $customizeItem->name = $validated['name'];
            $customizeItem->category = $validated['category'];
            $customizeItem->price = $validated['price'] ?? 0;
            $customizeItem->image = $path;
            $customizeItem->inventory_item_id = $validated['inventory_item_id'] ?? null;
            $customizeItem->is_approved = true; // Admin can directly approve
            $customizeItem->status = true;
            $customizeItem->save();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Item added successfully.',
                    'item' => $customizeItem,
                    'cloudinary_configured' => $cloudinaryConfigured
                ]);
            }

            return back()->with('success','Item added successfully.');
        } catch (\Exception $e) {
            Log::error('Error uploading customize item image', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error uploading image: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->withErrors(['error' => 'Error uploading image. Please try again.']);
        }
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
            try {
                // Delete old image
                if ($customizeItem->image && \Storage::disk('public')->exists($customizeItem->image)) {
                    \Storage::disk('public')->delete($customizeItem->image);
                }
                $path = $request->file('image')->store('customize', 'public');
                $customizeItem->image = $path;
                
                Log::info('Customize item image updated', [
                    'item_id' => $id,
                    'path' => $path,
                    'cloudinary_configured' => !empty(env('CLOUDINARY_CLOUD_NAME'))
                ]);
            } catch (\Exception $e) {
                Log::error('Error updating customize item image', [
                    'item_id' => $id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        $customizeItem->name = $validated['name'];
        $customizeItem->category = $validated['category'];
        $customizeItem->price = $validated['price'] ?? 0;
        $customizeItem->inventory_item_id = $validated['inventory_item_id'] ?? null;
        $customizeItem->save();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Item updated successfully.',
                'item' => $customizeItem
            ]);
        }

        return back()->with('success','Item updated successfully.');
    }

    public function destroy(Request $request, $id)
    {
        $customizeItem = CustomizeItem::findOrFail($id);
        
        try {
            if ($customizeItem->image && \Storage::disk('public')->exists($customizeItem->image)) {
                \Storage::disk('public')->delete($customizeItem->image);
            }
        } catch (\Exception $e) {
            Log::error('Error deleting customize item image', [
                'item_id' => $id,
                'error' => $e->getMessage()
            ]);
        }
        
        $customizeItem->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Item deleted successfully.'
            ]);
        }

        return back()->with('success','Item deleted successfully.');
    }

    public function toggleApproval(Request $request, $id)
    {
        $customizeItem = CustomizeItem::findOrFail($id);
        $customizeItem->is_approved = !$customizeItem->is_approved;
        $customizeItem->save();

        return back()->with('success', $customizeItem->is_approved ? 'Item approved.' : 'Item unapproved.');
    }

    public function updateAssemblingFee(Request $request)
    {
        $request->validate([
            'assembling_fee' => 'required|numeric|min:0'
        ]);

        Setting::set('assembling_fee', $request->assembling_fee);

        return back()->with('success', 'Assembling fee updated successfully.');
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        
        if (empty($ids)) {
            return response()->json([
                'success' => false,
                'message' => 'No items selected.'
            ], 400);
        }

        $deleted = 0;
        foreach ($ids as $id) {
            try {
                $customizeItem = CustomizeItem::find($id);
                if ($customizeItem) {
                    // Delete associated image
                    if ($customizeItem->image && \Storage::disk('public')->exists($customizeItem->image)) {
                        \Storage::disk('public')->delete($customizeItem->image);
                    }
                    $customizeItem->delete();
                    $deleted++;
                }
            } catch (\Exception $e) {
                Log::error('Error in bulk delete', [
                    'item_id' => $id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => "{$deleted} item(s) deleted successfully."
        ]);
    }
}
