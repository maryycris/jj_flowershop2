<?php

namespace App\Services;

use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class InventoryManagementService
{
    /**
     * Create inventory movement for order validation
     */
    public function createOrderMovement(Order $order, $userId)
    {
        $movements = [];
        
        foreach ($order->products as $orderProduct) {
            $movementNumber = InventoryMovement::generateMovementNumber('OUT');
            
            $movement = InventoryMovement::create([
                'order_id' => $order->id,
                'product_id' => $orderProduct->id,
                'movement_type' => 'OUT',
                'movement_number' => $movementNumber,
                'quantity' => $orderProduct->pivot->quantity,
                'unit_cost' => $orderProduct->cost_price,
                'notes' => "Order #{$order->id} validation - {$orderProduct->name}",
                'user_id' => $userId,
                'reference_type' => 'order',
                'reference_id' => $order->id
            ]);
            
            // Update product stock
            $this->updateProductStock($orderProduct->id, -$orderProduct->pivot->quantity);
            
            $movements[] = $movement;
        }
        
        return $movements;
    }
    
    /**
     * Update product stock level
     */
    public function updateProductStock($productId, $quantityChange)
    {
        $product = Product::find($productId);
        if ($product) {
            $product->stock = max(0, $product->stock + $quantityChange);
            $product->save();
        }
    }
    
    /**
     * Get inventory movement for order
     */
    public function getOrderMovement(Order $order)
    {
        return InventoryMovement::where('order_id', $order->id)
            ->where('movement_type', 'OUT')
            ->first();
    }
    
    /**
     * Get preview movement number for order (before approval)
     */
    public function getPreviewMovementNumber()
    {
        return InventoryMovement::generateMovementNumber('OUT');
    }
    
    /**
     * Get all movements for order
     */
    public function getOrderMovements(Order $order)
    {
        return InventoryMovement::where('order_id', $order->id)
            ->with(['product', 'user'])
            ->get();
    }
    
    /**
     * Get current stock level for product
     */
    public function getProductStock($productId)
    {
        $product = Product::find($productId);
        return $product ? $product->stock : 0;
    }
    
    /**
     * Check if product has sufficient stock
     */
    public function hasSufficientStock($productId, $requiredQuantity)
    {
        $currentStock = $this->getProductStock($productId);
        return $currentStock >= $requiredQuantity;
    }
    
    /**
     * Get low stock products
     */
    public function getLowStockProducts($threshold = null)
    {
        $query = Product::where('stock', '<=', 'reorder_min')
            ->where('reorder_min', '>', 0);
            
        if ($threshold) {
            $query->where('stock', '<=', $threshold);
        }
        
        return $query->get();
    }
    
    /**
     * Get inventory movement history
     */
    public function getMovementHistory($productId = null, $type = null, $limit = 50)
    {
        $query = InventoryMovement::with(['product', 'user', 'order']);
        
        if ($productId) {
            $query->where('product_id', $productId);
        }
        
        if ($type) {
            $query->where('movement_type', $type);
        }
        
        return $query->orderBy('created_at', 'desc')->limit($limit)->get();
    }
    
    /**
     * Get inventory summary
     */
    public function getInventorySummary()
    {
        return [
            'total_products' => Product::count(),
            'low_stock_products' => $this->getLowStockProducts()->count(),
            'out_of_stock' => Product::where('stock', 0)->count(),
            'total_movements_today' => InventoryMovement::whereDate('created_at', today())->count(),
            'total_out_movements_today' => InventoryMovement::where('movement_type', 'OUT')
                ->whereDate('created_at', today())->count()
        ];
    }
}
