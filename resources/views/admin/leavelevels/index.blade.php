@extends('layouts.admin')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header card-header-icon" data-background-color="rose">
                    <i class="material-icons">format_list_numbered</i>
                </div>
                <div class="card-content">
                    <h4 class="card-title">Manage Leave Levels</h4>
                    <div class="toolbar">
                        <a href="{{ route('leavelevels.create') }}" class="btn btn-rose">Add Leave Level</a>
                    </div>
                    <div class="material-datatables">
                        <table class="table table-striped table-no-bordered table-hover" cellspacing="0" width="100%"
                            style="width:100%">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Days Allowed</th>
                                    <th class="disabled-sorting text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($leavelevels as $level)
                                    <tr>
                                        <td>{{ $level->level_name }}</td>
                                        <td>{{ $level->annual_leave_days }}</td>
                                        <td class="text-right">
                                            <a href="{{ route('leavelevels.edit', $level->id) }}"
                                                class="btn btn-simple btn-warning btn-icon edit"><i
                                                    class="material-icons">edit</i></a>
                                            <form action="{{ route('leavelevels.destroy', $level->id) }}" method="POST"
                                                style="display:inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-simple btn-danger btn-icon remove"
                                                    onclick="return confirm('Are you sure?')"><i
                                                        class="material-icons">close</i></button>
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