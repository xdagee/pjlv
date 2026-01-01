<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

/**
 * AdminProfileController handles profile management for Super Admin users.
 * Uses the dedicated 'admins' table via the User details.
 */
class AdminProfileController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware(['auth', 'superadmin']);
    }

    /**
     * Show the admin's profile.
     */
    public function show()
    {
        return view('admin.profile');
    }

    /**
     * Update the admin's profile.
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $admin = $user->admin;

        if (!$admin) {
            return back()->with('error', 'Admin profile not found.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
        ]);

        // Update user email
        $user->email = $validated['email'];
        $user->save();

        // Update admin details
        $admin->name = $validated['name'];
        $admin->phone = $validated['phone'] ?? $admin->phone;
        $admin->save();

        return redirect()->route('admin.profile.show')->with('success', 'Profile updated successfully!');
    }

    /**
     * Update the admin's password.
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = Auth::user();

        // Verify current password
        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        // Update password
        $user->password = Hash::make($validated['password']);
        $user->save();

        return redirect()->route('admin.profile.show')->with('success', 'Password changed successfully!');
    }
}
