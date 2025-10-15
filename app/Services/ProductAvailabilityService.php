<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductComposition;
use App\Models\CatalogProduct;
use App\Models\CatalogProductComposition;

class ProductAvailabilityService
{
    /**
     * Check if a catalog product can be fulfilled based on its material availability.
     *
     * @param int $catalogProductId
     * @param int $desiredQuantity
     * @return array
     */
    public function checkCatalogProductAvailability(int $catalogProductId, int $desiredQuantity = 1): array
    {
        $catalogProduct = CatalogProduct::with('compositions.componentProduct')->find($catalogProductId);

        if (!$catalogProduct) {
            return [
                'can_fulfill' => false,
                'reason' => 'Product not found',
                'available_quantity' => 0,
                'insufficient_materials' => []
            ];
        }

        // If product has no composition data, assume it can be fulfilled (for products without materials)
        if ($catalogProduct->compositions->count() === 0) {
            return [
                'can_fulfill' => true,
                'reason' => 'No material requirements',
                'available_quantity' => 999, // Assume unlimited
                'insufficient_materials' => []
            ];
        }

        // Check material availability
        $insufficientMaterials = [];
        $canFulfill = true;
        $maxQuantity = PHP_INT_MAX;

        foreach ($catalogProduct->compositions as $composition) {
            $componentProduct = $composition->componentProduct;
            $availableStock = $componentProduct ? $componentProduct->stock : 0;
            $requiredQuantity = $composition->quantity * $desiredQuantity;

            if ($availableStock < $requiredQuantity) {
                $canFulfill = false;
                $insufficientMaterials[] = [
                    'material' => $composition->component_name,
                    'required' => $requiredQuantity,
                    'available' => $availableStock,
                    'shortage' => $requiredQuantity - $availableStock
                ];
            } else {
                // Calculate how many units we can make with this material
                $maxFromThisMaterial = floor($availableStock / $composition->quantity);
                $maxQuantity = min($maxQuantity, $maxFromThisMaterial);
            }
        }

        return [
            'can_fulfill' => $canFulfill,
            'reason' => $canFulfill ? 'Sufficient materials' : 'Insufficient materials',
            'available_quantity' => $canFulfill ? $maxQuantity : 0,
            'insufficient_materials' => $insufficientMaterials
        ];
    }

    /**
     * Check if a regular product can be fulfilled based on its material availability.
     *
     * @param int $productId
     * @param int $desiredQuantity
     * @return array
     */
    public function checkProductAvailability(int $productId, int $desiredQuantity = 1): array
    {
        $product = Product::with('compositions.component')->find($productId);

        if (!$product) {
            return [
                'can_fulfill' => false,
                'reason' => 'Product not found',
                'available_quantity' => 0,
                'insufficient_materials' => []
            ];
        }

        // If product has no composition data, check direct stock
        if ($product->compositions->count() === 0) {
            $canFulfill = $product->stock >= $desiredQuantity;
            return [
                'can_fulfill' => $canFulfill,
                'reason' => $canFulfill ? 'Sufficient stock' : 'Insufficient stock',
                'available_quantity' => $canFulfill ? $product->stock : 0,
                'insufficient_materials' => []
            ];
        }

        // Check material availability
        $insufficientMaterials = [];
        $canFulfill = true;
        $maxQuantity = PHP_INT_MAX;

        foreach ($product->compositions as $composition) {
            $componentProduct = $composition->component;
            $availableStock = $componentProduct ? $componentProduct->stock : 0;
            $requiredQuantity = $composition->quantity * $desiredQuantity;

            if ($availableStock < $requiredQuantity) {
                $canFulfill = false;
                $insufficientMaterials[] = [
                    'material' => $composition->component_name,
                    'required' => $requiredQuantity,
                    'available' => $availableStock,
                    'shortage' => $requiredQuantity - $availableStock
                ];
            } else {
                // Calculate how many units we can make with this material
                $maxFromThisMaterial = floor($availableStock / $composition->quantity);
                $maxQuantity = min($maxQuantity, $maxFromThisMaterial);
            }
        }

        return [
            'can_fulfill' => $canFulfill,
            'reason' => $canFulfill ? 'Sufficient materials' : 'Insufficient materials',
            'available_quantity' => $canFulfill ? $maxQuantity : 0,
            'insufficient_materials' => $insufficientMaterials
        ];
    }

    /**
     * Get availability status for multiple catalog products at once.
     *
     * @param array $catalogProductIds
     * @return array
     */
    public function getBulkCatalogAvailability(array $catalogProductIds): array
    {
        $results = [];
        
        foreach ($catalogProductIds as $catalogProductId) {
            $results[$catalogProductId] = $this->checkCatalogProductAvailability($catalogProductId, 1);
        }

        return $results;
    }

    /**
     * Get availability status for multiple products at once.
     *
     * @param array $productIds
     * @return array
     */
    public function getBulkAvailability(array $productIds): array
    {
        $results = [];
        
        foreach ($productIds as $productId) {
            $results[$productId] = $this->checkProductAvailability($productId, 1);
        }

        return $results;
    }

    /**
     * Check if a catalog product should be marked as out of stock.
     *
     * @param int $catalogProductId
     * @return bool
     */
    public function isCatalogProductOutOfStock(int $catalogProductId): bool
    {
        $availability = $this->checkCatalogProductAvailability($catalogProductId, 1);
        return !$availability['can_fulfill'];
    }

    /**
     * Check if a product should be marked as out of stock.
     *
     * @param int $productId
     * @return bool
     */
    public function isOutOfStock(int $productId): bool
    {
        $availability = $this->checkProductAvailability($productId, 1);
        return !$availability['can_fulfill'];
    }
}
