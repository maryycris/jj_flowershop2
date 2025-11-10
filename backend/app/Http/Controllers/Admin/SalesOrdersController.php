<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class SalesOrdersController extends Controller
{
    /**
     * Display the unified sales orders page with tabs
     */
    public function index(Request $request)
    {
        // Handle legacy route parameters
        $activeTab = $request->get('tab', $request->get('type', 'online'));
        $search = $request->get('search');
        $status = $request->get('status', 'pending');
        
        // Get online orders
        $onlineOrdersQuery = Order::with(['user', 'products'])->where('type', 'online');
        if ($search) {
            $onlineOrdersQuery->where(function($q) use ($search) {
                $q->whereHas('user', function($uq) use ($search) {
                    $uq->where('name', 'like', "%$search%");
                })
                ->orWhere('id', 'like', "%$search%");
            });
        }
        
        if ($status) {
            $onlineOrdersQuery->where(function($qq) use ($status) {
                $qq->where('order_status', $status)
                   ->orWhere(function($sub) use ($status){
                       $sub->whereNull('order_status')->where('status', $status);
                   });
            });
        }
        
        $onlineOrders = $onlineOrdersQuery->latest()->get();
        
        // Get walk-in orders
        $walkInOrdersQuery = Order::with(['user', 'products'])->where('type', 'walk-in')
            ->whereIn('status', ['quotation', 'validated', 'done', 'approved']);
        if ($search) {
            $walkInOrdersQuery->where(function($q) use ($search) {
                $q->whereHas('user', function($uq) use ($search) {
                    $uq->where('name', 'like', "%$search%");
                })
                ->orWhere('id', 'like', "%$search%");
            });
        }
        $walkInOrders = $walkInOrdersQuery->latest()->get();
        
        // Get completed orders for history with pagination
        $completedOrdersQuery = Order::whereIn('order_status', ['completed', 'delivered'])
            ->orWhere('status', 'completed')
            ->with(['user', 'assignedDriver', 'delivery.driver', 'products']);
        
        // Search functionality for completed orders
        if ($search) {
            $completedOrdersQuery->where(function($q) use ($search) {
                $q->whereHas('user', function($uq) use ($search) {
                    $uq->where('name', 'like', "%$search%");
                })
                ->orWhere('id', 'like', "%$search%");
            });
        }
        
        $completedOrders = $completedOrdersQuery->orderBy('updated_at', 'desc')->paginate(5);
        
        return view('admin.sales-orders.index', compact(
            'onlineOrders', 
            'walkInOrders', 
            'completedOrders',
            'activeTab',
            'search',
            'status'
        ));
    }
    
    /**
     * Calculate return rate percentage
     */
    private function calculateReturnRate($startDate)
    {
        $totalOrders = Order::where('created_at', '>=', $startDate)->count();
        $returnedOrders = Order::where('order_status', 'returned')->where('returned_at', '>=', $startDate)->count();
        
        if ($totalOrders == 0) return 0;
        return round(($returnedOrders / $totalOrders) * 100, 2);
    }
    
    /**
     * Calculate average return time in days
     */
    private function calculateAvgReturnTime($startDate)
    {
        $returns = Order::where('order_status', 'returned')
            ->where('returned_at', '>=', $startDate)
            ->whereNotNull('returned_at')
            ->get();
            
        if ($returns->isEmpty()) return 0;
        
        $totalDays = $returns->sum(function($order) {
            return $order->created_at->diffInDays($order->returned_at);
        });
        
        return round($totalDays / $returns->count(), 1);
    }
    
    /**
     * Get top return reasons
     */
    private function getTopReturnReasons($startDate)
    {
        return Order::where('order_status', 'returned')
            ->where('returned_at', '>=', $startDate)
            ->whereNotNull('return_reason')
            ->selectRaw('return_reason, COUNT(*) as count')
            ->groupBy('return_reason')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get();
    }
    
    /**
     * Get monthly returns data
     */
    private function getMonthlyReturns($startDate)
    {
        return Order::where('order_status', 'returned')
            ->where('returned_at', '>=', $startDate)
            ->selectRaw('DATE_FORMAT(returned_at, "%Y-%m") as month, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }
    
    /**
     * Display a specific sales order
     */
    public function show($orderId)
    {
        $order = Order::with(['user', 'products', 'salesOrder', 'delivery', 'assignedDriver'])
            ->findOrFail($orderId);
            
        // Get the sales order if it exists
        $salesOrder = $order->salesOrder;
        
        return view('admin.sales-orders.show', compact('order', 'salesOrder'));
    }
    
    /**
     * Confirm the sales order
     */
    public function confirm(Request $request, $orderId)
    {
        $order = Order::findOrFail($orderId);
        
        // Update sales order status to confirmed
        if ($order->salesOrder) {
            $order->salesOrder->update([
                'status' => 'confirmed',
                'confirmed_at' => now(),
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Sales Order confirmed successfully!',
                'sales_order' => $order->salesOrder,
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Sales Order not found',
        ], 404);
    }
}
