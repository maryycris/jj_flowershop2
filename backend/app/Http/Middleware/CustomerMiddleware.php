<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CustomerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            if (!Auth::check()) {
                \Log::warning('CustomerMiddleware: User not authenticated', ['url' => $request->url()]);
                return redirect('/customer/login')->with('error', 'Please log in to access this page.');
            }

            $user = Auth::user();
            if (!$user) {
                \Log::error('CustomerMiddleware: Auth::check() returned true but no user found');
                return redirect('/customer/login')->with('error', 'Session expired. Please log in again.');
            }

            if ($user->role !== 'customer') {
                \Log::warning('CustomerMiddleware: User role mismatch', ['user_id' => $user->id, 'role' => $user->role, 'expected' => 'customer']);
                abort(403, 'Unauthorized action.');
            }

            $response = $next($request);
            
            // Add cache control headers to prevent browser caching
            $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');
            
            return $response;
        } catch (\Exception $e) {
            \Log::error('CustomerMiddleware error', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return redirect('/customer/login')->with('error', 'An error occurred. Please try again.');
        }
    }
} 