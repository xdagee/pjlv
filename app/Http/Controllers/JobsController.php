<?php

namespace App\Http\Controllers;

use App\Job;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class JobsController extends Controller
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
        $jobs = Job::latest()->get();
        //
        return $jobs;
        // return view('jobs.index', compact('jobs')); // view
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view ('jobs.create');
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
        $this->validate(request(), [
            'job_title' => 'required',
            'job_description' => 'required',
            'is_multiple_staff' => 'required'
        ]);

        //
        $jobs = Job::create(request (
            ['job_title','job_description','is_multiple_staff']
        ));

        return redirect('jobs');
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
        $job = Job::findOrFail($id);
        return $job;
        // return view ('jobs.show', compact('job'));
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
        // return $job;
        return view ('jobs.edit', compact('job'));
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
            'job_title'=>'required',
            'job_description'=>'required',
            'is_multiple_staff'=>'required',
        ]);

        $job = Job::findOrFail($id);
        $job -> job_title = $request -> input('job_title');
        $job -> job_description = $request -> input('job_description');
        $job -> is_multiple_staff = $request -> input('is_multiple_staff');
        $job = save();
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
        // $job = Job::findOrFail($id);
        // $job->delete();
    }
}
