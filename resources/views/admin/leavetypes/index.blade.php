@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
             <div class="card-header card-header-icon" data-background-color="rose">
                <i class="material-icons">category</i>
            </div>
            <div class="card-content">
                <h4 class="card-title">Manage Leave Types</h4>
                <div class="toolbar">
                    <a href="{{ route('leavetypes.create') }}" class="btn btn-rose">Add Leave Type</a>
                </div>
                <div class="material-datatables">
                    <table class="table table-striped table-no-bordered table-hover" cellspacing="0" width="100%" style="width:100%">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Duration (Days)</th>
                                <th class="disabled-sorting text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($leavetypes as $type)
                            <tr>
                                <td>{{ $type->leave_type_name }}</td>
                                <td>{{ $type->leave_duration }}</td>
                                <td class="text-right">
                                    <a href="{{ route('leavetypes.edit', $type->id) }}" class="btn btn-simple btn-warning btn-icon edit"><i class="material-icons">edit</i></a>
                                    <form action="{{ route('leavetypes.destroy', $type->id) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-simple btn-danger btn-icon remove" onclick="return confirm('Are you sure?')"><i class="material-icons">close</i></button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection