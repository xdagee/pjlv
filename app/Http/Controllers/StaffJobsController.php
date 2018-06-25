<?php

namespace App\Http\Controllers;

use App\StaffJob;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StaffJobsController extends Controller
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


    public function index()
    {
        //
    	$staffjobs = StaffJob::latest()->get();
        // json
    	return $staffjob;
    }
}
