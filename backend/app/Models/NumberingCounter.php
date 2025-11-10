<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NumberingCounter extends Model
{
    protected $fillable = [
        'type',
        'prefix',
        'current_number',
        'padding_length',
    ];

    protected $casts = [
        'current_number' => 'integer',
        'padding_length' => 'integer',
    ];
}