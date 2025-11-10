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
            $path = $img->store('promoted_banners', 'public');
            PromotedBanner::create([
                'image' => $path,
                'title' => null,
                'link_url' => $request->input('link_url'),
                'is_active' => true,
                'sort_order' => $sortBase + $index,
            ]);
            $index++;
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
            $data['image'] = $request->file('image')->store('promoted_banners','public');
        }
        $banner->update($data);
        return back()->with('success','Banner updated');
    }

    public function destroy(string $id)
    {
        $banner = PromotedBanner::findOrFail($id);
        
        // Delete the image file
        if ($banner->image && \Storage::disk('public')->exists($banner->image)) {
            \Storage::disk('public')->delete($banner->image);
        }
        
        $banner->delete();
        
        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Banner deleted']);
        }
        
        return back()->with('success','Banner deleted');
    }
}
