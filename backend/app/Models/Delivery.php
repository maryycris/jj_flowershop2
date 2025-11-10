<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Delivery extends Model
{
    protected $fillable = [
        'order_id',
        'driver_id',
        'delivery_date',
        'delivery_time',
        'recipient_name',
        'recipient_phone',
        'delivery_address',
        'shipping_fee',
        'special_instructions',
        'delivery_notes',
        'delivery_message',
        'recipient_relationship',
        'status',
        'proof_of_delivery_image',
        'proof_of_delivery_taken_at',
    ];

    protected $casts = [
        'proof_of_delivery_taken_at' => 'datetime',
    ];

    /**
     * Get the order that owns the delivery.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the driver that owns the delivery.
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    /**
     * Get formatted shipping fee
     */
    public function getFormattedShippingFeeAttribute(): string
    {
        return '₱' . number_format($this->shipping_fee ?? 0, 2);
    }

    /**
     * Get full delivery address
     */
    public function getFullAddressAttribute(): string
    {
        return $this->delivery_address;
    }

    /**
     * Get formatted delivery date
     */
    public function getFormattedDeliveryDateAttribute(): string
    {
        return $this->delivery_date ? \Carbon\Carbon::parse($this->delivery_date)->format('M d, Y') : 'N/A';
    }

    /**
     * Check if delivery has proof
     */
    public function hasProof(): bool
    {
        return !empty($this->proof_of_delivery_image);
    }

    /**
     * Get proof of delivery URL
     */
    public function getProofOfDeliveryUrlAttribute(): ?string
    {
        return $this->proof_of_delivery_image ? asset('storage/' . $this->proof_of_delivery_image) : null;
    }

    /**
     * Check if delivery is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === 'delivered' || $this->status === 'completed';
    }

    /**
     * Scope: Get deliveries by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope: Get pending deliveries
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope: Get completed deliveries
     */
    public function scopeCompleted($query)
    {
        return $query->whereIn('status', ['delivered', 'completed']);
    }
}
