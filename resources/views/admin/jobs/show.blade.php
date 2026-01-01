@extends('layouts.admin')

@section('content')
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="card">
                <div class="card-header" data-background-color="blue">
                    <h4 class="title">Job Details</h4>
                    <p class="category">View job position information</p>
                </div>
                <div class="card-content">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Title</label>
                                <p class="form-control-static">{{ $job->job_title }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Multiple Staff?</label>
                                <p class="form-control-static">{{ $job->is_multiple_staff ? 'Yes' : 'No' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">Description</label>
                                <p class="form-control-static">{{ $job->job_description }}</p>
                            </div>
                        </div>
                    </div>

                    <a href="{{ url('admin/jobs') }}" class="btn btn-default">Back to List</a>
                    <a href="{{ url('admin/jobs/' . $job->id . '/edit') }}" class="btn btn-primary">Edit</a>
                </div>
            </div>
        </div>
    </div>
@endsection