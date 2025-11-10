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

        $products = $query->latest()->paginate(20); // 20 products per page (5 rows of 4 products each)

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

    /**
     * Get product search suggestions for autocomplete
     */
    public function searchSuggestions(Request $request)
    {
        $query = $request->get('q', '');
        
        if (strlen($query) < 2) {
            return response()->json([
                'success' => false,
                'suggestions' => []
            ]);
        }
        
        // Get product suggestions
        $includeCategories = ['Bouquets', 'Packages', 'Gifts'];
        $suggestions = CatalogProduct::select(['id', 'name', 'price', 'category'])
            ->where('status', true)
            ->where('is_approved', true)
            ->whereIn('category', $includeCategories)
            ->where('name', 'like', '%' . $query . '%')
            ->orderBy('name', 'asc')
            ->limit(8) // Limit to 8 suggestions for mobile responsiveness
            ->get()
            ->map(function($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'category' => $product->category
                ];
            });
        
        return response()->json([
            'success' => true,
            'suggestions' => $suggestions
        ]);
    }

    /**
     * Show store credit history for customer
     */
    public function storeCreditHistory()
    {
        $user = Auth::user();
        
        // Get all store credit transactions (refunds)
        $storeCreditTransactions = Order::where('user_id', $user->id)
            ->where('refund_method', 'store_credit')
            ->whereNotNull('refund_amount')
            ->whereNotNull('refund_processed_at')
            ->with(['products'])
            ->latest('refund_processed_at')
            ->paginate(15);
            
        // Calculate total store credit balance
        $totalStoreCredit = $storeCreditTransactions->sum('refund_amount');
        
        return view('customer.store-credit.history', compact('storeCreditTransactions', 'totalStoreCredit'));
    }
}
 