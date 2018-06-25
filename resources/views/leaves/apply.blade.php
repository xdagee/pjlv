<style>
    label.error, .error{
        color:red;
    }
</style>

                                    <hr class="text-primary">
                                    <form name="apply-leave-form" role="form" action="{{ url('/staffs') }}" >

                                        <p  class="text-center">You have <span class="text-danger">XX </span>days remaining</p>
                                        <hr class="text-primary">
                                        <div class="row">
                                            <label class="col-md-3 label-on-left">Leave Type</label>
                                            <div class="col-md-9">
                                                <div class="form-group label-floating is-empty">
                                                    <label class="control-label"></label>
                                                    <select class="form-control" name="leave-type-id" required>
                                                        <option value="">Select a leave type</option>
                                                        <option value="1">Annual Leave</option>
                                                        <option value="2">Other Leave types</option>
                                                    </select>
                                                    <span class="help-block">Select type of leave you wish to go, only eligible types are displayed</span>
                                                        </div>
                                            </div>
                                        </div>

                                        <br>

                                        <div class="row">
                                            <label class="col-md-3 label-on-left">Start date</label>
                                            <div class="col-md-9">
                                                <div class="form-group label-floating is-empty input-group date">
                                                    <label class="control-label"></label>
                                                    <input type="text" name="start-date" class="form-control" placeholder="Leave start date(First select a leave type)" readonly="readonly" required disabled/>
                                                    <span class="help-block">
                                                       This is the day you start your leave
                                                    </span>
                                                    <span class="input-group-addon">
                                                        <i class="material-icons text-primary" rel="tooltip"
                                                           title="Choose the date you wish start your leave.You can select a return date or type in duration" >event</i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <br>

                                        <div class="row">
                                            <label class="col-md-3 label-on-left">Return date</label>
                                            <div class="col-md-9">
                                                <div class="form-group label-floating is-empty input-group date">
                                                    <label class="control-label"></label>
                                                    <input type="text" name="end-date" class="form-control"
                                                           placeholder="Leave return date(First select a start date)" readonly="readonly" required disabled/>
                                                    <span class="help-block">
                                                        This is the day you will return to work
                                                    </span>
                                                    <span class="input-group-addon">
                                                        <i class="material-icons text-primary" rel="tooltip"
                                                           title="Choose the date you wish to end your leave" >event</i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <br>

                                        <div class="row">
                                            <label class="col-md-3 label-on-left">Duration</label>
                                            <div class="col-md-9">
                                                <div class="form-group label-floating is-empty input-group">
                                                    <label class="control-label"></label>
                                                    <input type="text" class="form-control" name="duration"
                                                     placeholder="Duration in days(First select a start date)" required disabled/>
                                                    <span class="help-block">
                                                        You may enter the number of days you want to be on leave
                                                    </span>
                                                    <span class="input-group-addon">
                                                        <i class="material-icons text-primary" rel="tooltip"
                                                           title="Enter the number of days you wish to go on leave" >date_range</i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <br>
                                        {{ csrf_field() }}
                                    </form>
                                <br>


            <script src="{{URL::asset('js/customjs/apply-leave-rules.js')}}"></script>
           