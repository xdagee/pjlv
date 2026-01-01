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

<form name="edit-staff-form" role="form">
    @csrf
    <input type="hidden" name="staff_id" value="{{ $staff->id }}">

    <div class="form-group label-floating">
        <label class="control-label">Title <star>*</star></label>
        <div class="form-group">
            <select class="form-control" name="title" required>
                <option value="">Select title</option>
                <option value="Dr" {{ $staff->title == 'Dr' ? 'selected' : '' }}>Dr.</option>
                <option value="Mr" {{ $staff->title == 'Mr' ? 'selected' : '' }}>Mr.</option>
                <option value="Mrs" {{ $staff->title == 'Mrs' ? 'selected' : '' }}>Mrs.</option>
                <option value="Miss" {{ $staff->title == 'Miss' ? 'selected' : '' }}>Miss</option>
            </select>
        </div>
    </div>

    <div class="form-group label-floating">
        <label class="control-label">First Name <star>*</star></label>
        <input class="form-control" name="firstname" type="text" value="{{ $staff->firstname }}" required />
    </div>

    <div class="form-group label-floating">
        <label class="control-label">Last Name <star>*</star></label>
        <input class="form-control" name="lastname" type="text" value="{{ $staff->lastname }}" required />
    </div>

    <div class="form-group label-floating">
        <label class="control-label">Date of Birth <star>*</star></label>
        <input class="form-control datepicker" name="dob" type="text"
            value="{{ $staff->dob ? \Carbon\Carbon::parse($staff->dob)->format('Y-m-d') : '' }}" required />
    </div>

    <div class="form-group label-floating">
        <label class="control-label">Mobile Number <star>*</star></label>
        <input class="form-control" name="mobile_number" type="text" value="{{ $staff->mobile_number }}" required />
    </div>

    <div class="form-group label-floating">
        <label class="control-label">Gender <star>*</star></label>
        <div class="radio radio-inline">
            <label>
                <input type="radio" name="gender" value="1" {{ $staff->gender ? 'checked' : '' }} required /> Male
            </label>
            <label>
                <input type="radio" name="gender" value="0" {{ !$staff->gender ? 'checked' : '' }} required /> Female
            </label>
        </div>
    </div>

    <div class="form-group label-floating">
        <label class="label-control">Date Joined <star>*</star></label>
        <input class="form-control datepicker" name="date_joined" type="text"
            value="{{ $staff->date_joined ? \Carbon\Carbon::parse($staff->date_joined)->format('Y-m-d') : '' }}"
            required />
    </div>

    <div class="form-group label-floating">
        <label class="control-label">Total Leave Days</label>
        <input class="form-control" name="total_leave_days" type="number" value="{{ $staff->total_leave_days ?? 21 }}"
            min="0" max="365" />
    </div>

    <div class="form-group label-floating">
        <label class="control-label">Role <star>*</star></label>
        <div class="form-group">
            <select class="form-control" name="role_id" required>
                <option value="">Select role</option>
                @foreach($roles as $role)
                    <option value="{{ $role->id }}" {{ $staff->role_id == $role->id ? 'selected' : '' }}>
                        {{ $role->role_name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="form-group">
        <label>Status</label>
        <div class="radio radio-inline">
            <label>
                <input type="radio" name="is_active" value="1" {{ $staff->is_active ? 'checked' : '' }} /> Active
            </label>
            <label>
                <input type="radio" name="is_active" value="0" {{ !$staff->is_active ? 'checked' : '' }} /> Inactive
            </label>
        </div>
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