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
        $products = Product::all();
        return view('admin.reports.inventory', compact('products'));
    }

    public function sales()
    {
        $sales = Order::with('user')
            ->where('status', 'completed')
            ->orderByDesc('created_at')
            ->get()
            ->map(function($order) {
                return (object) [
                    'date' => $order->created_at->format('m/d/Y'),
                    'order_number' => str_pad($order->id, 5, '0', STR_PAD_LEFT),
                    'customer' => $order->user->name ?? 'N/A',
                    'qty' => $order->products->sum(function($p) { return $p->pivot->quantity; }),
                    'total' => $order->total_price,
                ];
            });
        return view('admin.reports.sales', compact('sales'));
    }

    public function generateSalesReport(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $categoryName = $request->input('category_name');

        $query = Order::query()
            ->select(
                DB::raw('DATE(orders.created_at) as date'),
                DB::raw('SUM(orders.total_price) as total_sales'),
                DB::raw('COUNT(DISTINCT orders.id) as total_orders')
            )
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('orders.status', 'completed')
            ->groupBy(DB::raw('DATE(orders.created_at)'))
            ->orderBy('date');

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
