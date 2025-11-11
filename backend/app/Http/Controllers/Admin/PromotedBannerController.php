<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PromotedBanner;

class PromotedBannerController extends Controller
{
    /**
     * Extract public_id from Cloudinary URL
     */
    private function extractPublicIdFromCloudinaryUrl($url)
    {
        if (!str_contains($url, 'cloudinary.com')) {
            return null;
        }
        
        $urlParts = parse_url($url);
        $path = trim($urlParts['path'] ?? '', '/');
        
        $uploadPos = strpos($path, '/image/upload/');
        if ($uploadPos === false) {
            $uploadPos = strpos($path, 'image/upload/');
            if ($uploadPos === false) {
                return null;
            }
            $publicId = substr($path, $uploadPos + strlen('image/upload/'));
        } else {
            $publicId = substr($path, $uploadPos + strlen('/image/upload/'));
        }
        
        return preg_replace('/\.(png|jpg|jpeg|gif|webp)$/i', '', $publicId);
    }

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
            
            // Check if Cloudinary is configured
            $cloudName = env('CLOUDINARY_CLOUD_NAME');
            $apiKey = env('CLOUDINARY_API_KEY');
            $apiSecret = env('CLOUDINARY_API_SECRET');
            
            if ($cloudName && $apiKey && $apiSecret) {
                // Use Cloudinary API directly
                try {
                    \Log::info('Uploading banner directly to Cloudinary using API', [
                        'file_path' => $img->getPathname(),
                        'file_valid' => $img->isValid()
                    ]);
                    
                    $cloudinary = new \Cloudinary\Cloudinary([
                        'cloud' => [
                            'cloud_name' => $cloudName,
                            'api_key' => $apiKey,
                            'api_secret' => $apiSecret,
                        ],
                        'url' => [
                            'secure' => true,
                        ],
                    ]);
                    
                    $filePath = $img->getPathname();
                    
                    // Upload to Cloudinary with folder
                    $uploadResult = $cloudinary->uploadApi()->upload(
                        $filePath,
                        [
                            'folder' => 'promoted_banners',
                            'resource_type' => 'image',
                        ]
                    );
                    
                    // Get the secure URL from the upload result
                    $fullUrl = $uploadResult['secure_url'];
                    
                    PromotedBanner::create([
                        'image' => $fullUrl,
                        'title' => null,
                        'link_url' => $request->input('link_url'),
                        'is_active' => true,
                        'sort_order' => $sortBase + $index,
                    ]);
                    $index++;
                    \Log::info('Banner uploaded successfully to Cloudinary (PERMANENT)', [
                        'url' => $fullUrl,
                        'note' => 'This image will persist across all deployments'
                    ]);
                } catch (\Exception $cloudinaryError) {
                    \Log::error('Direct Cloudinary API upload failed for banner', [
                        'error' => $cloudinaryError->getMessage(),
                        'error_class' => get_class($cloudinaryError),
                        'trace' => $cloudinaryError->getTraceAsString()
                    ]);
                    return back()->withErrors(['images' => 'Failed to upload banner to Cloudinary: ' . $cloudinaryError->getMessage()]);
                }
            } else {
                // Cloudinary not configured, use local disk
                try {
                    $path = $img->store('promoted_banners', 'local');
                    PromotedBanner::create([
                        'image' => $path,
                        'title' => null,
                        'link_url' => $request->input('link_url'),
                        'is_active' => true,
                        'sort_order' => $sortBase + $index,
                    ]);
                    $index++;
                    \Log::info('Banner uploaded to local storage (Cloudinary not configured)', ['path' => $path]);
                } catch (\Exception $e) {
                    \Log::error('Failed to upload banner to local storage', [
                        'error' => $e->getMessage()
                    ]);
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
            // Delete old image if exists
            if ($banner->image) {
                $cloudName = env('CLOUDINARY_CLOUD_NAME');
                $apiKey = env('CLOUDINARY_API_KEY');
                $apiSecret = env('CLOUDINARY_API_SECRET');
                
                if ($cloudName && $apiKey && $apiSecret && str_contains($banner->image, 'cloudinary.com')) {
                    try {
                        $cloudinary = new \Cloudinary\Cloudinary([
                            'cloud' => [
                                'cloud_name' => $cloudName,
                                'api_key' => $apiKey,
                                'api_secret' => $apiSecret,
                            ],
                            'url' => ['secure' => true],
                        ]);
                        
                        // Extract public_id from URL
                        $publicId = $this->extractPublicIdFromCloudinaryUrl($banner->image);
                        if ($publicId) {
                            $cloudinary->uploadApi()->destroy($publicId, ['resource_type' => 'image']);
                            \Log::info('Old banner image deleted from Cloudinary', ['public_id' => $publicId]);
                        }
                    } catch (\Exception $e) {
                        \Log::warning('Failed to delete old banner image from Cloudinary (non-critical)', [
                            'error' => $e->getMessage()
                        ]);
                    }
                } else {
                    // Local storage - try to delete using file system directly
                    $fullPath = storage_path('app/public/' . $banner->image);
                    if (file_exists($fullPath)) {
                        unlink($fullPath);
                        \Log::info('Old banner image deleted from local storage', ['path' => $banner->image]);
                    }
                }
            }
            
            // Upload new image using direct Cloudinary API
            $cloudName = env('CLOUDINARY_CLOUD_NAME');
            $apiKey = env('CLOUDINARY_API_KEY');
            $apiSecret = env('CLOUDINARY_API_SECRET');
            
            if ($cloudName && $apiKey && $apiSecret) {
                try {
                    $cloudinary = new \Cloudinary\Cloudinary([
                        'cloud' => [
                            'cloud_name' => $cloudName,
                            'api_key' => $apiKey,
                            'api_secret' => $apiSecret,
                        ],
                        'url' => ['secure' => true],
                    ]);
                    
                    $filePath = $request->file('image')->getPathname();
                    
                    $uploadResult = $cloudinary->uploadApi()->upload(
                        $filePath,
                        [
                            'folder' => 'promoted_banners',
                            'resource_type' => 'image',
                        ]
                    );
                    
                    $data['image'] = $uploadResult['secure_url'];
                    \Log::info('Banner image updated to Cloudinary', ['url' => $data['image']]);
                } catch (\Exception $e) {
                    \Log::error('Failed to upload banner image to Cloudinary during update', [
                        'error' => $e->getMessage(),
                        'error_class' => get_class($e)
                    ]);
                    return back()->withErrors(['image' => 'Failed to upload banner: ' . $e->getMessage()]);
                }
            } else {
                // Cloudinary not configured, use local disk
                $data['image'] = $request->file('image')->store('promoted_banners', 'local');
                \Log::info('Banner image updated to local storage (Cloudinary not configured)', ['path' => $data['image']]);
            }
        }
        $banner->update($data);
        return back()->with('success','Banner updated');
    }

    public function destroy(string $id)
    {
        try {
            $banner = PromotedBanner::findOrFail($id);
            
            // Delete the image file from Cloudinary or local storage
            if ($banner->image) {
                $cloudName = env('CLOUDINARY_CLOUD_NAME');
                $apiKey = env('CLOUDINARY_API_KEY');
                $apiSecret = env('CLOUDINARY_API_SECRET');
                
                if ($cloudName && $apiKey && $apiSecret && str_contains($banner->image, 'cloudinary.com')) {
                    try {
                        $cloudinary = new \Cloudinary\Cloudinary([
                            'cloud' => [
                                'cloud_name' => $cloudName,
                                'api_key' => $apiKey,
                                'api_secret' => $apiSecret,
                            ],
                            'url' => ['secure' => true],
                        ]);
                        
                        // Extract public_id from URL
                        $publicId = $this->extractPublicIdFromCloudinaryUrl($banner->image);
                        if ($publicId) {
                            $cloudinary->uploadApi()->destroy($publicId, ['resource_type' => 'image']);
                            \Log::info('Banner image deleted from Cloudinary', ['public_id' => $publicId]);
                        }
                    } catch (\Exception $e) {
                        \Log::warning('Failed to delete banner image from Cloudinary (non-critical)', [
                            'path' => $banner->image,
                            'error' => $e->getMessage()
                        ]);
                    }
                } else {
                    // Local storage - try to delete using file system directly
                    $fullPath = storage_path('app/public/' . $banner->image);
                    if (file_exists($fullPath)) {
                        unlink($fullPath);
                        \Log::info('Banner image deleted from local storage', ['path' => $banner->image]);
                    }
                }
            }
            
            $banner->delete();
            
            // Check if request is AJAX/JSON (multiple ways to detect)
            if (request()->expectsJson() || request()->wantsJson() || request()->ajax() || request()->header('X-Requested-With') === 'XMLHttpRequest' || request()->header('Content-Type') === 'application/json') {
                return response()->json([
                    'success' => true, 
                    'message' => 'Banner deleted successfully'
                ]);
            }
            
            return back()->with('success','Banner deleted');
        } catch (\Exception $e) {
            \Log::error('Error deleting banner', [
                'banner_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Check if request is AJAX/JSON
            if (request()->expectsJson() || request()->wantsJson() || request()->ajax() || request()->header('X-Requested-With') === 'XMLHttpRequest' || request()->header('Content-Type') === 'application/json') {
                return response()->json([
                    'success' => false,
                    'message' => 'Error deleting banner: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->withErrors(['error' => 'Error deleting banner: ' . $e->getMessage()]);
        }
    }
}
