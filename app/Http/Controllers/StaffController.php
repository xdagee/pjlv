<?php

namespace App\Http\Controllers;

use App\Staff;
use App\StaffLeave;
use App\StaffLeaveLevel;
use App\Role;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StaffController extends Controller
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // get all staff sorting by latest added
        // $staff = Staff::latest()->get();

        // get all staff with limits
        // $staff = Staff::orderby('id', 'desc')->paginate(5); 

        // explicitly selecting
        $data = array();
        $staff = Staff::select('id','staff_number','title','firstname','lastname', 'mobile_number','is_active', 'supervisor_id')->latest()->get();
        // json
        $data['data']=$staff;
        return $data;

        // view
        // return view('staff.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // a view for staff
        return view ('staff.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // server side validation
        $this->validate(request(),[
            'title'=>'required',
            'firstname'=>'required',
            'lastname'=>'required',
            'dob'=> 'required',
            'mobile_number' => 'required',
            'gender' => 'required',
            'date_joined' => 'required',
            'leave_level_id' => 'required',
            'role_id' => 'required'
        ]);

        // save staff
        Staff::create(
            [
                'title' => request('title'),
                'firstname' => request ('firstname'),
                'lastname' => request ('lastname'),
                'dob' => request ('dob'),
                'mobile_number' => request ('mobile_number'),
                'gender' => request ('gender'),
                'date_joined' => request ('date_joined'),
                'leave_level_id' => request ('leave_level_id'),
                'role_id' => request ('role_id')
            ]
        );

        return redirect('/staff');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // find a staff by id
        $staff = Staff::findOrFail($id);

        // json
        return $staff;
        // return view ('staff.show', compact('staff'));

    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // update staff info by id
        $staff = Staff::findOrFail($id);

        // a view
        return view('staff.edit', compact('staff'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // validate fields
        $this->validate($request, [
            'title'=>'required',
            'firstname'=>'required',
            'lastname'=>'required',
            'dob'=> 'required',
            'mobile_number' => 'required',
            'gender' => 'required',
            'date_joined' => 'required'
        ]);

        $staff = Staff::findOrFail($id);
        $staff -> title = $request->input('title');
        $staff -> firstname = $request->input('firstname');
        $staff -> lastname = $request->input('lastname');
        $staff -> dob = $request->input('dob');
        $staff -> mobile_number->input('mobile_number');
        $staff -> gender->input('gender');
        $staff -> date_joined->input('date_joined');
        $staff -> save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        // $staff = Staff::findOrFail($id);
        // $staff->delete();
    }
}
