<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromotedBanner extends Model
{
    protected $fillable = ['image','title','link_url','is_active','sort_order'];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }
}
