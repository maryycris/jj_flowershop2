<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'event_type',
        'event_date',
        'event_time',
        'venue',
        'recipient_name',
        'recipient_phone',
        'guest_count',
        'personalized_message',
        'special_instructions',
        'color_scheme',
        'contact_phone',
        'contact_email',
        'notes',
        'order_id',
        'status',
        'subtotal',
        'delivery_fee',
        'service_fee',
        'total',
        'payment_method',
        'payment_status',
        'paymongo_source_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
