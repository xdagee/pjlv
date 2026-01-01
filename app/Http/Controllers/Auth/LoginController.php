<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @return string
     */
    protected function redirectTo()
    {
        // 1. Check for Super Admin (New Architecture - admins table)
        if (auth()->check() && auth()->user()->admin) {
            return '/admin/dashboard';
        }

        // 2. Check for Super Admin (Legacy/Staff Role 1 - Fail-safe)
        // Note: We migrated User 1 out of staff, but if any other staff has role_id 1, redirect them too.
        if (auth()->check() && auth()->user()->staff && auth()->user()->staff->role_id == 1) {
            return '/admin/dashboard';
        }

        return '/dashboard';
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }
}
