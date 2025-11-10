<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    protected $fillable = [
        'name',
        'address',
        'contact_number',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get formatted contact number
     */
    public function getFormattedContactNumberAttribute(): string
    {
        return $this->contact_number ?? 'N/A';
    }

    /**
     * Get full store information
     */
    public function getFullInfoAttribute(): string
    {
        $info = [$this->name];
        if ($this->address) {
            $info[] = $this->address;
        }
        if ($this->contact_number) {
            $info[] = 'Tel: ' . $this->contact_number;
        }
        return implode(' | ', $info);
    }

    /**
     * Scope: Get store by name
     */
    public function scopeByName($query, $name)
    {
        return $query->where('name', $name);
    }
}
