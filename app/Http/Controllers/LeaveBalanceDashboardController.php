<?php

namespace App\Http\Controllers;

use App\Services\LeaveBalanceService;
use Illuminate\Support\Facades\Auth;

class LeaveBalanceDashboardController extends Controller
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
     * Display the leave balance dashboard for the authenticated employee.
     *
     * @param LeaveBalanceService $balanceService
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index(LeaveBalanceService $balanceService)
    {
        $staff = Auth::user()->staff;

        if (!$staff) {
            return redirect('/dashboard')
                ->with('error', 'No staff profile found.');
        }

        $balanceBreakdown = $balanceService->getBalanceBreakdown($staff->id);

        return view('leave-balance.index', compact('balanceBreakdown', 'staff'));
    }
}
