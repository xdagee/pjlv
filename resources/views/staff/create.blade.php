@extends('layouts.master') 
@section('content')

<div class="row">
    <div class="col-md-12 col-lg-12">
        <div class="col-md-8 col-md-8">
                            <div class="card pull">
                                <form method="POST" role="form"  action="{{ url('/staff') }}"/>

                                    {{ csrf_field() }}
                                    
                                    <div class="card-header card-header-icon" data-background-color="rose">
                                        <i class="material-icons">contacts</i>
                                    </div>

                                    <div class="card-content">
                                        <h4 class="card-title">Add New Staff Form</h4>

                                        <div class="form-group label-floating">
                                            <label class="control-label">Title
                                                <star>*</star>
                                            </label>
                                            <input class="form-control" name="title" type="text" title="true" required="true" />
                                        </div>

                                        <div class="form-group label-floating">
                                            <label class="control-label">First Name
                                                <star>*</star>
                                            </label>
                                            <input class="form-control" name="firstname" type="text" firstname="true" required="true" />
                                        </div>

                                        <div class="form-group label-floating">
                                            <label class="control-label">Last Name
                                                <star>*</star>
                                            </label>
                                            <input class="form-control" name="lastname" type="text" lastname="true" required="true" />
                                        </div>

                                        <div class="form-group label-floating">
                                            <label class="control-label">Date of Birth
                                                <star>*</star>
                                            </label>
                                            <input class="form-control" name="dob" type="text" dob="true" required="true" />
                                        </div>

                                        <div class="form-group label-floating">
                                            <label class="control-label">Mobile Number
                                                <star>*</star>
                                            </label>
                                            <input class="form-control" name="mobile_number" type="text" mobile_number="true" required="true" />
                                        </div>

                                        <div class="form-group label-floating">
                                            <label class="control-label"> Gender
                                                <star>*</star>
                                            </label>

                                                <div class="radio">
                                                    <label>
                                                        <input type="radio" name="gender" value="0" /> Female
                                                    </label>
                                                </div>
                                                <div class="radio">
                                                    <label>
                                                        <input type="radio" name="gender" value="1" /> Male
                                                    </label>
                                                </div>
                                        </div>

                                        <div class="form-group label-floating">
                                            <label class="label-control">Date Joined
                                                <star>*</star>
                                            </label>
                                            <input class="form-control" name="date_joined" type="text" date_joined="true" required="true" />
                                        </div>

                                        <div class="category form-category">
                                            <star>*</star> Required fields </div>

                                        <div class="text-center">
                                            <button type="submit" class="btn btn-rose btn-fill btn-wd pull-right">Add</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
    </div>
</div>

@endsection