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
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminLeavesController;
use App\Http\Controllers\AdminSettingsController;
use App\Http\Controllers\AdminCalendarController;
use App\Http\Controllers\DepartmentsController;
use App\Http\Controllers\AdminProfileController;
use App\Http\Controllers\LeaveBalanceDashboardController;

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
	Route::get('/staff/profile', [ProfileController::class, 'show'])->name('profile.show');
	Route::get('/staff/profile/{id}', [ProfileController::class, 'show'])->name('profile.show.id');
	Route::put('/staff/profile', [ProfileController::class, 'update'])->name('profile.update');
	Route::put('/staff/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

	// My Department - view colleagues in same department
	Route::get('/my-department', function () {
		$staff = Auth::user()->staff;
		if (!$staff || !$staff->department_id) {
			return redirect('/dashboard')->with('error', 'No department assigned.');
		}
		$colleagues = \App\Models\Staff::with(['role', 'department'])
			->where('department_id', $staff->department_id)
			->where('is_active', true)
			->get();
		$department = $staff->department;
		return view('staff.my-department', compact('colleagues', 'department', 'staff'));
	});

	// All Departments - for privileged roles (handled via middleware in group below)
	Route::get('/departments', function () {
		$departments = \App\Models\Department::withCount('staff')->get();
		return view('staff.departments', compact('departments'));
	})->middleware('role:ops');

	// Calendar
	Route::get('/calendar', [CalendarController::class, 'index']);
	Route::get('/calendar/events', [CalendarController::class, 'events']);

	// Leave requests - all users can apply and view their own
	Route::get('/leaves', [StaffLeavesController::class, 'index']);
	Route::get('/leaves/apply', [StaffLeavesController::class, 'create']);
	Route::post('/leaves', [StaffLeavesController::class, 'store']);
	Route::get('/leaves/{staffleave}', [StaffLeavesController::class, 'show']);
	Route::post('/leaves/{staffleave}/cancel', [StaffLeavesController::class, 'cancel']);
	Route::get('/leaves/{staffleave}/edit', [StaffLeavesController::class, 'edit']);
	Route::put('/leaves/{staffleave}', [StaffLeavesController::class, 'update']);
	Route::delete('/leaves/{staffleave}', [StaffLeavesController::class, 'destroy']);

	// Staff personal reports
	Route::get('/staff/reports', [StaffLeavesController::class, 'myReports']);
	Route::get('/staff/reports/export', [StaffLeavesController::class, 'exportMyReports']);

	// Leave Balance Dashboard - detailed balance view for employees
	Route::get('/leave-balance', [LeaveBalanceDashboardController::class, 'index'])
		->name('leave-balance.index');

	// Reference data - all users can view
	Route::get('/leavetypes', [LeaveTypesController::class, 'index']);
	Route::get('/leavetypes/{leavetype}', [LeaveTypesController::class, 'show']);
	Route::get('/leavestatuses', [LeaveStatusesController::class, 'index']);
	Route::get('/leavestatuses/{leavestatus}', [LeaveStatusesController::class, 'show']);
	Route::get('/leavelevels', [LeaveLevelsController::class, 'index']);
	Route::get('/leavelevels/{leavelevel}', [LeaveLevelsController::class, 'show']);

	// Notifications - all authenticated users
	Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
	Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.unread');
	Route::get('/notifications/recent', [NotificationController::class, 'recent'])->name('notifications.recent');
	Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
	Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');
	Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
});

/*
|--------------------------------------------------------------------------
| HR (Staff Management)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:hod'])->group(function () {

	// Staff management - Directory view (uses staff layout)
	Route::get('/staff', function () {
		return view('staff.index');
	});
	Route::get('/staff/data', [StaffController::class, 'index']);
	Route::get('/staff/create', [StaffController::class, 'create']);
	Route::post('/staff', [StaffController::class, 'store']);
	Route::get('/staff/{staff}', [StaffController::class, 'show']);
	Route::get('/staff/{staff}/edit', [StaffController::class, 'edit']);
	Route::put('/staff/{staff}', [StaffController::class, 'update']);
	Route::delete('/staff/{staff}', [StaffController::class, 'destroy']);


	// Leave actions
	Route::get('/leaveactions', [LeaveActionsController::class, 'index']);
	Route::get('/leaveactions/create', [LeaveActionsController::class, 'create']);
	Route::post('/leaveactions', [LeaveActionsController::class, 'store']);
	Route::get('/leaveactions/{leaveaction}', [LeaveActionsController::class, 'show']);

	// Reports
	Route::get('/reports', [ReportsController::class, 'index']);
	Route::get('/reports/export', [ReportsController::class, 'export']);
	Route::get('/reports/export-pdf', [ReportsController::class, 'exportPdf']);
});

/*
|--------------------------------------------------------------------------
| Admin Only Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'superadmin', 'throttle:60,1'])->prefix('admin')->group(function () {

	// Admin Dashboard
	Route::get('/dashboard', [App\Http\Controllers\AdminController::class, 'index'])->name('admin.dashboard');

	// Staff Management (admin/staffs)
	Route::get('/staffs', function () {
		return view('admin.staff.index', ['url_prefix' => '/admin/staffs']);
	});
	Route::get('/staffs/data', [StaffController::class, 'index']);
	Route::get('/staffs/create', [StaffController::class, 'create']);
	Route::post('/staffs', [StaffController::class, 'store']);
	Route::get('/staffs/{staff}', [StaffController::class, 'show']);
	Route::get('/staffs/{staff}/edit', [StaffController::class, 'edit']);
	Route::put('/staffs/{staff}', [StaffController::class, 'update']);
	Route::delete('/staffs/{staff}', [StaffController::class, 'destroy']);

	// Roles Management
	Route::get('/roles', [App\Http\Controllers\RolesController::class, 'index'])->name('roles.index');
	Route::post('/roles', [App\Http\Controllers\RolesController::class, 'store'])->name('roles.store');
	Route::put('/roles/{role}', [App\Http\Controllers\RolesController::class, 'update'])->name('roles.update');
	Route::delete('/roles/{role}', [App\Http\Controllers\RolesController::class, 'destroy'])->name('roles.destroy');

	// Holidays Management
	Route::get('/holidays', [App\Http\Controllers\HolidaysController::class, 'index'])->name('holidays.index');
	Route::get('/holidays/create', [App\Http\Controllers\HolidaysController::class, 'create'])->name('holidays.create');
	Route::post('/holidays', [App\Http\Controllers\HolidaysController::class, 'store'])->name('holidays.store');
	Route::get('/holidays/{holiday}/edit', [App\Http\Controllers\HolidaysController::class, 'edit'])->name('holidays.edit');
	Route::put('/holidays/{holiday}', [App\Http\Controllers\HolidaysController::class, 'update'])->name('holidays.update');
	Route::delete('/holidays/{holiday}', [App\Http\Controllers\HolidaysController::class, 'destroy'])->name('holidays.destroy');

	// Leave Levels Management
	Route::get('/leavelevels', [LeaveLevelsController::class, 'index'])->name('leavelevels.index');
	Route::get('/leavelevels/create', [LeaveLevelsController::class, 'create'])->name('leavelevels.create');
	Route::post('/leavelevels', [LeaveLevelsController::class, 'store'])->name('leavelevels.store');
	Route::get('/leavelevels/{leavelevel}/edit', [LeaveLevelsController::class, 'edit'])->name('leavelevels.edit');
	Route::put('/leavelevels/{leavelevel}', [LeaveLevelsController::class, 'update'])->name('leavelevels.update');
	Route::delete('/leavelevels/{leavelevel}', [LeaveLevelsController::class, 'destroy'])->name('leavelevels.destroy');

	// Job management
	Route::get('/jobs', [JobsController::class, 'index']);
	Route::get('/jobs/create', [JobsController::class, 'create']);
	Route::post('/jobs', [JobsController::class, 'store']);
	Route::get('/jobs/{job}', [JobsController::class, 'show']);
	Route::get('/jobs/{job}/edit', [JobsController::class, 'edit']);
	Route::put('/jobs/{job}', [JobsController::class, 'update']);
	Route::delete('/jobs/{job}', [JobsController::class, 'destroy']);

	// Departments
	Route::get('/departments', [DepartmentsController::class, 'index'])->name('departments.index');
	Route::get('/departments/create', [DepartmentsController::class, 'create'])->name('departments.create');
	Route::post('/departments', [DepartmentsController::class, 'store'])->name('departments.store');
	Route::get('/departments/{department}', [DepartmentsController::class, 'show'])->name('departments.show');
	Route::get('/departments/{department}/edit', [DepartmentsController::class, 'edit'])->name('departments.edit');
	Route::put('/departments/{department}', [DepartmentsController::class, 'update'])->name('departments.update');
	Route::delete('/departments/{department}', [DepartmentsController::class, 'destroy'])->name('departments.destroy');

	// Admin Leaves (View All)
	Route::get('/leaves', [AdminLeavesController::class, 'index']);

	// Leave Types
	// Leave Types
	Route::get('/leavetypes', [LeaveTypesController::class, 'index'])->name('leavetypes.index');
	Route::get('/leavetypes/create', [LeaveTypesController::class, 'create'])->name('leavetypes.create');
	Route::post('/leavetypes', [LeaveTypesController::class, 'store'])->name('leavetypes.store');
	Route::get('/leavetypes/{leavetype}/edit', [LeaveTypesController::class, 'edit'])->name('leavetypes.edit');
	Route::put('/leavetypes/{leavetype}', [LeaveTypesController::class, 'update'])->name('leavetypes.update');
	Route::delete('/leavetypes/{leavetype}', [LeaveTypesController::class, 'destroy'])->name('leavetypes.destroy');

	// Reports
	Route::get('/reports', [ReportsController::class, 'index']);

	// Calendar
	Route::get('/calendar', [AdminCalendarController::class, 'index']);

	// Settings
	Route::get('/settings', [AdminSettingsController::class, 'index'])->name('admin.settings.index');
	Route::put('/settings', [AdminSettingsController::class, 'update'])->name('admin.settings.update');
	Route::post('/settings', [AdminSettingsController::class, 'store'])->name('admin.settings.store');
	Route::delete('/settings/{id}', [AdminSettingsController::class, 'destroy'])->name('admin.settings.destroy');

	// Profile
	Route::get('/profile', [AdminProfileController::class, 'show'])->name('admin.profile.show');
	Route::put('/profile', [AdminProfileController::class, 'update'])->name('admin.profile.update');
	Route::put('/profile/password', [AdminProfileController::class, 'updatePassword'])->name('admin.profile.password');
});
