<<<<<<< HEAD
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
=======
<style>
    .datepicker-dropdown::after {
        content: '';
        display: none;
    }
    .error, label.error{
        color: red;
    }
</style>


<form name="add-staff-form" role="form"  action="{{ url('/staffs') }}">
    <div class="form-group label-floating">
        <label class="control-label">Title
            <star>*</star>
        </label>
        <div class="form-group">
            <label class="control-label"></label>
            <select  class="form-control" name="title-name" required>
                <option value="">Select title</option>
                <option value="Dr">Dr.</option>
                <option value="Mr">Mr.</option>
                <option value="Mrs">Mrs</option>
                <option value="Miss">Miss</option>
            </select>
        </div>
    </div>


    <div class="form-group label-floating">
        <label class="control-label">First Name
            <star>*</star>
        </label>
        <input class="form-control" name="firstname" type="text"  required />
    </div>

    <div class="form-group label-floating">
        <label class="control-label">Last Name
            <star>*</star>
        </label>
        <input class="form-control" name="lastname" type="text"  required/>
    </div>


    <div class="form-group label-floating">
        <label class="control-label">Date of Birth
            <star>*</star>
        </label>
        <input class="form-control" name="dob" type="text" dob="true" required" />
    </div>

    <div class="form-group label-floating">
        <label class="control-label">Mobile Number
            <star>*</star>
        </label>
        <input class="form-control" name="mobile_number" type="text" required />
        <span class="help-block">Should be between 10 - 15 digits only</span>
>>>>>>> 262148d9f3548d588db0e30037e57ca23d80b414
    </div>

    <br>

    <div class="form-group label-floating">
        <label class="control-label">Supervisor
            <star>*</star>
        </label>
        <div class="form-group">
            <select class="form-control" name="supervisor-id" required >
                <option value="">Select a supervisor</option>
                <option value="1">PeeZee - Manager</option>
            </select>
        </div>
    </div>
    <br>


    <div class="form-group label-floating">
        <label class="control-label"> Gender
            <star>*</star>
        </label>

        <div class="radio radio-inline">
            <label>
                <input type="radio" name="gender" value="1"  required/> Male
            </label>
            <label>
                <input type="radio" name="gender" value="0"  required/> Female
            </label>
        </div>
    </div>
    <br>

    <div class="form-group label-floating ">
        <label class="label-control">Date Joined
            <star>*</star>
        </label>
        <input class="form-control" name="date-joined" type="text" required/>
    </div>
    <br>
    <br>
</form>
<script src="{{URL::asset('staff-reg-rules.jsjs')}}" ></script>

<script>
    $(document).ready(function () {

        $("input[name=date-joined]").add("input[name=dob]").datepicker({
            format: "dd-MM-yyyy",
            endDate: moment().format("DD-MMMM-YYYY"),
            daysOfWeekDisabled: ["0", "6"],
            autoclose: true,
            startView: 2
        });
    });

</script>