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
                                    @csrf
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