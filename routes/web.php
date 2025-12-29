<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\StaffLeavesController;
use App\Http\Controllers\LeaveTypesController;
use App\Http\Controllers\LeaveStatusesController;
use App\Http\Controllers\LeaveLevelsController;
use App\Http\Controllers\LeaveActionsController;
use App\Http\Controllers\JobsController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\NotificationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
	return view('welcome');
});

Auth::routes();

/*
|--------------------------------------------------------------------------
| Authenticated Routes (All logged-in users)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

	// Dashboard
	Route::get('/dashboard', [DashboardController::class, 'index']);
	Route::get('/home', [DashboardController::class, 'index']); // Alias

	// User Profile
	Route::get('/staff/profile', function () {
		return view('staff.profile');
	});

	// Calendar
	Route::get('/calendar', [CalendarController::class, 'index']);
	Route::get('/calendar/events', [CalendarController::class, 'events']);

	// Leave requests - all users can apply and view their own
	Route::get('/leaves', [StaffLeavesController::class, 'index']);
	Route::get('/leaves/apply', [StaffLeavesController::class, 'create']);
	Route::post('/leaves', [StaffLeavesController::class, 'store']);
	Route::get('/leaves/{staffleave}', [StaffLeavesController::class, 'show']);
	Route::post('/leaves/{staffleave}/cancel', [StaffLeavesController::class, 'cancel']);

	// Reference data - all users can view
	Route::get('/leavetypes', [LeaveTypesController::class, 'index']);
	Route::get('/leavetypes/{leavetype}', [LeaveTypesController::class, 'show']);
	Route::get('/leavestatuses', [LeaveStatusesController::class, 'index']);
	Route::get('/leavestatuses/{leavestatus}', [LeaveStatusesController::class, 'show']);
	Route::get('/leavelevels', [LeaveLevelsController::class, 'index']);
	Route::get('/leavelevels/{leavelevel}', [LeaveLevelsController::class, 'show']);
});

/*
|--------------------------------------------------------------------------
| HR/Admin Routes (Staff Management)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:hr'])->group(function () {

	// Staff management
	Route::get('/staff', function () {
		return view('staff.index');
	});
	Route::get('/staff/data', [StaffController::class, 'index']);
	Route::get('/staff/create', [StaffController::class, 'create']);
	Route::post('/staff', [StaffController::class, 'store']);
	Route::get('/staff/{staff}', [StaffController::class, 'show']);
	Route::put('/staff/{staff}', [StaffController::class, 'update']);
	Route::delete('/staff/{staff}', [StaffController::class, 'destroy']);

	// Leave management (approve/reject)
	Route::get('/leaves/{staffleave}/edit', [StaffLeavesController::class, 'edit']);
	Route::put('/leaves/{staffleave}', [StaffLeavesController::class, 'update']);
	Route::delete('/leaves/{staffleave}', [StaffLeavesController::class, 'destroy']);

	// Leave actions
	Route::get('/leaveactions', [LeaveActionsController::class, 'index']);
	Route::get('/leaveactions/create', [LeaveActionsController::class, 'create']);
	Route::post('/leaveactions', [LeaveActionsController::class, 'store']);
	Route::get('/leaveactions/{leaveaction}', [LeaveActionsController::class, 'show']);

	// Reports
	Route::get('/reports', [ReportsController::class, 'index']);
	Route::get('/reports/export', [ReportsController::class, 'export']);
});

/*
|--------------------------------------------------------------------------
| Admin Only Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->group(function () {

	// Job management
	Route::get('/jobs', [JobsController::class, 'index']);
	Route::get('/jobs/create', [JobsController::class, 'create']);
	Route::post('/jobs', [JobsController::class, 'store']);
	Route::get('/jobs/{job}', [JobsController::class, 'show']);
	Route::get('/jobs/{job}/edit', [JobsController::class, 'edit']);
	Route::put('/jobs/{job}', [JobsController::class, 'update']);
	Route::delete('/jobs/{job}', [JobsController::class, 'destroy']);

	// Leave Types CRUD (Admin only)
	Route::get('/leavetypes/create', [LeaveTypesController::class, 'create']);
	Route::post('/leavetypes', [LeaveTypesController::class, 'store']);
	Route::get('/leavetypes/{leavetype}/edit', [LeaveTypesController::class, 'edit']);
	Route::put('/leavetypes/{leavetype}', [LeaveTypesController::class, 'update']);
	Route::delete('/leavetypes/{leavetype}', [LeaveTypesController::class, 'destroy']);
});
