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
            ->get();

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
            ->get();

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
            
            // Delete associated images
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            if ($product->image2) {
                Storage::disk('public')->delete($product->image2);
            }
            if ($product->image3) {
                Storage::disk('public')->delete($product->image3);
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
