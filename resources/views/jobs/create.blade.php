@extends('layouts.master') 

@section('content')

<div class="row">
    <div class="col-md-12 col-lg-12">
        <div class="col-md-8 col-md-8">
                            <div class="card pull">
                                <form method="POST" role="form"  action="{{ url('/jobs') }}"/>

                                    {{ csrf_field() }}
                                    
                                    <div class="card-header card-header-icon" data-background-color="rose">
                                        <i class="material-icons">contacts</i>
                                    </div>

                                    <div class="card-content">
                                        <h4 class="card-title">Add New Job Position Form</h4>

                                        <div class="form-group label-floating">
                                            <label class="control-label">Title
                                                <star>*</star>
                                            </label>
                                            <input class="form-control" name="job_title" type="text" title="true" required="true" />
                                        </div>

                                        <div class="form-group label-floating">
                                            <label class="control-label">Description
                                                <star>*</star>
                                            </label>
                                            <input class="form-control" name="job_description" type="text" description="true" required="true" />
                                        </div>

                                        <div class="form-group">
                                            <label class="control-label"> Is this job position available for mutiple Staffs?
                                                <star>*</star>
                                            </label>

                                                <div class="radio">
                                                    <label>
                                                        <input type="radio" name="is_multiple_staff" value="0" /> No
                                                    </label>
                                                </div>
                                                <div class="radio">
                                                    <label>
                                                        <input type="radio" name="is_multiple_staff" value="1" /> Yes
                                                    </label>
                                                </div>
                                        </div>

                                        <div class="category form-category">
                                            <star>*</star> Required fields </div>

                                        <div class="text-center">
                                            <button type="submit" class="btn btn-rose btn-fill btn-wd">Save</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
    </div>
</div>

@endsection