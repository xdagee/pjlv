<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\StaffLeave;
use App\Models\Role;
use App\Models\User;
use App\Models\LeaveLevel;
use App\Enums\RoleEnum;
use Illuminate\Support\Facades\Hash;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StaffController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return array
     */
    public function index()
    {
        // get all staff with required fields for DataTable
        $data = array();

        $user = auth()->user();
        $currentStaff = $user->staff;

        $query = Staff::select(
            'id',
            'staff_number',
            'title',
            'firstname',
            'lastname',
            'othername',
            'gender',
            'mobile_number',
            'is_active',
            'supervisor_id',
            'role_id',
            'department_id',
            'total_leave_days'
        )->with(['role', 'department']);

        // HOD can only see staff in their department
        if ($currentStaff && $currentStaff->role_id === RoleEnum::HOD->value) {
            $query->where('department_id', $currentStaff->department_id);
        }

        $staff = $query->latest()->get();

        // json
        $data['data'] = $staff;
        return $data;

        // view
        // return view('staff.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $roles = Role::where('role_status', 1)->get();
        // a view for staff
        return view('admin.staff.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // server side validation
        $validated = $request->validate([
            'title' => 'required|string|max:20',
            'firstname' => 'required|string|max:100',
            'lastname' => 'required|string|max:100',
            'dob' => 'required|date',
            'mobile_number' => 'required|string|max:20',
            'gender' => 'required|in:0,1',
            'date_joined' => 'required|date',
            'leave_level_id' => 'required|exists:leave_levels,id',
            'role_id' => 'required|exists:roles,id',
            'supervisor_id' => 'nullable|exists:staff,id',
            'department_id' => 'nullable|exists:departments,id',
            'total_leave_days' => 'nullable|integer|min:0|max:365',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Generate unique staff number
        $staffNumber = 'STF' . str_pad(Staff::max('id') + 1, 5, '0', STR_PAD_LEFT);

        // Calculate leave days if not provided
        $leaveDays = $validated['total_leave_days'] ?? 21; // Default fallback
        if (!isset($validated['total_leave_days']) && isset($validated['leave_level_id'])) {
            $level = LeaveLevel::find($validated['leave_level_id']);
            if ($level) {
                $leaveDays = $level->annual_leave_days;
            }
        }

        // save staff
        $staff = Staff::create([
            'staff_number' => $staffNumber,
            'title' => $validated['title'],
            'firstname' => $validated['firstname'],
            'lastname' => $validated['lastname'],
            'dob' => $validated['dob'],
            'mobile_number' => $validated['mobile_number'],
            'gender' => $validated['gender'],
            'date_joined' => $validated['date_joined'],
            'leave_level_id' => $validated['leave_level_id'],
            'role_id' => $validated['role_id'],
            'supervisor_id' => !empty($validated['supervisor_id']) ? $validated['supervisor_id'] : null,
            'department_id' => !empty($validated['department_id']) ? $validated['department_id'] : null,
            'total_leave_days' => $leaveDays,
            'picture' => $this->handlePictureUpload($request),
            'is_active' => true,
        ]);

        // Create User Account linked to Staff (Same ID strategy as per PJLV design)
        $user = User::create([
            'id' => $staff->id, // Force ID to match Staff ID
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Return JSON for AJAX requests
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Staff registered successfully!',
                'staff' => $staff,
            ], 201);
        }

        return redirect('/staff')->with('success', 'Staff registered successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \App\Models\Staff
     */
    public function show($id)
    {
        // find a staff by id
        $staff = Staff::findOrFail($id);

        // json
        return $staff;

        // view
        // return view ('staff.show', compact('staff'));

    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        // update staff info by id
        $staff = Staff::findOrFail($id);
        $roles = Role::where('role_status', 1)->get();

        // a view
        return view('admin.staff.edit', compact('staff', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        // validate fields
        $validated = $request->validate([
            'title' => 'required|string|max:20',
            'firstname' => 'required|string|max:100',
            'lastname' => 'required|string|max:100',
            'dob' => 'required|date',
            'mobile_number' => 'required|string|max:20',
            'gender' => 'required|in:0,1',
            'date_joined' => 'required|date',
            'total_leave_days' => 'nullable|integer|min:0|max:365',
            'is_active' => 'nullable|in:0,1',
            'role_id' => 'required|exists:roles,id',
        ]);

        $staff = Staff::findOrFail($id);
        $staff->title = $validated['title'];
        $staff->firstname = $validated['firstname'];
        $staff->lastname = $validated['lastname'];
        $staff->dob = $validated['dob'];
        $staff->mobile_number = $validated['mobile_number'];
        $staff->gender = $validated['gender'];
        $staff->date_joined = $validated['date_joined'];
        $staff->role_id = $validated['role_id'];

        if (isset($validated['total_leave_days'])) {
            $staff->total_leave_days = $validated['total_leave_days'];
        }
        if (isset($validated['is_active'])) {
            $staff->is_active = $validated['is_active'];
        }

        $staff->save();

        // Return JSON for AJAX requests
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Staff updated successfully!',
                'staff' => $staff,
            ]);
        }

        return redirect('/staff')->with('success', 'Staff updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $staff = Staff::findOrFail($id);

        // Soft delete - just mark as inactive instead of deleting
        $staff->is_active = false;
        $staff->save();

        return redirect('/staff')->with('success', 'Staff deactivated successfully.');
    }

    /**
     * Handle profile picture upload with default fallback.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function handlePictureUpload(Request $request): string
    {
        $defaultPicture = '/img/faces/nan.jpg';

        if ($request->hasFile('picture') && $request->file('picture')->isValid()) {
            $file = $request->file('picture');
            $filename = 'profile_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('img/faces'), $filename);
            return '/img/faces/' . $filename;
        }

        return $defaultPicture;
    }
}
