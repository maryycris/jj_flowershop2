<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductComposition;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InventoryService
{
    /**
     * Deduct materials from inventory when a product is sold
     */
    public static function deductMaterialsForProduct(Product $product, int $quantity)
    {
        try {
            DB::beginTransaction();

            // Get the product composition (materials needed)
            $compositions = $product->compositions;
            
            if ($compositions->isEmpty()) {
                Log::info("No composition found for product: {$product->name}");
                DB::rollBack();
                return false;
            }

            $deductedMaterials = [];
            $insufficientMaterials = [];

            foreach ($compositions as $composition) {
                // Find the inventory item by component ID (more accurate than name matching)
                $inventoryItem = Product::where('id', $composition->component_id)
                    ->where('category', 'NOT LIKE', '%Office Supplies%') // Exclude office supplies
                    ->where('status', true)
                    ->first();

                if (!$inventoryItem) {
                    Log::warning("Inventory item not found for component ID: {$composition->component_id}");
                    $insufficientMaterials[] = [
                        'component' => $composition->component_name,
                        'needed' => $composition->quantity * $quantity,
                        'available' => 0,
                        'reason' => 'Item not found in inventory'
                    ];
                    continue;
                }

                $neededQuantity = $composition->quantity * $quantity;
                $availableQuantity = $inventoryItem->stock ?? 0;

                if ($availableQuantity < $neededQuantity) {
                    Log::warning("Insufficient stock for {$composition->component_name}. Needed: {$neededQuantity}, Available: {$availableQuantity}");
                    $insufficientMaterials[] = [
                        'component' => $composition->component_name,
                        'needed' => $neededQuantity,
                        'available' => $availableQuantity,
                        'reason' => 'Insufficient stock'
                    ];
                    continue;
                }

                // Deduct from inventory
                $inventoryItem->stock = max(0, $inventoryItem->stock - $neededQuantity);
                $inventoryItem->qty_consumed = ($inventoryItem->qty_consumed ?? 0) + $neededQuantity;
                $inventoryItem->save();

                $deductedMaterials[] = [
                    'component' => $composition->component_name,
                    'deducted' => $neededQuantity,
                    'remaining' => $inventoryItem->stock
                ];

                Log::info("Deducted {$neededQuantity} {$composition->component_name} from inventory. Remaining: {$inventoryItem->stock}");
            }

            // If there are insufficient materials, rollback and return error
            if (!empty($insufficientMaterials)) {
                DB::rollBack();
                return [
                    'success' => false,
                    'message' => 'Insufficient materials in inventory',
                    'insufficient_materials' => $insufficientMaterials
                ];
            }

            DB::commit();
            
            return [
                'success' => true,
                'message' => 'Materials successfully deducted from inventory',
                'deducted_materials' => $deductedMaterials
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error deducting materials for product {$product->name}: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error processing inventory deduction: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Check if there are enough materials in inventory for a product
     */
    public static function checkMaterialAvailability(Product $product, int $quantity)
    {
        $compositions = $product->compositions;
        $availability = [];

        foreach ($compositions as $composition) {
            $inventoryItem = Product::where('id', $composition->component_id)
                ->where('category', 'NOT LIKE', '%Office Supplies%')
                ->where('status', true)
                ->first();

            $neededQuantity = $composition->quantity * $quantity;
            $availableQuantity = $inventoryItem ? ($inventoryItem->stock ?? 0) : 0;

            $availability[] = [
                'component' => $composition->component_name,
                'needed' => $neededQuantity,
                'available' => $availableQuantity,
                'sufficient' => $availableQuantity >= $neededQuantity,
                'inventory_item' => $inventoryItem
            ];
        }

        return $availability;
    }

    /**
     * Get available inventory items for product composition
     */
    public static function getAvailableInventoryItems()
    {
        return Product::where('category', 'NOT LIKE', '%Office Supplies%')
            ->where('status', true)
            ->where('stock', '>', 0)
            ->orderBy('name')
            ->get()
            ->map(function($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'category' => $product->category,
                    'stock' => $product->stock ?? 0,
                    'price' => $product->price ?? 0
                ];
            })
            ->toArray();
    }
}
