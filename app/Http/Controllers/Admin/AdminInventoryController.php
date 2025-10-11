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

    public function reports()
    {
        // Get inventory history reports
        // For now, return sample data - replace with actual database queries
        $inventoryHistory = collect([
            (object)['id' => 1, 'date' => '2025-10-10', 'user' => 'Admin', 'user_type' => 'admin'],
            (object)['id' => 2, 'date' => '2025-10-09', 'user' => 'Clerk', 'user_type' => 'clerk'],
            (object)['id' => 3, 'date' => '2025-10-08', 'user' => 'Admin', 'user_type' => 'admin'],
            (object)['id' => 4, 'date' => '2025-10-07', 'user' => 'Clerk', 'user_type' => 'clerk'],
            (object)['id' => 5, 'date' => '2025-10-06', 'user' => 'Admin', 'user_type' => 'admin'],
            (object)['id' => 6, 'date' => '2025-10-05', 'user' => 'Clerk', 'user_type' => 'clerk'],
            (object)['id' => 7, 'date' => '2025-10-04', 'user' => 'Admin', 'user_type' => 'admin'],
            (object)['id' => 8, 'date' => '2025-10-03', 'user' => 'Clerk', 'user_type' => 'clerk'],
        ]);

        // Get pending update requests from clerks
        $updateRequests = collect([
            (object)[
                'id' => 1,
                'clerk_name' => 'John Doe',
                'date' => '2025-10-10',
                'status' => 'pending',
                'changes' => [
                    (object)['type' => 'added', 'product_code' => '06001', 'name' => 'Rose'],
                    (object)['type' => 'edited', 'product_code' => '01002', 'name' => 'Tulip'],
                    (object)['type' => 'deleted', 'product_code' => '07003', 'name' => 'Yellow Tulip'],
                ]
            ]
        ]);

        return view('admin.inventory.reports', compact('inventoryHistory', 'updateRequests'));
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