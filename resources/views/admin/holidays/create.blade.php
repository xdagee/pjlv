@extends('layouts.admin')
@section('content')
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="card">
                <div class="card-header card-header-icon" data-background-color="rose">
                    <i class="material-icons">add_box</i>
                </div>
                <div class="card-content">
                    <h4 class="card-title">Add Holiday</h4>
                    <form method="POST" action="{{ route('holidays.store') }}">
                        @csrf
                        <div class="form-group label-floating">
                            <label class="control-label">Holiday Name</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="form-group label-floating">
                            <label class="control-label">Date</label>
                            <input type="date" class="form-control" name="date" required>
                        </div>
                        <button type="submit" class="btn btn-rose pull-right">Save Holiday</button>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection