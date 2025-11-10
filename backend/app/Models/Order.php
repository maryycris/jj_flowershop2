<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'total_price',
        'status',
        'notes',
        'selected_cart_item_ids',
        'payment_status',
        'payment_method',
        'type',
        'order_status',
        'approved_at',
        'on_delivery_at',
        'completed_at',
        'approved_by',
        'assigned_driver_id',
        'return_reason',
        'return_notes',
        'returned_at',
        'returned_by',
        'return_status',
        'refund_amount',
        'refund_reason',
        'refund_method',
        'refund_processed_at',
        'refund_processed_by',
        'admin_notes',
        'store_credit_used',
        'store_credit_order_id',
        'invoice_status',
        'invoice_generated_at',
        'invoice_paid_at',
        'paymongo_source_id',
        'paymongo_checkout_session_id',
        'paymongo_payment_id',
        'returned_at',
        'return_reason',
        'returned_by',
    ];

    protected $casts = [
        'selected_cart_item_ids' => 'array',
        'total_price' => 'decimal:2',
        'completed_at' => 'datetime',
        'approved_at' => 'datetime',
        'on_delivery_at' => 'datetime',
        'invoice_generated_at' => 'datetime',
        'invoice_paid_at' => 'datetime',
        'returned_at' => 'datetime',
        'refund_amount' => 'decimal:2',
        'refund_processed_at' => 'datetime',
        'store_credit_used' => 'decimal:2',
    ];

    /**
     * Get the delivery associated with the order.
     */
    public function delivery(): HasOne
    {
        return $this->hasOne(Delivery::class);
    }

    /**
     * Get the user that owns the order.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the products for the order.
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'order_product')
            ->withPivot('quantity', 'rating', 'review_comment', 'reviewed', 'reviewed_at')
            ->withTimestamps();
    }

    /**
     * Get the custom bouquets for the order.
     */
    public function customBouquets(): BelongsToMany
    {
        return $this->belongsToMany(CustomBouquet::class, 'order_custom_bouquet')
            ->withPivot('quantity', 'rating', 'review_comment', 'reviewed', 'reviewed_at')
            ->withTimestamps();
    }

    /**
     * Get the sales order for this order.
     */
    public function salesOrder(): HasOne
    {
        return $this->hasOne(\App\Models\SalesOrder::class);
    }

    /**
     * Get the calculated total price of the order (from products).
     */
    public function getCalculatedTotalPriceAttribute()
    {
        return $this->products->sum(function($product) {
            return $product->pivot->quantity * $product->price;
        });
    }

    public function paymentProofs()
    {
        return $this->hasMany(\App\Models\PaymentProof::class);
    }

    public function statusHistories()
    {
        return $this->hasMany(OrderStatusHistory::class);
    }

    /**
     * Get the user who approved the order.
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the driver who returned the order.
     */
    public function returnedByDriver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'returned_by');
    }

    /**
     * Get the assigned driver for the order.
     */
    public function assignedDriver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_driver_id');
    }

    /**
     * Get the invoice for this order
     */
    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }

    /**
     * Get formatted total price
     */
    public function getFormattedTotalPriceAttribute(): string
    {
        return '₱' . number_format($this->total_price ?? 0, 2);
    }

    /**
     * Get formatted order number
     */
    public function getFormattedOrderNumberAttribute(): string
    {
        return str_pad($this->id, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Get SO number from sales order
     */
    public function getSoNumberAttribute(): ?string
    {
        return $this->salesOrder?->so_number;
    }

    /**
     * Check if order is pending
     */
    public function isPending(): bool
    {
        return in_array($this->order_status ?? $this->status, ['pending', 'quotation']);
    }

    /**
     * Check if order is approved
     */
    public function isApproved(): bool
    {
        return in_array($this->order_status ?? $this->status, ['approved', 'sales_order']);
    }

    /**
     * Check if order is completed
     */
    public function isCompleted(): bool
    {
        return in_array($this->order_status ?? $this->status, ['completed', 'delivered']);
    }

    /**
     * Check if order is paid
     */
    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    /**
     * Scope: Get orders by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('order_status', $status);
    }

    /**
     * Scope: Get pending orders
     */
    public function scopePending($query)
    {
        return $query->whereIn('order_status', ['pending', 'quotation']);
    }

    /**
     * Scope: Get approved orders
     */
    public function scopeApproved($query)
    {
        return $query->whereIn('order_status', ['approved', 'sales_order']);
    }

    /**
     * Scope: Get completed orders
     */
    public function scopeCompleted($query)
    {
        return $query->whereIn('order_status', ['completed', 'delivered']);
    }

    /**
     * Scope: Get orders by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Get all payments for the order.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the payment tracking records for the order.
     */
    public function paymentTracking()
    {
        return $this->hasMany(PaymentTracking::class);
    }
}
