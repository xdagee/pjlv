<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminCalendarController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'superadmin']);
    }

    public function index()
    {
        // Add logic to fetch events if needed, similar to CalendarController
        return view('admin.calendar');
    }
}
