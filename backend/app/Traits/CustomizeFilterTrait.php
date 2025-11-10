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
     * EXCLUDES items that exist in inventory (Product table)
     */
    public function getCustomizeItems()
    {
        // Get all product names from inventory to exclude them
        $inventoryProductNames = Product::pluck('name')->map(function($name) {
            return mb_strtolower(trim($name));
        })->toArray();
        
        // Get customize items from the separate customize_items table
        // Eager load inventory item relationship to get latest price
        $customizeItems = CustomizeItem::where('status', true)
            ->with('inventoryItem')
            ->orderBy('category')
            ->orderBy('name')
            ->get();

        // Filter out items that are in inventory:
        // 1. Items with inventory_item_id (linked to Product)
        // 2. Items whose name matches a Product name
        $customizeItems = $customizeItems->filter(function($ci) use ($inventoryProductNames) {
            // Exclude if linked to inventory item
            if ($ci->inventoryItem) {
                return false;
            }
            
            // Exclude if name matches any inventory product name
            $itemName = mb_strtolower(trim($ci->name));
            if (in_array($itemName, $inventoryProductNames)) {
                return false;
            }
            
            return true;
        });

        // Compute display price: use own price (since we're excluding inventory items)
        foreach ($customizeItems as $ci) {
            $price = $ci->price ?? 0;
            // Attach a non-persistent attribute for views
            $ci->computed_price = $price;
        }
            
        // DO NOT fallback to products table - only show CustomizeItems that are NOT in inventory
        // If customize_items table is empty or all items are filtered out, return empty collection
        
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