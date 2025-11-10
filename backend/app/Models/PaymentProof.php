<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentProof extends Model
{
    protected $fillable = [
        'order_id',
        'image_path',
        'reference_number',
        'payment_method',
        'status',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
