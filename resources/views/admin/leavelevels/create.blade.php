@extends('layouts.admin')
@section('content')
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="card">
                <div class="card-header card-header-icon" data-background-color="rose">
                    <i class="material-icons">add_box</i>
                </div>
                <div class="card-content">
                    <h4 class="card-title">Add Leave Level</h4>
                    <form method="POST" action="{{ route('leavelevels.store') }}">
                        @csrf
                        <div class="form-group label-floating">
                            <label class="control-label">Level Name</label>
                            <input type="text" class="form-control" name="level_name" required>
                        </div>
                        <div class="form-group label-floating">
                            <label class="control-label">Annual Leave Days</label>
                            <input type="number" class="form-control" name="annual_leave_days" min="0" required>
                        </div>
                        <button type="submit" class="btn btn-rose pull-right">Save Leave Level</button>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection