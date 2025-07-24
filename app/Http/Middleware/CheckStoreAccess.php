<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckStoreAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        // List all resource parameter names you want to check
        foreach (['order', 'delivery', 'product'] as $param) {
            $resource = $request->route($param);
            if ($resource && isset($resource->store_id)) {
                // Allow super admin to access everything
                if ($user->role === 'super_admin') {
                    return $next($request);
                }
                // Check if the user's store_id matches the resource's store_id
                if ($user->store_id !== $resource->store_id) {
                    abort(403, 'Unauthorized: You do not have access to this store.');
                }
            }
        }

        return $next($request);
    }
}
