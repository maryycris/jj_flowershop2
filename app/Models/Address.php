<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $table = 'addresses';

    protected $fillable = [
        'user_id',
        'label',
        'first_name',
        'last_name',
        'company',
        'street_address',
        'barangay',
        'municipality',
        'city',
        'province',
        'zip_code',
        'region',
        'phone_number',
        'is_default',
        'landmark',
        'special_instructions',
    ];

    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where('id', $value)
            ->where('user_id', auth()->id())
            ->firstOrFail();
    }
}
