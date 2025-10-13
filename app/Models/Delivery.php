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
}
