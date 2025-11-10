<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
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
                \Log::warning('AdminMiddleware: User not authenticated', ['url' => $request->url()]);
                return redirect('/staff/login')->with('error', 'Please log in to access this page.');
            }

            $user = Auth::user();
            if (!$user) {
                \Log::error('AdminMiddleware: Auth::check() returned true but no user found');
                return redirect('/staff/login')->with('error', 'Session expired. Please log in again.');
            }

            if ($user->role !== 'admin') {
                \Log::warning('AdminMiddleware: User role mismatch', ['user_id' => $user->id, 'role' => $user->role, 'expected' => 'admin']);
                abort(403, 'Unauthorized action.');
            }

            return $next($request);
        } catch (\Exception $e) {
            \Log::error('AdminMiddleware error', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return redirect('/staff/login')->with('error', 'An error occurred. Please try again.');
        }
    }
}
