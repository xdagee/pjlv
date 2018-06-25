<?php

namespace App\Http\Controllers;

use App\Staff;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ShowProfile extends Controller
{
    	/**
         * Show the profile for the given user.
         *
         * @param  int  $id
         * @return Response
         */

        public function __invoke($id)
        {
            return view('staff.profile', ['staff' => Staff::findOrFail($id)]);
        }
}
