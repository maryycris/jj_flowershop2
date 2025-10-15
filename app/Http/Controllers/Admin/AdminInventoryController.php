<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\InventoryLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdminInventoryController extends Controller
{
    public function index()
    {
        // Get products for the inventory tab
        $products = Product::orderBy('created_at', 'desc')->get();

        // Get pending inventory logs for the inventory logs tab
        $pendingLogs = InventoryLog::with(['product','user'])
            ->where('status', 'pending')
            ->orderBy('created_at','desc')
            ->get();

        // Group pending logs by category for the UI tabs
        $categories = [
            'Fresh Flowers', 'Dried Flowers', 'Artificial Flowers', 'Greenery',
            'Floral Supplies', 'Packaging Materials', 'Wrappers', 'Ribbon', 'Other Offers'
        ];

        $logsByCategory = [];
        foreach ($categories as $cat) { 
            $logsByCategory[$cat] = collect(); 
        }

        foreach ($pendingLogs as $log) {
            $cat = 'Other Offers'; // Default category
            
            if ($log->action === 'create') {
                $nv = (array)($log->new_values ?? []);
                $cat = $nv['category'] ?? 'Other Offers';
            } elseif ($log->product) {
                $cat = $log->product->category ?? 'Other Offers';
            }
            
            // Ensure the category exists in our categories array
            if (!in_array($cat, $categories)) {
                $cat = 'Other Offers';
            }
            
            $logsByCategory[$cat] = ($logsByCategory[$cat] ?? collect())->push($log);
        }
        

        return view('admin.inventory', compact('products', 'pendingLogs', 'logsByCategory', 'categories'));
    }


    public function getPendingCount()
    {
        $count = InventoryLog::where('status', 'pending')->count();
        return response()->json(['count' => $count]);
    }

    /**
     * Show inventory reports
     */
    public function reports(Request $request)
    {
        $inventoryService = new \App\Services\InventoryManagementService();
        
        // Get summary data
        $summary = $inventoryService->getInventorySummary();
        
        // Get all products for filter
        $products = Product::orderBy('name')->get();
        
        // Build movement history query
        $movementsQuery = \App\Models\InventoryMovement::with(['product', 'user', 'order'])
            ->orderBy('created_at', 'desc');
        
        // Apply filters
        if ($request->filled('product_id')) {
            $movementsQuery->where('product_id', $request->product_id);
        }
        
        if ($request->filled('movement_type')) {
            $movementsQuery->where('movement_type', $request->movement_type);
        }
        
        if ($request->filled('date_from')) {
            $movementsQuery->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $movementsQuery->whereDate('created_at', '<=', $request->date_to);
        }
        
        // Paginate results
        $movements = $movementsQuery->paginate(50);
        
        return view('admin.inventory.reports', compact('summary', 'products', 'movements'));
    }
}