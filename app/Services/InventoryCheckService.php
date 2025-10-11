<?php

namespace App\Services;

use App\Models\CatalogProduct;
use App\Models\Product;
use App\Models\CatalogProductComposition;
use Illuminate\Support\Collection;

class InventoryCheckService
{
    /**
     * Check if a catalog product can be fulfilled based on component inventory
     */
    public function checkCatalogProductAvailability(CatalogProduct $catalogProduct, int $requestedQuantity = 1): array
    {
        $compositions = $catalogProduct->compositions()->with('componentProduct')->get();
        $issues = [];
        $totalRequired = [];

        foreach ($compositions as $composition) {
            $componentProduct = $composition->componentProduct;
            if (!$componentProduct) {
                $issues[] = [
                    'type' => 'missing_component',
                    'component_name' => $composition->component_name,
                    'message' => "Component '{$composition->component_name}' is not available in inventory."
                ];
                continue;
            }

            $requiredQuantity = $composition->quantity * $requestedQuantity;
            $availableStock = $componentProduct->stock ?? 0;

            if ($availableStock < $requiredQuantity) {
                $shortage = $requiredQuantity - $availableStock;
                $issues[] = [
                    'type' => 'insufficient_stock',
                    'component_name' => $componentProduct->name,
                    'required' => $requiredQuantity,
                    'available' => $availableStock,
                    'shortage' => $shortage,
                    'message' => "Insufficient stock for '{$componentProduct->name}'. Required: {$requiredQuantity}, Available: {$availableStock}"
                ];
            }

            $totalRequired[$componentProduct->id] = [
                'name' => $componentProduct->name,
                'required' => $requiredQuantity,
                'available' => $availableStock
            ];
        }

        return [
            'can_fulfill' => empty($issues),
            'issues' => $issues,
            'components_required' => $totalRequired
        ];
    }

    /**
     * Get alternative catalog products when the requested one is unavailable
     */
    public function getAlternativeProducts(CatalogProduct $unavailableProduct, int $limit = 3): Collection
    {
        // Get products in the same category
        $alternatives = CatalogProduct::where('category', $unavailableProduct->category)
            ->where('id', '!=', $unavailableProduct->id)
            ->where('status', true)
            ->where('is_approved', true)
            ->with('compositions.componentProduct')
            ->get()
            ->filter(function ($product) {
                // Only include products that can be fulfilled
                $check = $this->checkCatalogProductAvailability($product, 1);
                return $check['can_fulfill'];
            })
            ->take($limit);

        return $alternatives;
    }

    /**
     * Get low stock components that need restocking
     */
    public function getLowStockComponents(): Collection
    {
        return Product::where('stock', '<=', 5) // Assuming 5 is the low stock threshold
            ->where('stock', '>', 0)
            ->orderBy('stock', 'asc')
            ->get();
    }

    /**
     * Check if a simple product (not catalog) is available
     */
    public function checkSimpleProductAvailability(Product $product, int $requestedQuantity = 1): array
    {
        $availableStock = $product->stock ?? 0;
        
        if ($availableStock < $requestedQuantity) {
            $shortage = $requestedQuantity - $availableStock;
            return [
                'can_fulfill' => false,
                'issues' => [[
                    'type' => 'insufficient_stock',
                    'product_name' => $product->name,
                    'required' => $requestedQuantity,
                    'available' => $availableStock,
                    'shortage' => $shortage,
                    'message' => "Insufficient stock for '{$product->name}'. Required: {$requestedQuantity}, Available: {$availableStock}"
                ]],
                'product_info' => [
                    'name' => $product->name,
                    'required' => $requestedQuantity,
                    'available' => $availableStock
                ]
            ];
        }

        return [
            'can_fulfill' => true,
            'issues' => [],
            'product_info' => [
                'name' => $product->name,
                'required' => $requestedQuantity,
                'available' => $availableStock
            ]
        ];
    }

    /**
     * Get alternative simple products when the requested one is unavailable
     */
    public function getAlternativeSimpleProducts(Product $unavailableProduct, int $limit = 3): Collection
    {
        return Product::where('category', $unavailableProduct->category)
            ->where('id', '!=', $unavailableProduct->id)
            ->where('status', true)
            ->where('stock', '>', 0)
            ->orderBy('stock', 'desc')
            ->take($limit)
            ->get();
    }
}

