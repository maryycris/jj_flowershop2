<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    public function dashboard(Request $request)
    {
        $query = Product::query()->select(['id', 'name', 'price', 'image', 'description', 'category', 'stock', 'status']);
        
        // Only show FINISHED PRODUCTS that are:
        // 1. Active/approved by admin
        // 2. In stock (stock > 0)
        // 3. Only finished products (bouquets, flowers, gifts, arrangements)
        // Show finished-product categories
        $includeCategories = ['Bouquets', 'Flowers', 'Fresh Flowers', 'Artificial Flowers', 'Gifts', 'Arrangements'];
        $query->where('status', true)  // Product is active
              ->where('is_approved', true)  // Product is approved by admin
              // Allow products even if stock is null/0 (some items may not track stock)
              // ->where('stock', '>', 0)
              ->whereIn('category', $includeCategories);  // Only finished products

        if ($request->has('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        if ($request->has('search') && $request->search !== '') {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $products = $query->latest()->get();

        // Get 5 latest products as promoted products (same filters)
        $promotedProducts = Product::select(['id', 'name', 'price', 'image', 'description', 'category', 'stock', 'status'])
            ->where('status', true)
            ->where('is_approved', true)
            // ->where('stock', '>', 0)
            ->whereIn('category', $includeCategories)
            ->latest()->take(5)->get();

        $unreadCount = Auth::user()->unreadNotifications()->count();

        // The original dashboard logic for orders is removed as per new UI
        return view('customer.dashboard', compact('products', 'unreadCount', 'promotedProducts'));
    }
}
 