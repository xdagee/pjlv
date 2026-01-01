<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StaffLeave;
use App\Models\LeaveType;
use App\Services\SettingsService;

class AdminLeavesController extends Controller
{
    protected $settingsService;

    public function __construct(SettingsService $settingsService)
    {
        $this->middleware(['auth', 'superadmin']);
        $this->settingsService = $settingsService;
    }

    public function index()
    {
        $perPage = $this->settingsService->get('display.pagination_size', 15);

        $leaves = StaffLeave::with(['staff', 'leaveType', 'leaveAction.leaveStatus'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return view('admin.leaves.index', compact('leaves'));
    }
}
