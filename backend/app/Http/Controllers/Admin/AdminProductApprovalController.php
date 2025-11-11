<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CatalogProduct;
use App\Models\ProductComposition;
use App\Services\ProductAvailabilityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminProductApprovalController extends Controller
{
    /**
     * Get products pending approval
     */
    public function getPendingProducts()
    {
        $pendingProducts = CatalogProduct::where('is_approved', false)
            ->with(['compositions'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($product) {
                // Ensure image_url is included in JSON response
                $product->setAppends(['image_url']);
                return $product;
            });

        return response()->json($pendingProducts);
    }

    /**
     * Get approved products
     */
    public function getApprovedProducts(Request $request)
    {
        $query = CatalogProduct::where('is_approved', true);
        
        // Filter by category
        if ($request->has('category') && $request->category !== 'all') {
            $categoryMapping = [
                'bouquets' => 'Bouquets',
                'packages' => 'Packages', 
                'gifts' => 'Gifts'
            ];
            
            if (isset($categoryMapping[$request->category])) {
                $query->where('category', $categoryMapping[$request->category]);
            }
        }
        
        $products = $query->with(['compositions'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($product) {
                // Ensure image_url is included in JSON response
                $product->setAppends(['image_url']);
                return $product;
            });

        // Check availability for all products
        $availabilityService = new ProductAvailabilityService();
        $productIds = $products->pluck('id')->toArray();
        $productAvailability = $availabilityService->getBulkCatalogAvailability($productIds);

        return response()->json([
            'products' => $products,
            'productAvailability' => $productAvailability
        ]);
    }

    /**
     * Approve a product
     */
    public function approveProduct(Request $request, $productId)
    {
        try {
            $product = CatalogProduct::findOrFail($productId);
            $product->is_approved = true;
            $product->approved_by = auth()->id();
            $product->approved_at = now();
            $product->save();

            return response()->json([
                'success' => true,
                'message' => 'Product approved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error approving product: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Disapprove a product (delete it)
     */
    public function disapproveProduct(Request $request, $productId)
    {
        try {
            $product = CatalogProduct::findOrFail($productId);
            
            // Delete associated images from Cloudinary or local storage
            $cloudName = env('CLOUDINARY_CLOUD_NAME');
            $apiKey = env('CLOUDINARY_API_KEY');
            $apiSecret = env('CLOUDINARY_API_SECRET');
            
            if ($cloudName && $apiKey && $apiSecret) {
                // Use Cloudinary API directly
                $cloudinary = new \Cloudinary\Cloudinary([
                    'cloud' => [
                        'cloud_name' => $cloudName,
                        'api_key' => $apiKey,
                        'api_secret' => $apiSecret,
                    ],
                    'url' => ['secure' => true],
                ]);
                
                // Helper function to extract public_id from URL
                $extractPublicId = function($url) {
                    if (!str_contains($url, 'cloudinary.com')) {
                        return null;
                    }
                    $urlParts = parse_url($url);
                    $path = trim($urlParts['path'] ?? '', '/');
                    $uploadPos = strpos($path, '/image/upload/');
                    if ($uploadPos === false) {
                        $uploadPos = strpos($path, 'image/upload/');
                        if ($uploadPos === false) return null;
                        $publicId = substr($path, $uploadPos + strlen('image/upload/'));
                    } else {
                        $publicId = substr($path, $uploadPos + strlen('/image/upload/'));
                    }
                    return preg_replace('/\.(png|jpg|jpeg|gif|webp)$/i', '', $publicId);
                };
                
                // Delete images from Cloudinary
                $images = [$product->image, $product->image2, $product->image3];
                foreach ($images as $image) {
                    if ($image) {
                        if (str_contains($image, 'cloudinary.com')) {
                            $publicId = $extractPublicId($image);
                            if ($publicId) {
                                try {
                                    $cloudinary->uploadApi()->destroy($publicId, ['resource_type' => 'image']);
                                    \Log::info('Image deleted from Cloudinary during disapproval', ['public_id' => $publicId]);
                                } catch (\Exception $e) {
                                    \Log::warning('Failed to delete image from Cloudinary (non-critical)', [
                                        'public_id' => $publicId,
                                        'error' => $e->getMessage()
                                    ]);
                                }
                            }
                        } else {
                            // Local storage path - try to delete using file system directly
                            $fullPath = storage_path('app/public/' . $image);
                            if (file_exists($fullPath)) {
                                unlink($fullPath);
                                \Log::info('Image deleted from local storage during disapproval', ['path' => $image]);
                            }
                        }
                    }
                }
            } else {
                // Cloudinary not configured, use local storage
                if ($product->image) {
                    $fullPath = storage_path('app/public/' . $product->image);
                    if (file_exists($fullPath)) {
                        unlink($fullPath);
                    }
                }
                if ($product->image2) {
                    $fullPath = storage_path('app/public/' . $product->image2);
                    if (file_exists($fullPath)) {
                        unlink($fullPath);
                    }
                }
                if ($product->image3) {
                    $fullPath = storage_path('app/public/' . $product->image3);
                    if (file_exists($fullPath)) {
                        unlink($fullPath);
                    }
                }
            }

            // Delete product compositions
            $product->compositions()->delete();
            
            // Delete the product
            $product->delete();

            return response()->json([
                'success' => true,
                'message' => 'Product disapproved and deleted successfully'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error disapproving product', [
                'product_id' => $productId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error disapproving product: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get product details for review
     */
    public function getProductDetails($productId)
    {
        try {
            $product = CatalogProduct::with(['compositions.componentProduct'])
                ->findOrFail($productId);

            // Add category to each composition from component product
            $product->compositions->transform(function($composition) {
                if ($composition->componentProduct) {
                    $composition->category = $composition->componentProduct->category;
                }
                return $composition;
            });

            // Ensure image_url is included in JSON response
            $product->setAppends(['image_url']);
            
            return response()->json([
                'success' => true,
                'product' => $product
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching product details: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get product compositions for edit
     */
    public function getProductCompositions($productId)
    {
        try {
            $product = CatalogProduct::with(['compositions.componentProduct'])
                ->findOrFail($productId);

            // Add category to each composition from component product
            $compositions = $product->compositions->map(function($composition) {
                if ($composition->componentProduct) {
                    $composition->category = $composition->componentProduct->category;
                }
                return $composition;
            });
            
            return response()->json([
                'success' => true,
                'compositions' => $compositions
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching product compositions: ' . $e->getMessage()
            ], 500);
        }
    }
}
