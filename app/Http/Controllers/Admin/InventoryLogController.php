<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryLog;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class InventoryLogController extends Controller
{
    /**
     * Display a listing of inventory logs
     */
    public function index(Request $request)
    {
        $query = InventoryLog::with(['product', 'user']);

        // Filter by action
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by product
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search in description
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('description', 'like', "%{$searchTerm}%")
                  ->orWhereHas('product', function($productQuery) use ($searchTerm) {
                      $productQuery->where('name', 'like', "%{$searchTerm}%");
                  })
                  ->orWhereHas('user', function($userQuery) use ($searchTerm) {
                      $userQuery->where('name', 'like', "%{$searchTerm}%");
                  });
            });
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(20);

        // Get filter options
        $actions = InventoryLog::distinct()->pluck('action');
        $users = User::whereIn('id', InventoryLog::distinct()->pluck('user_id'))->get(['id', 'name']);
        $products = Product::whereIn('id', InventoryLog::distinct()->pluck('product_id'))->get(['id', 'name']);

        return view('admin.inventory-logs.index', compact('logs', 'actions', 'users', 'products'));
    }

    /**
     * Show details of a specific log entry
     */
    public function show(InventoryLog $inventoryLog)
    {
        $inventoryLog->load(['product', 'user']);
        return view('admin.inventory-logs.show', compact('inventoryLog'));
    }

    /**
     * Get logs for a specific product
     */
    public function productLogs(Product $product)
    {
        $logs = InventoryLog::with(['user'])
            ->where('product_id', $product->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.inventory-logs.product', compact('logs', 'product'));
    }

    /**
     * Export logs to CSV
     */
    public function export(Request $request)
    {
        $query = InventoryLog::with(['product', 'user']);

        // Apply same filters as index
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->orderBy('created_at', 'desc')->get();

        $filename = 'inventory_logs_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($logs) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'ID',
                'Date',
                'User',
                'Product',
                'Action',
                'Description',
                'IP Address',
                'Changes Summary'
            ]);

            // CSV data
            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->created_at->format('Y-m-d H:i:s'),
                    $log->user ? $log->user->name : 'Unknown',
                    $log->product ? $log->product->name : 'Unknown',
                    ucfirst($log->action),
                    $log->description,
                    $log->ip_address,
                    $log->changes_summary
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}