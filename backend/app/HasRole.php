<?php

namespace App;

use Illuminate\Support\Facades\Auth;

trait HasRole
{
    public function hasRole($role)
    {
        if (!Auth::check()) {
            return false;
        }

        return Auth::user()->role === $role;
    }

    public function requireRole($role)
    {
        if (!$this->hasRole($role)) {
            abort(403, 'Unauthorized action.');
        }
    }
}
