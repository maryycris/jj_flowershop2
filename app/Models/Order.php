<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'total_price',
        'status',
        'notes',
        'payment_status',
        'payment_method',
        'type',
        'order_status',
        'approved_at',
        'on_delivery_at',
        'completed_at',
        'approved_by',
        'assigned_driver_id',
        'invoice_status',
        'invoice_generated_at',
        'invoice_paid_at',
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
        return $this->belongsToMany(Product::class, 'order_product')->withPivot('quantity')->withTimestamps();
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
     * Get the assigned driver for the order.
     */
    public function assignedDriver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_driver_id');
    }

    /**
     * Get the payment tracking records for the order.
     */
    public function paymentTracking()
    {
        return $this->hasMany(PaymentTracking::class);
    }
}
