<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Order;
use App\Models\InventoryTransaction;
use Illuminate\Support\Facades\Log;

class InventoryService
{
    /**
     * Update inventory when order is placed (reserve stock only)
     */
    public function updateInventoryOnOrder(Order $order)
    {
        try {
            $order->load('products');
            
            foreach ($order->products as $product) {
                $quantity = $product->pivot->quantity;
                
                // Only log the order - don't decrease stock yet
                $this->logInventoryTransaction($product, $quantity, 'ordered', $order->id);
                
                Log::info("Order placed - stock reserved for order {$order->id}", [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity_ordered' => $quantity,
                    'current_stock' => $product->stock
                ]);
            }
            
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to log order for inventory tracking {$order->id}", [
                'error' => $e->getMessage(),
                'order_id' => $order->id
            ]);
                return false;
            }
    }
    
    /**
     * Update inventory when order is delivered (no stock change yet)
     */
    public function updateInventoryOnDelivery(Order $order)
    {
        try {
            $order->load('products');
            
            foreach ($order->products as $product) {
                $quantity = $product->pivot->quantity;
                
                // Log delivery - no stock change yet
                $this->logInventoryTransaction($product, $quantity, 'delivered', $order->id);
                
                Log::info("Order delivered - waiting for customer confirmation {$order->id}", [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity_delivered' => $quantity
                ]);
            }
            
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to log delivery for order {$order->id}", [
                'error' => $e->getMessage(),
                'order_id' => $order->id
            ]);
            return false;
        }
    }
    
    /**
     * Update inventory when order is received by customer (ACTUAL STOCK DECREASE)
     */
    public function updateInventoryOnReceived(Order $order)
    {
        try {
            $order->load('products');
            
            foreach ($order->products as $product) {
                $quantity = $product->pivot->quantity;

                // If this purchased item corresponds to a CatalogProduct with compositions,
                // consume the component materials instead of the finished product stock.
                $catalog = \App\Models\CatalogProduct::where('name', $product->name)
                    ->where('category', $product->category)
                    ->first();

                if ($catalog && $catalog->compositions && $catalog->compositions()->count() > 0) {
                    foreach ($catalog->compositions as $comp) {
                        $component = \App\Models\Product::find($comp->component_id);
                        if (!$component) { continue; }
                        // Idempotency: if we already logged a consumption for this order+product, skip
                        $already = \App\Models\InventoryTransaction::where('order_id', $order->id)
                            ->where('product_id', $component->id)
                            ->where('type', 'consumed')
                            ->exists();
                        if ($already) { continue; }
                        $consumeQty = (int) $comp->quantity * (int) $quantity; // total consumption
                        // Decrease component stock and track consumed
                        $component->decrement('stock', $consumeQty);
                        $component->increment('qty_consumed', $consumeQty);
                        $this->logInventoryTransaction($component, $consumeQty, 'consumed', $order->id);
                    }

                    Log::info("Customer received order {$order->id} - components consumed via composition", [
                        'catalog_product_id' => $catalog->id,
                        'product_name' => $product->name,
                        'order_quantity' => $quantity
                    ]);
                } else {
                    // Fallback: treat as standalone product sale
                    $alreadySold = \App\Models\InventoryTransaction::where('order_id', $order->id)
                        ->where('product_id', $product->id)
                        ->where('type', 'sold')
                        ->exists();
                    if (!$alreadySold) {
                        $product->decrement('stock', $quantity);
                        $product->increment('qty_sold', $quantity);
                        $this->logInventoryTransaction($product, $quantity, 'sold', $order->id);
                    }

                    Log::info("Customer received order {$order->id} - inventory updated (standalone)", [
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'quantity_sold' => $quantity,
                        'remaining_stock' => $product->fresh()->stock
                    ]);
                }
            }
            
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to update inventory for received order {$order->id}", [
                'error' => $e->getMessage(),
                'order_id' => $order->id
            ]);
            return false;
        }
    }

    /**
     * Check for low stock and generate alerts
     */
    public function checkLowStock()
    {
        $lowStockProducts = Product::whereColumn('stock', '<=', 'reorder_min')
            ->where('reorder_min', '>', 0)
            ->get();
            
        $alerts = [];
        
        foreach ($lowStockProducts as $product) {
            $needed = $product->reorder_max - $product->stock;
            $alerts[] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'current_stock' => $product->stock,
                'reorder_min' => $product->reorder_min,
                'reorder_max' => $product->reorder_max,
                'qty_to_purchase' => max(0, $needed),
                'category' => $product->category
            ];
        }
        
        return $alerts;
    }
    
    /**
     * Get products that need restocking
     */
    public function getRestockRecommendations()
    {
        return Product::whereColumn('stock', '<=', 'reorder_min')
            ->where('reorder_min', '>', 0)
            ->select([
                'id', 'name', 'category', 'stock', 'reorder_min', 'reorder_max',
                'price', 'cost_price'
            ])
            ->get()
            ->map(function ($product) {
                $needed = $product->reorder_max - $product->stock;
                $product->qty_to_purchase = max(0, $needed);
                $product->estimated_cost = $product->cost_price ? $product->cost_price * $product->qty_to_purchase : 0;
                return $product;
            });
    }
    
    /**
     * Log inventory transactions
     */
    private function logInventoryTransaction(Product $product, int $quantity, string $type, int $orderId)
    {
        InventoryTransaction::create([
            'product_id' => $product->id,
            'order_id' => $orderId,
            'quantity' => $quantity,
            'type' => $type, // 'sold', 'consumed', 'completed', 'damaged', 'returned'
            'stock_before' => $product->stock + $quantity,
            'stock_after' => $product->stock,
            'created_by' => auth()->id()
        ]);
    }
    
    /**
     * Restore inventory when order is cancelled
     */
    public function restoreInventoryOnCancellation(Order $order)
    {
        try {
            $order->load('products');
            
            foreach ($order->products as $product) {
                $quantity = $product->pivot->quantity;
                
                // Restore stock
                $product->increment('stock', $quantity);
                
                // Decrease qty_sold
                $product->decrement('qty_sold', $quantity);
                
                // Log inventory transaction
                $this->logInventoryTransaction($product, $quantity, 'returned', $order->id);
                
                Log::info("Inventory restored for cancelled order {$order->id}", [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity_restored' => $quantity,
                    'new_stock' => $product->fresh()->stock
                ]);
            }
            
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to restore inventory for cancelled order {$order->id}", [
                'error' => $e->getMessage(),
                'order_id' => $order->id
            ]);
            return false;
        }
    }
}