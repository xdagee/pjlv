<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle($request, Closure $next, $role)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();
        $staff = $user->staff;

        if (!$staff || !$staff->role) {
            return redirect('/dashboard')->with('error', 'Access denied. No role assigned.');
        }

        // Get role name from the staff's role
        $userRole = strtolower($staff->role->role_name);
        $requiredRole = strtolower($role);

        // Define role hierarchy (higher roles can access lower role routes)
        $roleHierarchy = [
            'admin' => 5,
            'dg' => 4,
            'director' => 3,
            'hr' => 2,
            'normal' => 1,
        ];

        $userLevel = $roleHierarchy[$userRole] ?? 0;
        $requiredLevel = $roleHierarchy[$requiredRole] ?? 0;

        if ($userLevel < $requiredLevel) {
            return redirect('/dashboard')->with('error', 'Access denied. Insufficient permissions.');
        }

        return $next($request);
    }
}
