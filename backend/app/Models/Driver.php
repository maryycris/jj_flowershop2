<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'license_number',
        'vehicle_type',
        'vehicle_plate',
        'availability_status',
        'work_start_time',
        'work_end_time',
        'delivery_areas',
        'max_deliveries_per_day',
        'current_deliveries_today',
        'notes',
        'is_active'
    ];

    protected $casts = [
        'delivery_areas' => 'array',
        'work_start_time' => 'datetime:H:i',
        'work_end_time' => 'datetime:H:i',
        'is_active' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'assigned_driver_id');
    }

    public function isAvailable()
    {
        return $this->availability_status === 'available' && 
               $this->is_active && 
               $this->current_deliveries_today < $this->max_deliveries_per_day;
    }

    public function getAvailabilityBadgeClass()
    {
        return match($this->availability_status) {
            'available' => 'bg-success',
            'busy' => 'bg-warning',
            'off_duty' => 'bg-secondary',
            'on_delivery' => 'bg-info',
            default => 'bg-secondary'
        };
    }

    public function getAvailabilityText()
    {
        return match($this->availability_status) {
            'available' => 'Available',
            'busy' => 'Busy',
            'off_duty' => 'Off Duty',
            'on_delivery' => 'On Delivery',
            default => 'Unknown'
        };
    }
}
