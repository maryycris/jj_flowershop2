<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InvoiceService
{
    /**
     * Generate invoice for an order after validation
     */
    public function generateInvoice(Order $order, $generatedBy)
    {
        DB::beginTransaction();
        try {
            // Update order with invoice generation details
            $order->update([
                'invoice_status' => 'ready',
                'invoice_generated_at' => now(),
            ]);

            // Create status history
            $order->statusHistories()->create([
                'status' => 'invoice_generated',
                'notes' => 'Invoice generated after order validation',
                'changed_by' => $generatedBy,
            ]);

            // Log invoice generation
            Log::info("Invoice generated for order {$order->id} by user {$generatedBy}");

            DB::commit();
            
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to generate invoice for order {$order->id}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get invoice data for display/download
     */
    public function getInvoiceData(Order $order)
    {
        $order->load(['user', 'products', 'delivery', 'paymentTracking']);
        
        // Calculate totals
        $subtotal = $order->products->sum(function($product) {
            return $product->pivot->quantity * $product->price;
        });
        
        $shippingFee = $order->delivery->shipping_fee ?? 0;
        if ($shippingFee == 0 && $order->total_price > $subtotal) {
            $shippingFee = $order->total_price - $subtotal;
        }
        
        $total = $subtotal + $shippingFee;
        
        return [
            'order' => $order,
            'subtotal' => $subtotal,
            'shipping_fee' => $shippingFee,
            'total' => $total,
            'invoice_number' => 'INV-' . str_pad($order->id, 6, '0', STR_PAD_LEFT),
            'generated_date' => now()->format('M d, Y'),
        ];
    }
}
