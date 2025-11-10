<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\InventoryLog;
use App\Models\PendingInventoryChange;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdminInventoryController extends Controller
{
    public function index()
    {
        // Get products for the inventory tab
        $products = Product::orderBy('created_at', 'desc')->get();

        // Admin inventory - no pending changes needed (direct actions only)

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
     * Approve all staged changes (edits and deletions)
     */
    public function approveChanges(Request $request)
    {
        try {
            $editedProducts = json_decode($request->edited_products, true) ?? [];
            $deletedProducts = json_decode($request->deleted_products, true) ?? [];
            $stagedEdits = json_decode($request->staged_edits, true) ?? [];
            
            \Log::info('Admin approving changes:', [
                'edited_products' => $editedProducts,
                'deleted_products' => $deletedProducts,
                'staged_edits' => $stagedEdits
            ]);
            
            DB::beginTransaction();
            
            // Process deletions
            foreach ($deletedProducts as $productId) {
                $product = Product::find($productId);
                if ($product) {
                    $product->delete();
                    \Log::info("Product deleted: {$product->name} (ID: {$productId})");
                    
                    // Mark pending change as approved
                    PendingInventoryChange::where('product_id', $productId)
                        ->where('action', 'delete')
                        ->where('status', 'pending')
                        ->update(['status' => 'approved', 'reviewed_by' => auth()->id(), 'reviewed_at' => now()]);
                }
            }
            
            // Process edits
            foreach ($stagedEdits as $productId => $editData) {
                $product = Product::find($productId);
                if ($product) {
                    // Sanitize the edit data to handle empty strings
                    $sanitizedData = [];
                    foreach ($editData as $key => $value) {
                        // Convert empty strings to 0 for numeric fields
                        if (in_array($key, ['qty_consumed', 'qty_damaged', 'qty_sold', 'stock', 'reorder_min', 'reorder_max'])) {
                            $sanitizedData[$key] = $value === '' || $value === null ? 0 : (int)$value;
                        } else {
                            $sanitizedData[$key] = $value;
                        }
                    }
                    
                    $product->update($sanitizedData);
                    \Log::info("Product updated: {$product->name} (ID: {$productId})", $sanitizedData);
                    
                    // Mark pending change as approved
                    PendingInventoryChange::where('product_id', $productId)
                        ->where('action', 'edit')
                        ->where('status', 'pending')
                        ->update(['status' => 'approved', 'reviewed_by' => auth()->id(), 'reviewed_at' => now()]);
                }
            }
            
            DB::commit();
            
            // Notify clerks about approved inventory changes
            // Group changes by submitted_by to send one notification per clerk
            $clerkChanges = [];
            $allChanges = \App\Models\PendingInventoryChange::whereIn('product_id', array_merge($deletedProducts, array_keys($stagedEdits)))
                ->where('status', 'approved')
                ->where('reviewed_at', '>=', now()->subSeconds(5)) // Recently approved in this batch
                ->get();
            
            foreach ($allChanges as $change) {
                if ($change->submitted_by) {
                    if (!isset($clerkChanges[$change->submitted_by])) {
                        $clerkChanges[$change->submitted_by] = 0;
                    }
                    $clerkChanges[$change->submitted_by]++;
                }
            }
            
            // Send notifications to clerks
            foreach ($clerkChanges as $clerkId => $changesCount) {
                try {
                    $clerk = \App\Models\User::find($clerkId);
                    if ($clerk && $clerk->role === 'clerk') {
                        $clerk->notify(new \App\Notifications\InventoryChangesApprovedNotification($changesCount));
                    }
                } catch (\Throwable $e) {
                    \Log::error("Failed to send inventory approval notification to clerk {$clerkId}: {$e->getMessage()}");
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => 'All changes have been approved and saved successfully!',
                'deleted_count' => count($deletedProducts),
                'edited_count' => count($stagedEdits)
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error approving changes: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error approving changes: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Approve a single pending change
     */
    public function approve($id)
    {
        try {
            $change = \App\Models\PendingInventoryChange::findOrFail($id);
            
            if ($change->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'This change has already been processed'
                ], 400);
            }
            
            // Update the change status to approved
            $change->update([
                'status' => 'approved',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now()
            ]);
            
            // Apply the changes to the actual product
            if ($change->action === 'edit' && $change->changes) {
                $product = \App\Models\Product::find($change->product_id);
                if ($product) {
                    // Sanitize the changes data to handle empty strings
                    $sanitizedChanges = [];
                    foreach ($change->changes as $key => $value) {
                        // Convert empty strings to 0 for numeric fields
                        if (in_array($key, ['qty_consumed', 'qty_damaged', 'qty_sold', 'stock', 'reorder_min', 'reorder_max'])) {
                            $sanitizedChanges[$key] = $value === '' || $value === null ? 0 : (int)$value;
                        } else {
                            $sanitizedChanges[$key] = $value;
                        }
                    }
                    
                    $product->update($sanitizedChanges);
                }
            } elseif ($change->action === 'delete') {
                $product = \App\Models\Product::find($change->product_id);
                if ($product) {
                    $product->delete();
                }
            }
            
            // Notify the clerk who submitted the change
            if ($change->submitted_by) {
                try {
                    $clerk = \App\Models\User::find($change->submitted_by);
                    if ($clerk && $clerk->role === 'clerk') {
                        $clerk->notify(new \App\Notifications\InventoryChangesApprovedNotification(1));
                    }
                } catch (\Throwable $e) {
                    \Log::error("Failed to send inventory approval notification to clerk: {$e->getMessage()}");
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Change approved successfully'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error approving change: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error approving change: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject a single pending change
     */
    public function reject($id)
    {
        try {
            $change = \App\Models\PendingInventoryChange::findOrFail($id);
            
            if ($change->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'This change has already been processed'
                ], 400);
            }
            
            // Update the change status to rejected
            $change->update([
                'status' => 'rejected',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now()
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Change rejected successfully'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error rejecting change: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error rejecting change: ' . $e->getMessage()
            ], 500);
        }
    }
}