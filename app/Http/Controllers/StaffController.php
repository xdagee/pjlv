<?php

namespace App\Http\Controllers;

use App\Staff;
use Illuminate\Http\Request;

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
        //
        $staff = Staff::latest()->get();
        // json
        return $staff;
        // view
        // return view('staffs.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
            'date_joined' => 'required'
        ]);

        //
        Staff::create(request(
            ['title','firstname','lastname','dob','mobile_number','gender','date_joined']
        ));
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
        //
        $staff = Staff::findOrFail($id);
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
        //
        $job = Job::findOrFail($id);

        return view('jobs.edit', compact('job'));
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
        //
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
        $staff = Staff::findOrFail($id);
        $staff->delete();
    }
}
