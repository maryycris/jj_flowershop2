<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PendingInventoryChange;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminInventoryController extends Controller
{
    public function index()
    {
        $pendingChanges = PendingInventoryChange::with(['product', 'submittedBy'])
            ->pending()
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.inventory.index', compact('pendingChanges'));
    }

    public function approve(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $pendingChange = PendingInventoryChange::findOrFail($id);
            
            if ($pendingChange->action === 'edit') {
                // Apply the changes to the actual product
                $product = Product::findOrFail($pendingChange->product_id);
                $changes = $pendingChange->changes;
                
                foreach ($changes as $field => $value) {
                    if (in_array($field, ['name', 'category', 'price', 'cost_price', 'reorder_min', 'reorder_max', 'stock', 'qty_consumed', 'qty_damaged', 'qty_sold'])) {
                        $product->$field = $value;
                    }
                }
                $product->save();
                
            } elseif ($pendingChange->action === 'delete') {
                // Delete the product
                $product = Product::findOrFail($pendingChange->product_id);
                $product->delete();
            }

            // Mark the pending change as approved
            $pendingChange->update([
                'status' => 'approved',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
                'admin_notes' => $request->input('admin_notes', '')
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Inventory change approved successfully.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error approving change: ' . $e->getMessage()
            ], 500);
        }
    }

    public function reject(Request $request, $id)
    {
        try {
            $pendingChange = PendingInventoryChange::findOrFail($id);
            
            $pendingChange->update([
                'status' => 'rejected',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
                'admin_notes' => $request->input('admin_notes', '')
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Inventory change rejected successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error rejecting change: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getPendingCount()
    {
        $count = PendingInventoryChange::pending()->count();
        return response()->json(['count' => $count]);
    }
}