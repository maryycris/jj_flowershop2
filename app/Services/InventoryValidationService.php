<?php

namespace App\Services;

use App\Models\Product;
use App\Models\CatalogProduct;
use App\Models\CatalogProductComposition;

class InventoryValidationService
{
    /**
     * Check if a catalog product can be made with current inventory
     */
    public function validateCatalogProductInventory($catalogProductId, $quantity = 1)
    {
        $catalogProduct = CatalogProduct::with('compositions.product')->find($catalogProductId);
        
        if (!$catalogProduct) {
            return [
                'can_make' => false,
                'message' => 'Product not found',
                'missing_components' => [],
                'alternatives' => []
            ];
        }

        $missingComponents = [];
        $lowStockComponents = [];
        
        foreach ($catalogProduct->compositions as $composition) {
            $requiredQuantity = $composition->quantity * $quantity;
            $availableStock = $composition->product->stock ?? 0;
            
            if ($availableStock < $requiredQuantity) {
                $missingComponents[] = [
                    'product' => $composition->product,
                    'required' => $requiredQuantity,
                    'available' => $availableStock,
                    'shortage' => $requiredQuantity - $availableStock
                ];
                
                if ($availableStock > 0) {
                    $lowStockComponents[] = [
                        'product' => $composition->product,
                        'required' => $requiredQuantity,
                        'available' => $availableStock,
                        'shortage' => $requiredQuantity - $availableStock
                    ];
                }
            }
        }

        $canMake = empty($missingComponents);
        
        return [
            'can_make' => $canMake,
            'message' => $canMake ? 'Product available' : 'Insufficient inventory',
            'missing_components' => $missingComponents,
            'low_stock_components' => $lowStockComponents,
            'alternatives' => $this->getAlternativeProducts($catalogProduct, $missingComponents)
        ];
    }

    /**
     * Get alternative products when components are missing
     */
    private function getAlternativeProducts($catalogProduct, $missingComponents)
    {
        $alternatives = [];
        
        foreach ($missingComponents as $missing) {
            $product = $missing['product'];
            
            // Find products in the same category with available stock
            $categoryAlternatives = Product::where('category', $product->category)
                ->where('id', '!=', $product->id)
                ->where('stock', '>', 0)
                ->where('status', true)
                ->limit(3)
                ->get();
                
            if ($categoryAlternatives->count() > 0) {
                $alternatives[] = [
                    'missing_product' => $product,
                    'suggestions' => $categoryAlternatives
                ];
            }
        }
        
        return $alternatives;
    }

    /**
     * Check if a simple product has enough stock
     */
    public function validateProductInventory($productId, $quantity = 1)
    {
        $product = Product::find($productId);
        
        if (!$product) {
            return [
                'can_make' => false,
                'message' => 'Product not found',
                'alternatives' => []
            ];
        }

        $canMake = $product->stock >= $quantity;
        
        return [
            'can_make' => $canMake,
            'message' => $canMake ? 'Product available' : 'Insufficient stock',
            'available_stock' => $product->stock,
            'required_quantity' => $quantity,
            'shortage' => $canMake ? 0 : $quantity - $product->stock,
            'alternatives' => $canMake ? [] : $this->getProductAlternatives($product)
        ];
    }

    /**
     * Get alternative products for a simple product
     */
    private function getProductAlternatives($product)
    {
        return Product::where('category', $product->category)
            ->where('id', '!=', $product->id)
            ->where('stock', '>', 0)
            ->where('status', true)
            ->limit(3)
            ->get();
    }

    /**
     * Format inventory validation message for SweetAlert
     */
    public function formatInventoryMessage($validation)
    {
        if ($validation['can_make']) {
            return [
                'type' => 'success',
                'title' => 'Available!',
                'text' => $validation['message']
            ];
        }

        $message = $validation['message'] . "\n\n";
        
        if (isset($validation['missing_components'])) {
            $message .= "Missing components:\n";
            foreach ($validation['missing_components'] as $component) {
                $message .= "• {$component['product']->name}: Need {$component['required']}, have {$component['available']}\n";
            }
        } elseif (isset($validation['shortage'])) {
            $message .= "Available stock: {$validation['available_stock']}\n";
            $message .= "Required: {$validation['required_quantity']}\n";
            $message .= "Shortage: {$validation['shortage']}\n";
        }

        return [
            'type' => 'warning',
            'title' => 'Low Stock Alert!',
            'text' => $message,
            'showAlternatives' => !empty($validation['alternatives'])
        ];
    }
}
