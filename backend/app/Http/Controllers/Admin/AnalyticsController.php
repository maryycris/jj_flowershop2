<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function dashboard()
    {
        // Get date ranges
        $today = Carbon::today();
        $thisWeek = Carbon::now()->startOfWeek();
        $thisMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();
        $thisYear = Carbon::now()->startOfYear();

        // Sales Overview
        $todayRevenue = Order::whereDate('created_at', $today)
            ->where('status', '!=', 'cancelled')
            ->sum('total_price');

        $thisWeekRevenue = Order::where('created_at', '>=', $thisWeek)
            ->where('status', '!=', 'cancelled')
            ->sum('total_price');

        $thisMonthRevenue = Order::where('created_at', '>=', $thisMonth)
            ->where('status', '!=', 'cancelled')
            ->sum('total_price');

        $lastMonthRevenue = Order::whereBetween('created_at', [$lastMonth, $thisMonth])
            ->where('status', '!=', 'cancelled')
            ->sum('total_price');

        // Order Counts
        $todayOrders = Order::whereDate('created_at', $today)->count();
        $thisWeekOrders = Order::where('created_at', '>=', $thisWeek)->count();
        $thisMonthOrders = Order::where('created_at', '>=', $thisMonth)->count();

        // Order Status Overview
        $pendingOrders = Order::where('status', 'pending')->count();
        $approvedOrders = Order::where('status', 'approved')->count();
        $outForDeliveryOrders = Order::where('status', 'out_for_delivery')->count();
        $completedOrders = Order::where('status', 'completed')->count();
        $cancelledOrders = Order::where('status', 'cancelled')->count();

        // Online vs Walk-in Orders
        $onlineOrders = Order::where('type', 'online')->count();
        $walkinOrders = Order::where('type', 'walk-in')->count();

        // Average Order Value
        $avgOrderValue = Order::where('status', '!=', 'cancelled')
            ->where('created_at', '>=', $thisMonth)
            ->avg('total_price');

        // Monthly Sales Data for Chart (last 6 months)
        $monthlySales = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthStart = $month->copy()->startOfMonth();
            $monthEnd = $month->copy()->endOfMonth();
            
            $revenue = Order::whereBetween('created_at', [$monthStart, $monthEnd])
                ->where('status', '!=', 'cancelled')
                ->sum('total_price');
            
            $monthlySales[] = [
                'month' => $month->format('M Y'),
                'revenue' => $revenue
            ];
        }

        // Daily Sales Data for Chart (last 7 days)
        $dailySales = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = Carbon::now()->subDays($i);
            $dayStart = $day->copy()->startOfDay();
            $dayEnd = $day->copy()->endOfDay();
            
            $revenue = Order::whereBetween('created_at', [$dayStart, $dayEnd])
                ->where('status', '!=', 'cancelled')
                ->sum('total_price');
            
            $dailySales[] = [
                'day' => $day->format('M d'),
                'revenue' => $revenue
            ];
        }

        // Top Selling Products (this month)
        $topProducts = Order::join('order_product', 'orders.id', '=', 'order_product.order_id')
            ->join('products', 'order_product.product_id', '=', 'products.id')
            ->where('orders.created_at', '>=', $thisMonth)
            ->where('orders.status', '!=', 'cancelled')
            ->select('products.name', 'products.price', DB::raw('SUM(order_product.quantity) as total_quantity'), DB::raw('SUM(order_product.quantity * products.price) as total_revenue'))
            ->groupBy('products.id', 'products.name', 'products.price')
            ->orderBy('total_quantity', 'desc')
            ->limit(10)
            ->get();

        // Revenue Growth
        $revenueGrowth = 0;
        if ($lastMonthRevenue > 0) {
            $revenueGrowth = (($thisMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100;
        }

        return view('admin.analytics.dashboard', compact(
            'todayRevenue', 'thisWeekRevenue', 'thisMonthRevenue', 'lastMonthRevenue',
            'todayOrders', 'thisWeekOrders', 'thisMonthOrders',
            'pendingOrders', 'approvedOrders', 'outForDeliveryOrders', 'completedOrders', 'cancelledOrders',
            'onlineOrders', 'walkinOrders', 'avgOrderValue',
            'monthlySales', 'dailySales', 'topProducts', 'revenueGrowth'
        ));
    }
}
