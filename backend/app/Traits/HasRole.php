<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait HasRole
{
    public function hasRole($role)
    {
        return Auth::check() && Auth::user()->role === $role;
    }

    public function requireRole($role)
    {
        if (!$this->hasRole($role)) {
            abort(403, 'Unauthorized action.');
        }
    }
} 