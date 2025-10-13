<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class ReportController extends Controller
{
    public function index()
    {
        $categories = Product::select('category')->distinct()->get()->pluck('category');
        $products = Product::withCount(['orderProducts as units_sold' => function ($query) {
            $query->select(DB::raw('SUM(quantity)'));
        }])->get();
        return view('admin.reports.index', compact('categories', 'products'));
    }

    public function inventory()
    {
        // Load ALL flower-related products and materials, then for each product compute units sold (sum of quantity in order_product)
        // Include finished products + raw materials (exclude only office supplies)
        $excludeCategories = ['Office Supplies'];
        $products = Product::whereNotIn('category', $excludeCategories)->get();

        // Add "units_sold" property dynamically
        $products->map(function($product) {
            $product->units_sold = DB::table('order_product')
                ->where('product_id', $product->id)
                ->sum('quantity');
            return $product;
        });

        return view('admin.reports.inventory', compact('products'));
    }

    public function sales()
    {
        // Show all orders with priority to completed orders for sales reporting
        $sales = Order::with('user', 'products')
            ->orderByDesc('created_at')
            ->get()
            ->map(function($order) {
                // Use order_status if available, fallback to status
                $currentStatus = $order->order_status ?? $order->status;
                
                return (object) [
                    'date' => $order->created_at->format('m/d/Y'),
                    'order_number' => str_pad($order->id, 5, '0', STR_PAD_LEFT),
                    'customer' => $order->user->name ?? 'N/A',
                    // Ensure qty always numeric
                    'qty' => $order->products->sum(function($p) { return $p->pivot->quantity ?? 0; }),
                    'total' => $order->total_price ?? 0,
                    'status' => $currentStatus,
                    'is_completed' => in_array($currentStatus, ['completed', 'delivered']),
                    'completed_at' => $order->completed_at ? \Carbon\Carbon::parse($order->completed_at)->format('m/d/Y H:i') : null,
                ];
            });
        return view('admin.reports.sales', compact('sales'));
    }

    public function generateSalesReport(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $categoryName = $request->input('category_name');
        $status = $request->input('status');

        $query = Order::query()
            ->select(
                DB::raw('DATE(orders.created_at) as date'),
                DB::raw('SUM(orders.total_price) as total_sales'),
                DB::raw('COUNT(DISTINCT orders.id) as total_orders')
            )
            ->join('order_product', 'orders.id', '=', 'order_product.order_id')
            ->join('products', 'order_product.product_id', '=', 'products.id')
            ->groupBy(DB::raw('DATE(orders.created_at)'))
            ->orderBy('date');

        if ($status && $status !== 'all_completed') {
            $query->where('orders.status', $status);
        }
        // If 'all_completed', show all orders that are paid/delivered/completed but not just limited to 'completed'
        if ($status === 'all_completed') {
            $query->whereIn('orders.status', ['completed', 'paid', 'delivered', 'on_delivery', 'approved']);
        }
        if ($startDate) {
            $query->whereDate('orders.created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('orders.created_at', '<=', $endDate);
        }
        if ($categoryName) {
            $query->where('products.category', $categoryName);
        }
        $salesData = $query->get();
        return response()->json($salesData);
    }

    public function generateOrderStatusReport(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $status = $request->input('status');

        $query = Order::query()
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(id) as total_orders')
            )
            ->when($status, function ($q) use ($status) {
                $q->where('status', $status);
            })
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date');

        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        $orderStatusData = $query->get();

        return response()->json($orderStatusData);
    }

    public function generateProductPerformanceReport(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $productName = $request->input('product_name');

        $query = Product::query()
            ->select(
                'products.name as product_name',
                DB::raw('SUM(order_items.quantity) as units_sold'),
                DB::raw('SUM(order_items.price * order_items.quantity) as revenue')
            )
            ->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', 'completed')
            ->groupBy('products.name')
            ->orderByDesc('revenue');

        if ($startDate) {
            $query->whereDate('orders.created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('orders.created_at', '<=', $endDate);
        }

        if ($productName) {
            $query->where('products.name', 'like', '%' . $productName . '%');
        }

        $productPerformanceData = $query->get();

        return response()->json($productPerformanceData);
    }

    public function generateUserActivityReport(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $role = $request->input('role');

        $query = User::query()
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(id) as total_users')
            )
            ->when($role, function ($q) use ($role) {
                $q->where('role', $role);
            })
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date');

        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        $userActivityData = $query->get();

        return response()->json($userActivityData);
    }
}
