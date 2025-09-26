<?php

namespace App\Helpers;

use App\Models\Product;

class ProductFilterHelper
{
    /**
     * Get products for customer catalog (approved, active, in stock)
     */
    public static function getCustomerCatalog($filters = [])
    {
        $query = Product::query();
        
        // Only show products that are:
        // 1. Active/approved by admin
        // 2. In stock (stock > 0)
        // 3. Not customization components
        $excludeCategories = ['Wrapper', 'Focal', 'Greeneries', 'Ribbons', 'Fillers'];
        $query->where('status', true)
              ->where('is_approved', true)
              ->where('stock', '>', 0)
              ->whereNotIn('category', $excludeCategories);
        
        // Apply additional filters
        if (isset($filters['category']) && $filters['category'] !== 'all') {
            $query->where('category', $filters['category']);
        }
        
        if (isset($filters['search']) && $filters['search'] !== '') {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }
        
        if (isset($filters['price_min']) && $filters['price_min'] !== '') {
            $query->where('price', '>=', $filters['price_min']);
        }
        
        if (isset($filters['price_max']) && $filters['price_max'] !== '') {
            $query->where('price', '<=', $filters['price_max']);
        }
        
        // Apply sorting
        $sort = $filters['sort'] ?? 'newest';
        switch ($sort) {
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'popular':
                $query->orderBy('qty_sold', 'desc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }
        
        return $query;
    }
    
    /**
     * Get products for clerk catalog (approved and active)
     */
    public static function getClerkCatalog($filters = [])
    {
        $query = Product::query();
        
        // Only show approved and active products
        $query->where('status', true)
              ->where('is_approved', true);
        
        // Apply additional filters
        if (isset($filters['category']) && $filters['category'] !== '') {
            $query->where('category', $filters['category']);
        }
        
        if (isset($filters['search']) && $filters['search'] !== '') {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }
        
        if (isset($filters['price_min']) && $filters['price_min'] !== '') {
            $query->where('price', '>=', $filters['price_min']);
        }
        
        if (isset($filters['price_max']) && $filters['price_max'] !== '') {
            $query->where('price', '<=', $filters['price_max']);
        }
        
        // Apply sorting
        $sort = $filters['sort'] ?? 'newest';
        switch ($sort) {
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'popular':
                $query->orderBy('qty_sold', 'desc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }
        
        return $query;
    }
    
    /**
     * Get all products for admin (no filters)
     */
    public static function getAdminCatalog($filters = [])
    {
        $query = Product::query();
        
        // Apply filters
        if (isset($filters['category']) && $filters['category'] !== '') {
            $query->where('category', $filters['category']);
        }
        
        if (isset($filters['search']) && $filters['search'] !== '') {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }
        
        if (isset($filters['price_min']) && $filters['price_min'] !== '') {
            $query->where('price', '>=', $filters['price_min']);
        }
        
        if (isset($filters['price_max']) && $filters['price_max'] !== '') {
            $query->where('price', '<=', $filters['price_max']);
        }
        
        // Apply sorting
        $sort = $filters['sort'] ?? 'newest';
        switch ($sort) {
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'popular':
                $query->orderBy('qty_sold', 'desc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }
        
        return $query;
    }
    
    /**
     * Check if product should be visible to customers
     */
    public static function isVisibleToCustomer(Product $product)
    {
        $excludeCategories = ['Wrapper', 'Focal', 'Greeneries', 'Ribbons', 'Fillers'];
        
        return $product->status === true &&
               $product->is_approved === true &&
               $product->stock > 0 &&
               !in_array($product->category, $excludeCategories);
    }
    
    /**
     * Get stock status for display
     */
    public static function getStockStatus(Product $product)
    {
        if ($product->stock <= 0) {
            return 'out_of_stock';
        } elseif ($product->stock <= 5) {
            return 'low_stock';
        } else {
            return 'in_stock';
        }
    }
}
