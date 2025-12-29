<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $permission
     * @return mixed
     */
    public function handle($request, Closure $next, $permission)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();
        $staff = $user->staff;

        if (!$staff || !$staff->role) {
            return redirect('/dashboard')->with('error', 'Access denied.');
        }

        $roleName = strtolower($staff->role->role_name);

        // Define permissions per role
        $rolePermissions = [
            'admin' => [
                'manage_staff',
                'view_staff',
                'create_staff',
                'edit_staff',
                'delete_staff',
                'manage_leaves',
                'view_leaves',
                'approve_leaves',
                'reject_leaves',
                'manage_jobs',
                'view_reports',
                'manage_settings',
            ],
            'hr' => [
                'view_staff',
                'create_staff',
                'edit_staff',
                'manage_leaves',
                'view_leaves',
                'approve_leaves',
                'reject_leaves',
                'view_reports',
            ],
            'dg' => [
                'view_staff',
                'view_leaves',
                'approve_leaves',
                'reject_leaves',
                'view_reports',
            ],
            'director' => [
                'view_staff',
                'view_leaves',
                'approve_leaves',
                'reject_leaves',
            ],
            'normal' => [
                'view_leaves',
                'apply_leaves',
            ],
        ];

        $userPermissions = $rolePermissions[$roleName] ?? [];

        if (!in_array($permission, $userPermissions)) {
            return redirect('/dashboard')->with('error', 'Access denied. You do not have permission for this action.');
        }

        return $next($request);
    }
}
