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
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index');

Route::get('/employees', 'EmployeesController@index');

Route::get('/leaves', 'LeavesController@index');

Route::get('/calendar', 'CalendarsController@index');

Route::get('/reports', 'ReportsController@index');
