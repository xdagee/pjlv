@extends('layouts.admin')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header card-header-icon" data-background-color="rose">
                    <i class="material-icons">event_note</i>
                </div>
                <div class="card-content">
                    <h4 class="card-title">Manage Holidays</h4>
                    <div class="toolbar">
                        <a href="{{ route('holidays.create') }}" class="btn btn-rose">Add Holiday</a>
                    </div>
                    <div class="material-datatables">
                        <table class="table table-striped table-no-bordered table-hover" cellspacing="0" width="100%"
                            style="width:100%">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Date</th>
                                    <th class="disabled-sorting text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($holidays as $holiday)
                                    <tr>
                                        <td>{{ $holiday->name }}</td>
                                        <td>{{ $holiday->date->format('Y-m-d') }}</td>
                                        <td class="text-right">
                                            <a href="{{ route('holidays.edit', $holiday->id) }}"
                                                class="btn btn-simple btn-warning btn-icon edit"><i
                                                    class="material-icons">edit</i></a>
                                            <form action="{{ route('holidays.destroy', $holiday->id) }}" method="POST"
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