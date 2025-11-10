<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\InventoryMovement;
use App\Services\OrderStatusService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function dashboard()
    {
        try {
            $orderStatusService = new OrderStatusService();
            $orderCounts = $orderStatusService->getOrderCounts();

        // Aggregate totals for dashboard analytics
        $totalOrders = Order::count();
        $totalProducts = Product::count();
        $totalCustomers = User::where('role', 'customer')->count();

        // Most popular products (all-time by quantity sold)
        $popularProducts = \DB::table('order_product')
            ->join('products', 'order_product.product_id', '=', 'products.id')
            ->select('products.id', 'products.name', \DB::raw('SUM(order_product.quantity) as total_quantity'))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get();

        // Inventory movements data for dashboard
        $totalMovementsToday = InventoryMovement::whereDate('created_at', today())->count();
        $recentMovements = InventoryMovement::with(['product', 'user', 'order'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Restock products (low stock)
        $restockProducts = Product::where('stock', '<=', \DB::raw('reorder_min'))
            ->where('reorder_min', '>', 0)
            ->whereNull('deleted_at')
            ->get();

        // Revenue Analytics
        $todayRevenue = Order::whereDate('created_at', today())
            ->whereIn('order_status', ['completed', 'delivered', 'paid'])
            ->sum('total_price');

        $thisWeekRevenue = Order::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->whereIn('order_status', ['completed', 'delivered', 'paid'])
            ->sum('total_price');

        $thisMonthRevenue = Order::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->whereIn('order_status', ['completed', 'delivered', 'paid'])
            ->sum('total_price');

        // Sales chart data (last 7 days)
        $salesChartData = [];
        $salesChartLabels = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dailyRevenue = Order::whereDate('created_at', $date)
                ->whereIn('order_status', ['completed', 'delivered', 'paid'])
                ->sum('total_price');
            
            $salesChartLabels[] = $date->format('M d');
            $salesChartData[] = (float) $dailyRevenue;
        }

        // Orders chart data (last 7 days)
        $ordersChartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dailyOrders = Order::whereDate('created_at', $date)->count();
            $ordersChartData[] = $dailyOrders;
        }

        // Top selling products this month
        $topProductsThisMonth = \DB::table('order_product')
            ->join('products', 'order_product.product_id', '=', 'products.id')
            ->join('orders', 'order_product.order_id', '=', 'orders.id')
            ->whereMonth('orders.created_at', Carbon::now()->month)
            ->whereYear('orders.created_at', Carbon::now()->year)
            ->whereIn('orders.order_status', ['completed', 'delivered', 'paid'])
            ->select('products.name', \DB::raw('SUM(order_product.quantity) as total_sold'), \DB::raw('SUM(order_product.quantity * products.price) as total_revenue'))
            ->groupBy('products.name')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        // Order type distribution
        $onlineOrdersCount = Order::where('type', 'online')->count();
        $walkinOrdersCount = Order::where('type', 'walkin')->count();

            return view('admin.dashboard', [
            'pendingOrdersCount' => $orderCounts['pending'],
            'approvedOrdersCount' => $orderCounts['approved'],
            'onDeliveryCount' => $orderCounts['on_delivery'],
            'completedTodayCount' => $orderCounts['completed_today'],
            'totalOrders' => $totalOrders,
            'totalProducts' => $totalProducts,
            'totalCustomers' => $totalCustomers,
            'popularProducts' => $popularProducts,
            'totalMovementsToday' => $totalMovementsToday,
            'recentMovements' => $recentMovements,
            'restockProducts' => $restockProducts,
            'todayRevenue' => $todayRevenue,
            'thisWeekRevenue' => $thisWeekRevenue,
            'thisMonthRevenue' => $thisMonthRevenue,
            'salesChartLabels' => $salesChartLabels,
            'salesChartData' => $salesChartData,
            'ordersChartData' => $ordersChartData,
            'topProductsThisMonth' => $topProductsThisMonth,
            'onlineOrdersCount' => $onlineOrdersCount,
            'walkinOrdersCount' => $walkinOrdersCount,
        ]);
        } catch (\Exception $e) {
            \Log::error('Admin dashboard error', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('admin.dashboard')->with('error', 'An error occurred loading the dashboard. Please try again.');
        }
    }

    public function chatbox(Request $request)
    {
        $users = \App\Models\User::where('id', '!=', auth()->id())->get();
        $selectedUserId = $request->input('user_id') ?? $users->first()->id ?? null;
        $messages = [];
        if ($selectedUserId) {
            $messages = \App\Models\Message::where(function($q) use ($selectedUserId) {
                $q->where('sender_id', auth()->id())->where('receiver_id', $selectedUserId);
            })->orWhere(function($q) use ($selectedUserId) {
                $q->where('sender_id', $selectedUserId)->where('receiver_id', auth()->id());
            })->orderBy('created_at')->get();
        }
        return view('admin.chatbox', compact('users', 'selectedUserId', 'messages'));
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string',
        ]);
        \App\Models\Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
        ]);
        return redirect()->route('admin.chatbox', ['user_id' => $request->receiver_id]);
    }


    /**
     * Approve an order
     */
    public function approveOrder(Request $request, $orderId)
    {
        try {
            $order = Order::findOrFail($orderId);
            $orderStatusService = new OrderStatusService();
            
            if ($orderStatusService->approveOrder($order, auth()->id())) {
                return response()->json([
                    'success' => true,
                    'message' => 'Order approved successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to approve order'
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error approving order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Assign driver to order
     */
    public function assignDriver(Request $request, $orderId)
    {
        $request->validate([
            'driver_id' => 'required|exists:users,id'
        ]);

        try {
            $order = Order::findOrFail($orderId);
            $orderStatusService = new OrderStatusService();
            
            if ($orderStatusService->assignDriver($order, $request->driver_id, auth()->id())) {
                return response()->json([
                    'success' => true,
                    'message' => 'Driver assigned successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to assign driver'
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error assigning driver: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Complete an order
     */
    public function completeOrder(Request $request, $orderId)
    {
        try {
            $order = Order::findOrFail($orderId);
            $orderStatusService = new OrderStatusService();
            
            if ($orderStatusService->completeOrder($order, auth()->id())) {
                return response()->json([
                    'success' => true,
                    'message' => 'Order completed successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to complete order'
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error completing order: ' . $e->getMessage()
            ], 500);
        }
    }

    public function assignDelivery(Request $request, Order $order)
    {
        $request->validate([
            'driver_id' => 'required|exists:users,id'
        ]);

        try {
            $orderStatusService = new OrderStatusService();
            
            if ($orderStatusService->assignDriver($order, $request->driver_id, auth()->id())) {
                return redirect()->back()->with('success', 'Driver assigned successfully! Order is now on delivery.');
            } else {
                return redirect()->back()->with('error', 'Failed to assign driver. Please try again.');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error assigning driver: ' . $e->getMessage());
        }
    }
} 