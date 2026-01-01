<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Enums\RoleEnum;

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

        // Use RoleEnum for role hierarchy
        $userRoleEnum = RoleEnum::fromName($userRole);
        $requiredRoleEnum = RoleEnum::fromName($requiredRole);

        if (!$userRoleEnum || !$requiredRoleEnum) {
            return redirect('/dashboard')->with('error', 'Invalid role configuration.');
        }

        if ($userRoleEnum->level() > $requiredRoleEnum->level()) {
            return redirect('/dashboard')->with('error', 'Access denied. Insufficient permissions.');
        }

        return $next($request);
    }
}
