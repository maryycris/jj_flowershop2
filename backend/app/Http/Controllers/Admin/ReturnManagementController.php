<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\NotificationService;

class ReturnManagementController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Display returned orders for admin review
     */
    public function index()
    {
        $returnedOrders = Order::where('order_status', 'returned')
            ->with(['user', 'returnedByDriver', 'products'])
            ->orderBy('returned_at', 'desc')
            ->paginate(15);
            
        // Get filtered orders for tabs
        $pendingOrders = Order::where('order_status', 'returned')
            ->where('return_status', 'pending')
            ->with(['user', 'returnedByDriver', 'products'])
            ->orderBy('returned_at', 'desc')
            ->paginate(15);
            
        $approvedOrders = Order::where('order_status', 'returned')
            ->where('return_status', 'approved')
            ->with(['user', 'returnedByDriver', 'products'])
            ->orderBy('returned_at', 'desc')
            ->paginate(15);

        $returnStats = [
            'total_returned' => Order::where('order_status', 'returned')->count(),
            'pending_review' => Order::where('order_status', 'returned')->where('return_status', 'pending')->count(),
            'approved' => Order::where('order_status', 'returned')->where('return_status', 'approved')->count(),
            'rejected' => Order::where('order_status', 'returned')->where('return_status', 'rejected')->count(),
            'resolved' => Order::where('order_status', 'returned')->where('return_status', 'resolved')->count(),
        ];

        return view('admin.returns.index', compact('returnedOrders', 'pendingOrders', 'approvedOrders', 'returnStats'));
    }

    /**
     * Show specific returned order details
     */
    public function show(Order $order)
    {
        if ($order->order_status !== 'returned') {
            abort(404, 'Order not found or not returned.');
        }

        $order->load(['user', 'returnedByDriver', 'products', 'delivery', 'statusHistories']);

        return view('admin.returns.show', compact('order'));
    }

    /**
     * Update return status (approve/reject)
     */
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'return_status' => 'required|in:approved,rejected,resolved',
            'admin_notes' => 'nullable|string|max:1000'
        ]);

        if ($order->order_status !== 'returned') {
            return response()->json([
                'success' => false,
                'message' => 'Order is not in returned status.'
            ], 400);
        }

        try {
            $oldStatus = $order->return_status;
            $order->update([
                'return_status' => $request->return_status,
                'admin_notes' => $request->admin_notes
            ]);

            // Create status history entry
            $order->statusHistories()->create([
                'status' => 'return_status_updated',
                'message' => "Return status changed from {$oldStatus} to {$request->return_status}. Admin notes: " . ($request->admin_notes ?? 'None'),
                'changed_by' => auth()->id(),
                'changed_at' => now()
            ]);

            // Send notifications based on status
            $this->sendStatusUpdateNotifications($order, $request->return_status);

            return response()->json([
                'success' => true,
                'message' => 'Return status updated successfully.',
                'new_status' => $request->return_status
            ]);

        } catch (\Exception $e) {
            \Log::error('Error updating return status: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update return status. Please try again.'
            ], 500);
        }
    }

    /**
     * Process refund for approved returns
     */
    public function processRefund(Request $request, Order $order)
    {
        $request->validate([
            'refund_amount' => 'required|numeric|min:0|max:' . $order->total_price,
            'refund_reason' => 'required|string|max:255',
            'refund_method' => 'required|in:original_payment,store_credit,cash'
        ]);

        if ($order->return_status !== 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'Return must be approved before processing refund.'
            ], 400);
        }

        try {
            // Update order with refund information
            $order->update([
                'refund_amount' => $request->refund_amount,
                'refund_reason' => $request->refund_reason,
                'refund_method' => $request->refund_method,
                'refund_processed_at' => now(),
                'refund_processed_by' => auth()->id()
            ]);

            // Create status history entry
            $order->statusHistories()->create([
                'status' => 'refund_processed',
                'message' => "Refund processed: ₱{$request->refund_amount} via {$request->refund_method}. Reason: {$request->refund_reason}",
                'changed_by' => auth()->id(),
                'changed_at' => now()
            ]);

            // Send refund notification to customer
            $this->sendRefundNotification($order);

            return response()->json([
                'success' => true,
                'message' => 'Refund processed successfully.',
                'refund_amount' => $request->refund_amount
            ]);

        } catch (\Exception $e) {
            \Log::error('Error processing refund: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to process refund. Please try again.'
            ], 500);
        }
    }

    /**
     * Send notifications based on return status update
     */
    private function sendStatusUpdateNotifications(Order $order, string $status)
    {
        try {
            $customer = $order->user;
            
            if ($status === 'approved') {
                // Notify customer that return was approved
                $this->notificationService->sendReturnApprovedNotification($order, $customer);
            } elseif ($status === 'rejected') {
                // Notify customer that return was rejected
                $this->notificationService->sendReturnRejectedNotification($order, $customer);
            } elseif ($status === 'resolved') {
                // Notify customer that return was resolved
                $this->notificationService->sendReturnResolvedNotification($order, $customer);
            }

        } catch (\Exception $e) {
            \Log::error('Error sending status update notifications: ' . $e->getMessage());
        }
    }

    /**
     * Send refund notification to customer
     */
    private function sendRefundNotification(Order $order)
    {
        try {
            $customer = $order->user;
            $this->notificationService->sendRefundProcessedNotification($order, $customer);
        } catch (\Exception $e) {
            \Log::error('Error sending refund notification: ' . $e->getMessage());
        }
    }

    /**
     * Display return analytics dashboard
     */
    public function analytics(Request $request)
    {
        $days = $request->get('days', 30);
        $startDate = now()->subDays($days);
        
        // Get return metrics
        $metrics = $this->getReturnMetrics($startDate);
        
        // Get chart data
        $charts = $this->getChartData($startDate);
        
        // Get driver performance data
        $driverPerformance = $this->getDriverPerformance($startDate);
        
        // Get customer patterns
        $customerPatterns = $this->getCustomerPatterns($startDate);
        
        if ($request->wantsJson()) {
            return response()->json([
                'metrics' => $metrics,
                'charts' => $charts,
                'driverPerformance' => $driverPerformance,
                'customerPatterns' => $customerPatterns
            ]);
        }
        
        return view('admin.returns.analytics', compact('metrics', 'charts', 'driverPerformance', 'customerPatterns'));
    }

    /**
     * Export analytics data
     */
    public function exportAnalytics(Request $request)
    {
        $days = $request->get('days', 30);
        $startDate = now()->subDays($days);
        
        // Generate CSV export
        $filename = "return_analytics_{$days}_days_" . now()->format('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\""
        ];
        
        $callback = function() use ($startDate) {
            $file = fopen('php://output', 'w');
            
            // Headers
            fputcsv($file, ['Order ID', 'Customer', 'Driver', 'Return Reason', 'Return Date', 'Amount', 'Status', 'Resolution Time']);
            
            // Data
            $returns = Order::where('order_status', 'returned')
                ->where('returned_at', '>=', $startDate)
                ->with(['user', 'returnedByDriver'])
                ->get();
                
            foreach ($returns as $order) {
                $resolutionTime = $order->refund_processed_at ? 
                    $order->returned_at->diffInHours($order->refund_processed_at) : 
                    $order->returned_at->diffInHours(now());
                    
                fputcsv($file, [
                    $order->id,
                    $order->user->name,
                    $order->returnedByDriver->name ?? 'N/A',
                    $order->return_reason,
                    $order->returned_at->format('Y-m-d H:i'),
                    $order->total_price,
                    $order->return_status,
                    $resolutionTime . ' hours'
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get return metrics
     */
    private function getReturnMetrics($startDate)
    {
        $totalReturns = Order::where('order_status', 'returned')
            ->where('returned_at', '>=', $startDate)
            ->count();
            
        $totalOrders = Order::where('created_at', '>=', $startDate)->count();
        $returnRate = $totalOrders > 0 ? round(($totalReturns / $totalOrders) * 100, 2) : 0;
        
        $totalRefundAmount = Order::where('order_status', 'returned')
            ->where('returned_at', '>=', $startDate)
            ->whereNotNull('refund_amount')
            ->sum('refund_amount');
            
        $avgResolutionTime = Order::where('order_status', 'returned')
            ->where('returned_at', '>=', $startDate)
            ->whereNotNull('refund_processed_at')
            ->get()
            ->avg(function($order) {
                return $order->returned_at->diffInHours($order->refund_processed_at);
            }) ?? 0;
            
        return [
            'totalReturns' => $totalReturns,
            'returnRate' => $returnRate,
            'totalRefundAmount' => $totalRefundAmount,
            'avgResolutionTime' => round($avgResolutionTime, 1)
        ];
    }

    /**
     * Get chart data
     */
    private function getChartData($startDate)
    {
        // Return reasons distribution
        $reasons = Order::where('order_status', 'returned')
            ->where('returned_at', '>=', $startDate)
            ->selectRaw('return_reason, COUNT(*) as count')
            ->groupBy('return_reason')
            ->orderBy('count', 'desc')
            ->get();
            
        $returnReasons = [
            'labels' => $reasons->pluck('return_reason')->toArray(),
            'data' => $reasons->pluck('count')->toArray()
        ];
        
        // Return trends over time
        $trends = Order::where('order_status', 'returned')
            ->where('returned_at', '>=', $startDate)
            ->selectRaw('DATE(returned_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
            
        $returnTrends = [
            'labels' => $trends->pluck('date')->map(function($date) {
                return \Carbon\Carbon::parse($date)->format('M d');
            })->toArray(),
            'data' => $trends->pluck('count')->toArray()
        ];
        
        return [
            'returnReasons' => $returnReasons,
            'returnTrends' => $returnTrends
        ];
    }

    /**
     * Get driver performance data
     */
    private function getDriverPerformance($startDate)
    {
        $drivers = \App\Models\User::where('role', 'driver')
            ->whereHas('orders', function($query) use ($startDate) {
                $query->where('created_at', '>=', $startDate);
            })
            ->with(['orders' => function($query) use ($startDate) {
                $query->where('created_at', '>=', $startDate);
            }])
            ->get();
            
        return $drivers->map(function($driver) use ($startDate) {
            $totalDeliveries = $driver->orders->where('status', 'on_delivery')->count() + 
                              $driver->orders->where('status', 'completed')->count() +
                              $driver->orders->where('order_status', 'returned')->count();
                              
            $returns = $driver->orders->where('order_status', 'returned')->count();
            $returnRate = $totalDeliveries > 0 ? round(($returns / $totalDeliveries) * 100, 2) : 0;
            
            $mostCommonReason = $driver->orders->where('order_status', 'returned')
                ->groupBy('return_reason')
                ->map->count()
                ->sortDesc()
                ->keys()
                ->first() ?? 'N/A';
                
            $score = max(0, 100 - ($returnRate * 2)); // Simple scoring algorithm
            
            return [
                'name' => $driver->name,
                'totalDeliveries' => $totalDeliveries,
                'returns' => $returns,
                'returnRate' => $returnRate,
                'mostCommonReason' => $mostCommonReason,
                'score' => round($score)
            ];
        })->sortByDesc('returnRate')->values();
    }

    /**
     * Get customer return patterns
     */
    private function getCustomerPatterns($startDate)
    {
        $customers = \App\Models\User::where('role', 'customer')
            ->whereHas('orders', function($query) use ($startDate) {
                $query->where('created_at', '>=', $startDate);
            })
            ->with(['orders' => function($query) use ($startDate) {
                $query->where('created_at', '>=', $startDate);
            }])
            ->get();
            
        return $customers->map(function($customer) use ($startDate) {
            $totalOrders = $customer->orders->count();
            $returns = $customer->orders->where('order_status', 'returned')->count();
            $returnRate = $totalOrders > 0 ? round(($returns / $totalOrders) * 100, 2) : 0;
            
            $lastReturn = $customer->orders->where('order_status', 'returned')
                ->sortByDesc('returned_at')
                ->first();
            $lastReturnDate = $lastReturn ? $lastReturn->returned_at->format('M d, Y') : 'Never';
            
            $riskLevel = $returnRate > 20 ? 'high' : ($returnRate > 10 ? 'medium' : 'low');
            
            return [
                'name' => $customer->name,
                'totalOrders' => $totalOrders,
                'returns' => $returns,
                'returnRate' => $returnRate,
                'lastReturn' => $lastReturnDate,
                'riskLevel' => $riskLevel
            ];
        })->sortByDesc('returnRate')->values();
    }
}