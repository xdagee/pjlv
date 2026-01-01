@extends('layouts.admin')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <form method="POST" action="{{ url('admin/jobs/' . $job->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="card-header" data-background-color="rose">
                        <h4 class="title">Edit Job Position</h4>
                        <p class="category">Update job details</p>
                    </div>

                    <div class="card-content">

                        <div class="form-group label-floating">
                            <label class="control-label">Title <star>*</star></label>
                            <input class="form-control" name="job_title" type="text" value="{{ $job->job_title }}"
                                required />
                        </div>

                        <div class="form-group label-floating">
                            <label class="control-label">Description <star>*</star></label>
                            <input class="form-control" name="job_description" type="text"
                                value="{{ $job->job_description }}" required />
                        </div>

                        <div class="form-group">
                            <label class="control-label">Is this job position available for multiple Staffs?</label>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="is_multiple_staff" value="0" {{ $job->is_multiple_staff == 0 ? 'checked' : '' }} /> No
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="is_multiple_staff" value="1" {{ $job->is_multiple_staff == 1 ? 'checked' : '' }} /> Yes
                                </label>
                            </div>
                        </div>

                        <div class="text-center">
                            <a href="{{ url('admin/jobs') }}" class="btn btn-default">Cancel</a>
                            <button type="submit" class="btn btn-rose btn-fill">Update Job</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection