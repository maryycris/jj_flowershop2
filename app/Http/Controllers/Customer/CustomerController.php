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
        $query = Product::query();

        if ($request->has('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        if ($request->has('search') && $request->search !== '') {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $products = $query->latest()->get();

        // Get 5 latest products as promoted products
        $promotedProducts = Product::latest()->take(5)->get();

        $unreadCount = Auth::user()->unreadNotifications()->count();

        // The original dashboard logic for orders is removed as per new UI
        return view('customer.dashboard', compact('products', 'unreadCount', 'promotedProducts'));
    }
}
 