<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductComposition;
use App\Models\CatalogProduct;
use App\Models\CatalogProductComposition;
use Illuminate\Support\Collection;

class ProductCompositionService
{
    /**
     * Get product composition breakdown with stock analysis
     */
    public function getProductCompositionBreakdown($productId, $requiredQuantity = 1)
    {
        $product = Product::with('compositions')->find($productId);
        
        if (!$product) {
            return null;
        }

        $breakdown = [
            'product' => $product,
            'required_quantity' => $requiredQuantity,
            'components' => [],
            'total_components' => 0,
            'sufficient_components' => 0,
            'insufficient_components' => 0,
            'can_fulfill' => true
        ];

        $compositions = $product->compositions;

        // Fallback: if no product-level compositions, try catalog product compositions by name
        if ($compositions->isEmpty()) {
            $catalog = CatalogProduct::where('name', $product->name)
                ->where('status', true)
                ->where('is_approved', true)
                ->with('compositions')
                ->first();

            if ($catalog && $catalog->compositions->count() > 0) {
                $compositions = $catalog->compositions;
            }
        }

        foreach ($compositions as $composition) {
            // CatalogProductComposition has componentProduct relation; normalize fields
            $componentId = $composition->component_id;
            $component = Product::find($componentId);

            $quantityPerUnit = $composition->quantity;
            $required = $quantityPerUnit * $requiredQuantity;
            $available = $component ? (int) $component->stock : 0;
            $sufficient = $available >= $required;

            if (!$sufficient) {
                $breakdown['can_fulfill'] = false;
                $breakdown['insufficient_components']++;
            } else {
                $breakdown['sufficient_components']++;
            }

            $breakdown['components'][] = [
                'composition' => $composition,
                'component' => $component,
                'required_quantity' => $required,
                'available_stock' => $available,
                'sufficient' => $sufficient,
                'shortage' => max(0, $required - $available),
                'status' => $sufficient ? 'Sufficient' : 'Insufficient',
                'status_class' => $sufficient ? 'success' : 'danger'
            ];
        }

        $breakdown['total_components'] = count($breakdown['components']);

        return $breakdown;
    }

    /**
     * Get simplified composition summary
     */
    public function getCompositionSummary($productId, $requiredQuantity = 1)
    {
        $breakdown = $this->getProductCompositionBreakdown($productId, $requiredQuantity);
        
        if (!$breakdown) {
            return null;
        }

        return [
            'product_name' => $breakdown['product']->name,
            'required_quantity' => $requiredQuantity,
            'can_fulfill' => $breakdown['can_fulfill'],
            'total_components' => $breakdown['total_components'],
            'sufficient_components' => $breakdown['sufficient_components'],
            'insufficient_components' => $breakdown['insufficient_components'],
            'components' => $breakdown['components']
        ];
    }

    /**
     * Check if product can be fulfilled with current stock
     */
    public function canFulfillProduct($productId, $requiredQuantity = 1)
    {
        $breakdown = $this->getProductCompositionBreakdown($productId, $requiredQuantity);
        return $breakdown ? $breakdown['can_fulfill'] : false;
    }

    /**
     * Get stock requirements for product
     */
    public function getStockRequirements($productId, $requiredQuantity = 1)
    {
        $product = Product::with('compositions')->find($productId);
        
        if (!$product) {
            return [];
        }

        $requirements = [];
        
        foreach ($product->compositions as $composition) {
            $component = Product::find($composition->component_id);
            $required = $composition->quantity * $requiredQuantity;
            
            $requirements[] = [
                'component_name' => $composition->component_name,
                'component_id' => $composition->component_id,
                'required_per_unit' => $composition->quantity,
                'required_total' => $required,
                'available_stock' => $component ? $component->stock : 0,
                'unit' => $composition->unit,
                'sufficient' => $component ? ($component->stock >= $required) : false
            ];
        }

        return $requirements;
    }
}
