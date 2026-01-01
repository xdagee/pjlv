<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the user's profile or another staff member's profile.
     */
    public function show($id = null)
    {
        if ($id) {
            // Viewing another staff's profile
            $staff = \App\Models\Staff::with(['role', 'department'])->findOrFail($id);
            $user = $staff->user;
        } else {
            // Viewing own profile
            $user = Auth::user();
            $staff = $user->staff;
        }

        return view('staff.profile', compact('user', 'staff'));
    }

    /**
     * Update the user's profile.
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $staff = $user->staff;

        $validated = $request->validate([
            'firstname' => 'required|string|max:100',
            'lastname' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'mobile_number' => 'nullable|string|max:20',
            'dob' => 'nullable|date',
        ]);

        // Update user email
        $user->email = $validated['email'];
        $user->save();

        // Update staff details
        if ($staff) {
            $staff->firstname = $validated['firstname'];
            $staff->lastname = $validated['lastname'];
            $staff->mobile_number = $validated['mobile_number'] ?? $staff->mobile_number;
            if (!empty($validated['dob'])) {
                $staff->dob = $validated['dob'];
            }
            $staff->save();
        }

        return redirect()->route('profile.show')->with('success', 'Profile updated successfully!');
    }

    /**
     * Update the user's password.
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

        return redirect()->route('profile.show')->with('success', 'Password changed successfully!');
    }
}
