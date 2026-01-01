<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckSuperAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // STRICT: User ID 1 is the System Owner/Super Admin regardless of Staff status
        if (Auth::check() && Auth::id() === 1) {
            return $next($request);
        }

        // Primary check: User has an 'admin' profile record
        if (Auth::check() && Auth::user()->admin) {
            return $next($request);
        }

        return redirect('/dashboard')->with('error', 'Access denied. Super Admin privileges required.');
    }
}
