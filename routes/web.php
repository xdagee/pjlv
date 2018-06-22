<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
	// landing page...
    return view('welcome');
});

Auth::routes();

Route::get('/dashboard', 'DashboardController@index');



Route::get('/staffs/create', function(){ return view('staffs.create');});



// staff controller with all the possible methods
Route::get('/staff/', 'StaffController@index');

Route::get('/staff/create', 'StaffController@create');

Route::post('staff', 'StaffController@store');

Route::get('/staff/{staff}', 'StaffController@show');



// jobs controller with all possible job options
Route::get('/jobs/', 'JobsController@index');

Route::get('/jobs/create', 'JobsController@create');

Route::post('jobs', 'JobsController@store');

Route::get('/jobs/{job}', 'JobsController@show');



// leave controller and it's eqivallent methods
Route::get('/leaves/', 'StaffLeavesController@index');

Route::get('/leaves/apply', 'StaffLeavesController@create');

Route::post('leaves', 'StaffLeavesController@store');

Route::get('/leaves/{staffleave}', 'StaffLeavesController@show');



// leave types controller and it's eqivallent methods
Route::get('/leavetypes/', 'LeaveTypesController@index');

Route::get('/leavetypes/{leavetype}', 'LeaveTypesController@show');



// leave types controller and it's eqivallent methods
Route::get('/leavestatuses/', 'LeaveStatusesController@index');

Route::get('/leavestatuses/{leavestatus}', 'LeaveStatusesController@show');



// leave types controller and it's eqivallent methods
Route::get('/leaveactions/', 'LeaveActionsController@index');

Route::get('/leaveactions/{leaveaction}', 'LeaveActionsController@show');


// leave types controller and it's eqivallent methods
Route::get('/leavelevels/', 'LeaveLevelsController@index');

Route::get('/leavelevels/{leavelevel}', 'LeaveLevelsController@show');



// calender controller and it's eqivallents...
Route::get('/calendar', 'CalendarController@index');



// reports
Route::get('/reports', 'ReportsController@index');
Route::get('/apply', function () {
	return view('leaves.apply');
	});