<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\CatalogProduct;
use App\Services\ProductAvailabilityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    public function dashboard(Request $request)
    {
        // Use CatalogProduct instead of Product for customer catalog
        $query = CatalogProduct::query()->select(['id', 'name', 'price', 'image', 'description', 'category', 'status', 'is_approved']);
        
        // Only show catalog products that are:
        // 1. Active/approved by admin
        // 2. Only finished products (bouquets, packages, gifts)
        $includeCategories = ['Bouquets', 'Packages', 'Gifts'];
        $query->where('status', true)  // Product is active
              ->where('is_approved', true)  // Product is approved by admin
              ->whereIn('category', $includeCategories);  // Only finished products

        if ($request->has('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        if ($request->has('search') && $request->search !== '') {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->has('min_price') && $request->min_price !== '') {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->has('max_price') && $request->max_price !== '') {
            $query->where('price', '<=', $request->max_price);
        }

        $products = $query->latest()->get();

        // Get 5 latest products as promoted products (same filters)
        $promotedProducts = CatalogProduct::select(['id', 'name', 'price', 'image', 'description', 'category', 'status', 'is_approved'])
            ->where('status', true)
            ->where('is_approved', true)
            ->whereIn('category', $includeCategories)
            ->latest()->take(5)->get();

        // Check availability for all products
        $availabilityService = new ProductAvailabilityService();
        $productIds = $products->pluck('id')->toArray();
        $promotedProductIds = $promotedProducts->pluck('id')->toArray();
        
        $productAvailability = $availabilityService->getBulkCatalogAvailability($productIds);
        $promotedProductAvailability = $availabilityService->getBulkCatalogAvailability($promotedProductIds);

        $unreadCount = Auth::check() ? Auth::user()->unreadNotifications()->count() : 0;

        // The original dashboard logic for orders is removed as per new UI
        return view('customer.dashboard', compact('products', 'unreadCount', 'promotedProducts', 'productAvailability', 'promotedProductAvailability'));
    }
}
 