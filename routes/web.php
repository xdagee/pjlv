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

Route::get('/dashboard', 'DashboardsController@index');

// staffs controller with all the possible methods
Route::get('/staffs/', 'StaffsController@index');

Route::get('/staffs/create', function(){ return view('staffs.create');});

Route::post('staffs', 'StaffsController@store');

Route::get('/staffs/{staff}', 'StaffsController@show');




// jobs controller with all possible job options
Route::get('/jobs/', 'JobsController@index');

Route::get('/jobs/create', 'JobsController@create');

Route::post('jobs', 'JobsController@store');

Route::get('/jobs/{job}', 'JobsController@show');



// leave controller and it's eqivallent methods
Route::get('/leaves/', 'StaffLeavesController@index');

Route::get('/leaves/create', 'StaffLeavesController@create');

Route::post('leaves', 'StaffLeavesController@store');

Route::get('/leaves/{staffleave}', 'StaffLeavesController@show');



// calender controller and it's eqivallents...
Route::get('/calendar', 'CalendarsController@index');



// reports
Route::get('/reports', 'ReportsController@index');
Route::get('/apply', function () {
	return view('leaves.apply');
	});


