<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PromotedBanner;

class PromotedBannerController extends Controller
{
    public function index()
    {
        return redirect('/admin/products');
    }

    public function create() {}

    public function store(Request $request)
    {
        $request->validate([
            'images' => 'required',
            'images.*' => 'image|max:4096',
            'link_url' => 'nullable|string|max:255',
        ]);

        $sortBase = (PromotedBanner::max('sort_order') ?? 0) + 1;
        $index = 0;
        foreach ($request->file('images', []) as $img) {
            if ($index >= 3) { break; }
            try {
                // Check what driver is being used
                $driver = config('filesystems.disks.public.driver');
                \Log::info('Attempting banner upload', [
                    'driver' => $driver,
                    'file_size' => $img->getSize(),
                    'file_name' => $img->getClientOriginalName()
                ]);
                
                $path = $img->store('promoted_banners', 'public');
                
                // Check if path is a Cloudinary URL (starts with http)
                $isCloudinary = filter_var($path, FILTER_VALIDATE_URL) !== false;
                
                PromotedBanner::create([
                    'image' => $path,
                    'title' => null,
                    'link_url' => $request->input('link_url'),
                    'is_active' => true,
                    'sort_order' => $sortBase + $index,
                ]);
                $index++;
                \Log::info('Banner uploaded successfully', [
                    'path' => $path,
                    'driver' => $driver,
                    'is_cloudinary' => $isCloudinary,
                    'note' => $isCloudinary ? 'PERMANENT - Stored in Cloudinary' : 'TEMPORARY - Stored locally (will be lost on deployment)'
                ]);
            } catch (\Exception $e) {
                \Log::error('Failed to upload banner image', [
                    'error' => $e->getMessage(),
                    'error_class' => get_class($e),
                    'trace' => $e->getTraceAsString()
                ]);
                
                // If Cloudinary error, try falling back to local storage
                if (strpos($e->getMessage(), 'Invalid configuration') !== false || 
                    strpos($e->getMessage(), 'Cloudinary') !== false) {
                    try {
                        \Log::warning('Cloudinary failed for banner, attempting local storage fallback');
                        $path = $img->store('promoted_banners', 'local');
                        PromotedBanner::create([
                            'image' => $path,
                            'title' => null,
                            'link_url' => $request->input('link_url'),
                            'is_active' => true,
                            'sort_order' => $sortBase + $index,
                        ]);
                        $index++;
                        \Log::info('Banner uploaded to local storage as fallback', ['path' => $path]);
                    } catch (\Exception $fallbackError) {
                        \Log::error('Fallback to local storage also failed for banner', [
                            'error' => $fallbackError->getMessage()
                        ]);
                        return back()->withErrors(['images' => 'Failed to upload banner: ' . $fallbackError->getMessage()]);
                    }
                } else {
                    return back()->withErrors(['images' => 'Failed to upload banner: ' . $e->getMessage()]);
                }
            }
        }

        return back()->with('success','Banners added');
    }

    public function show(string $id) {}

    public function edit(string $id) {}

    public function update(Request $request, string $id)
    {
        $banner = PromotedBanner::findOrFail($id);
        $data = $request->validate([
            'title' => 'nullable|string|max:255',
            'link_url' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean'
        ]);
        if ($request->hasFile('image')) {
            try {
                // Delete old image if exists
                if ($banner->image) {
                    try {
                        \Storage::disk('public')->delete($banner->image);
                    } catch (\Exception $e) {
                        \Log::warning('Failed to delete old banner image (non-critical)', ['error' => $e->getMessage()]);
                    }
                }
                
                $driver = config('filesystems.disks.public.driver');
                \Log::info('Attempting banner update upload', ['driver' => $driver]);
                
                $data['image'] = $request->file('image')->store('promoted_banners','public');
                
                $isCloudinary = filter_var($data['image'], FILTER_VALIDATE_URL) !== false;
                \Log::info('Banner image updated successfully', [
                    'path' => $data['image'],
                    'driver' => $driver,
                    'is_cloudinary' => $isCloudinary,
                    'note' => $isCloudinary ? 'PERMANENT - Stored in Cloudinary' : 'TEMPORARY - Stored locally'
                ]);
            } catch (\Exception $e) {
                \Log::error('Failed to upload banner image during update', [
                    'error' => $e->getMessage(),
                    'error_class' => get_class($e)
                ]);
                
                // If Cloudinary error, try falling back to local storage
                if (strpos($e->getMessage(), 'Invalid configuration') !== false || 
                    strpos($e->getMessage(), 'Cloudinary') !== false) {
                    try {
                        \Log::warning('Cloudinary failed for banner update, attempting local storage fallback');
                        $data['image'] = $request->file('image')->store('promoted_banners','local');
                        \Log::info('Banner image updated to local storage as fallback', ['path' => $data['image']]);
                    } catch (\Exception $fallbackError) {
                        \Log::error('Fallback to local storage also failed for banner update', [
                            'error' => $fallbackError->getMessage()
                        ]);
                        return back()->withErrors(['image' => 'Failed to upload banner: ' . $fallbackError->getMessage()]);
                    }
                } else {
                    return back()->withErrors(['image' => 'Failed to upload banner: ' . $e->getMessage()]);
                }
            }
        }
        $banner->update($data);
        return back()->with('success','Banner updated');
    }

    public function destroy(string $id)
    {
        $banner = PromotedBanner::findOrFail($id);
        
        // Delete the image file
        if ($banner->image) {
            try {
                \Storage::disk('public')->delete($banner->image);
                \Log::info('Banner image deleted', ['path' => $banner->image]);
            } catch (\Exception $e) {
                \Log::warning('Failed to delete banner image (non-critical)', [
                    'path' => $banner->image,
                    'error' => $e->getMessage()
                ]);
                // Continue with deletion even if image delete fails
            }
        }
        
        $banner->delete();
        
        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Banner deleted']);
        }
        
        return back()->with('success','Banner deleted');
    }
}
