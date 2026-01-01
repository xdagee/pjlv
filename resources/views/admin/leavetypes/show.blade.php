@extends('layouts.admin')
@section('content')
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="card">
                <div class="card-header card-header-icon" data-background-color="rose">
                    <i class="material-icons">visibility</i>
                </div>
                <div class="card-content">
                    <h4 class="card-title">Leave Type Details</h4>
                    <p><strong>Name:</strong> {{ $leavetype->leave_type_name }}</p>
                    <p><strong>Duration:</strong> {{ $leavetype->leave_duration }} Days</p>
                    <a href="{{ route('leavetypes.index') }}" class="btn btn-rose">Back</a>
                </div>
            </div>
        </div>
    </div>
@endsection