<?php

namespace App\Traits;

use App\Models\Product;
use App\Models\CustomizeItem;
use Illuminate\Support\Str;

trait CustomizeFilterTrait
{
    /**
     * Get filtered customize items for admin, clerk, and customer
     * Ensures all three use exactly the same data source and filtering
     * Shows all items from customize_items table (regardless of inventory_item_id)
     */
    public function getCustomizeItems()
    {
        // Get customize items from the separate customize_items table
        // Eager load inventory item relationship to get latest price
        $customizeItems = CustomizeItem::where('status', true)
            ->where('is_approved', true) // Only show approved items
            ->with('inventoryItem')
            ->orderBy('category')
            ->orderBy('name')
            ->get();

        // Compute display price: use inventory price if linked, otherwise use own price
        foreach ($customizeItems as $ci) {
            // If linked to inventory, use inventory price; otherwise use own price
            $price = $ci->inventoryItem ? $ci->inventoryItem->price : ($ci->price ?? 0);
            // Attach a non-persistent attribute for views
            $ci->computed_price = $price;
        }
            
        // Show all items from customize_items table
        // Items can be linked to inventory or standalone - both are allowed
        
        return $customizeItems->groupBy('category');
    }
    
    /**
     * Get the standard categories for customize
     */
    public function getCustomizeCategories()
    {
        return ['Fresh Flowers','Greenery','Artificial Flowers','Ribbon','Wrappers'];
    }
    
    /**
     * Get filtered items for price calculation (same filtering as display)
     * Uses CustomizeItem table first, then falls back to Product table
     */
    public function getCustomizeItemsForPricing()
    {
        // First, get customize items with inventory relationships
        $customizeItems = CustomizeItem::where('status', true)
            ->with('inventoryItem')
            ->get();
        
        // Build a map by item name, prioritizing inventory price
        $itemsMap = [];
        foreach ($customizeItems as $item) {
            $key = strtolower(trim($item->name));
            $price = $item->inventoryItem ? $item->inventoryItem->price : ($item->price ?? 0);
            // Create a simple object with price for calculation
            $itemsMap[$key] = (object)[
                'name' => $item->name,
                'price' => $price,
                'category' => $item->category
            ];
        }
        
        // If customize items exist, return the map
        if (!empty($itemsMap)) {
            return collect($itemsMap);
        }
        
        // Fallback to Product table if no customize items
        $categories = $this->getCustomizeCategories();
        $excludeKeywords = ['bouquet', 'arrangement', 'basket', 'vase', 'harmony', 'bundle', 'set', 'collection'];
        
        $products = Product::whereIn('category', $categories)
            ->get();

        $safeCategories = ['Wrappers', 'Ribbon'];
        $products = $products->filter(function ($product) use ($excludeKeywords, $safeCategories) {
            if (in_array($product->category, $safeCategories, true)) {
                return true;
            }
            $nameLower = mb_strtolower($product->name);
            foreach ($excludeKeywords as $kw) {
                if (str_contains($nameLower, mb_strtolower($kw))) {
                    return false;
                }
            }
            return true;
        });

        $normalize = function ($value) {
            $v = trim($value ?? '');
            $v = preg_replace('/\s+/', ' ', $v);
            return mb_strtolower($v);
        };

        $products = $products->unique(function ($product) use ($normalize) {
            return $normalize($product->category) . '|' . $normalize($product->name);
        })->sortBy(function ($p) use ($normalize) {
            return $normalize($p->category) . '|' . $normalize($p->name);
        })->values();

        // Return products keyed by normalized name
        $result = [];
        foreach ($products as $product) {
            $key = strtolower(trim($product->name));
            $result[$key] = $product;
        }
        return collect($result);
    }
}