<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StaffLeave;
use App\Models\LeaveType;
use App\Services\SettingsService;
use App\Enums\RoleEnum;
use Illuminate\Support\Facades\Auth;

class AdminLeavesController extends Controller
{
    protected $settingsService;

    public function __construct(SettingsService $settingsService)
    {
        $this->middleware('auth');
        $this->settingsService = $settingsService;
    }

    public function index()
    {
        $user = Auth::user();
        $staff = $user->staff;
        $perPage = $this->settingsService->get('display.pagination_size', 15);

        $query = StaffLeave::with(['staff', 'leaveType', 'leaveAction.leaveStatus'])
            ->orderBy('created_at', 'desc');

        // Super Admin (no staff profile) or Admin role can see all leaves
        // HOD can only see leaves from their department
        if ($staff && $staff->role_id === RoleEnum::HOD->value) {
            $query->whereHas('staff', function ($q) use ($staff) {
                $q->where('department_id', $staff->department_id);
            });
        }
        // Other privileged roles (Admin, HR, CEO, OPS) and Super Admin see all leaves

        $leaves = $query->paginate($perPage);

        // Use admin view for /admin/leaves, staff view for /all-leaves
        $view = request()->is('admin/*') ? 'admin.leaves.index' : 'staff.leaves.all';
        return view($view, compact('leaves', 'staff'));
    }
}
