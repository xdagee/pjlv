<style>
    .datepicker-dropdown::after {
        content: '';
        display: none;
    }

    .error,
    label.error {
        color: red;
    }
</style>

<form name="add-staff-form" role="form" action="{{ url('/staff') }}">
    @csrf

    <div class="form-group label-floating">
        <label class="control-label">Title <star>*</star></label>
        <div class="form-group">
            <select class="form-control" name="title" required>
                <option value="">Select title</option>
                <option value="Dr">Dr.</option>
                <option value="Mr">Mr.</option>
                <option value="Mrs">Mrs.</option>
                <option value="Miss">Miss</option>
            </select>
        </div>
    </div>

    <div class="form-group label-floating">
        <label class="control-label">First Name <star>*</star></label>
        <input class="form-control" name="firstname" type="text" required />
    </div>

    <div class="form-group label-floating">
        <label class="control-label">Last Name <star>*</star></label>
        <input class="form-control" name="lastname" type="text" required />
    </div>

    <div class="form-group label-floating">
        <label class="control-label">Date of Birth <star>*</star></label>
        <input class="form-control datepicker" name="dob" type="text" required />
    </div>

    <div class="form-group label-floating">
        <label class="control-label">Mobile Number <star>*</star></label>
        <input class="form-control" name="mobile_number" type="text" required />
        <span class="help-block">Should be between 10 - 15 digits only</span>
    </div>

    <div class="form-group label-floating">
        <label class="control-label">Email Address <star>*</star></label>
        <input class="form-control" name="email" type="email" required />
        <span class="help-block">This will be used for login</span>
    </div>

    <div class="form-group label-floating">
        <label class="control-label">Password <star>*</star></label>
        <input class="form-control" name="password" type="password" required minlength="6" />
        <span class="help-block">Minimum 6 characters</span>
    </div>

    <div class="form-group label-floating">
        <label class="control-label">Department <star>*</star></label>
        <div class="form-group">
            <select class="form-control" name="department_id" required>
                <option value="">Select department</option>
                @foreach(\App\Models\Department::orderBy('name')->get() as $department)
                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="form-group label-floating">
        <label class="control-label">Supervisor</label>
        <div class="form-group">
            <select class="form-control" name="supervisor_id">
                <option value="">Select a supervisor (optional)</option>
                @foreach(\App\Models\Staff::where('is_active', true)->orderBy('firstname')->get() as $supervisor)
                    <option value="{{ $supervisor->id }}">{{ $supervisor->firstname }} {{ $supervisor->lastname }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="form-group label-floating">
        <label class="control-label">Leave Level <star>*</star></label>
        <div class="form-group">
            <select class="form-control" name="leave_level_id" required>
                <option value="">Select leave level</option>
                @foreach(\App\Models\LeaveLevel::all() as $level)
                    <option value="{{ $level->id }}">{{ $level->level_name ?? 'Level ' . $level->id }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="form-group label-floating">
        <label class="control-label">Role <star>*</star></label>
        <div class="form-group">
            <select class="form-control" name="role_id" required>
                <option value="">Select role</option>
                @foreach($roles as $role)
                    <option value="{{ $role->id }}">{{ $role->role_name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="form-group label-floating">
        <label class="control-label">Gender <star>*</star></label>
        <div class="radio radio-inline">
            <label>
                <input type="radio" name="gender" value="1" required /> Male
            </label>
            <label>
                <input type="radio" name="gender" value="0" required /> Female
            </label>
        </div>
    </div>

    <div class="form-group label-floating">
        <label class="label-control">Date Joined <star>*</star></label>
        <input class="form-control datepicker" name="date_joined" type="text" required />
    </div>

    <div class="form-group label-floating">
        <label class="control-label">Total Leave Days</label>
        <input class="form-control" name="total_leave_days" type="number" value="21" min="0" max="365" />
    </div>

</form>

<script>
    $(document).ready(function () {
        $(".datepicker").datepicker({
            format: "yyyy-mm-dd",
            endDate: moment().format("YYYY-MM-DD"),
            autoclose: true,
            startView: 2
        });
    });
</script>