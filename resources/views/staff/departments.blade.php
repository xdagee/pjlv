@extends('layouts.master')
@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header card-header-icon" data-background-color="purple">
                    <i class="material-icons">business</i>
                </div>
                <div class="card-content">
                    <h4 class="card-title">All Departments</h4>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="text-primary">
                                <tr>
                                    <th>#</th>
                                    <th>Department Name</th>
                                    <th>Description</th>
                                    <th>Staff Count</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($departments as $index => $dept)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <strong>{{ $dept->name }}</strong>
                                        </td>
                                        <td>{{ \Illuminate\Support\Str::limit($dept->description, 60) }}</td>
                                        <td>
                                            <span class="badge badge-info">{{ $dept->staff_count }} staff</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">No departments found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="stats">
                        <i class="material-icons">business</i> {{ $departments->count() }} department(s)
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection