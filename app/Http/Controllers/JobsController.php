<?php

namespace App\Http\Controllers;

use App\Models\Job;
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
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // getting all jobs
        $jobs = Job::select('id', 'job_title', 'job_description', 'is_multiple_staff')->latest()->get();

        // return view
        return view('admin.jobs.index', compact('jobs'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.jobs.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
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
        $jobs = Job::create(request(
            ['job_title', 'job_description', 'is_multiple_staff']
        ));

        return redirect('admin/jobs')->with('success', 'Job created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        //find the id
        $job = Job::findOrFail($id);

        // view
        return view('admin.jobs.show', compact('job'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        // find id
        $job = Job::findOrFail($id);

        return view('admin.jobs.edit', compact('job'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        //
        $this->validate($request, [
            'job_title' => 'required',
            'job_description' => 'required',
            'is_multiple_staff' => 'required',
        ]);

        $job = Job::findOrFail($id);
        $job->job_title = $request->input('job_title');
        $job->job_description = $request->input('job_description');
        $job->is_multiple_staff = $request->input('is_multiple_staff');
        $job->save();

        return redirect('/admin/jobs')->with('success', 'Job updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $job = Job::findOrFail($id);
        $job->delete();

        return redirect('/admin/jobs')->with('success', 'Job deleted successfully.');
    }
}
