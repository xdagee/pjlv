@extends('layouts.master')
@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header card-header-icon" data-background-color="blue">
                    <i class="material-icons">people</i>
                </div>
                <div class="card-content">
                    <h4 class="card-title">{{ $department->name ?? 'My Department' }}</h4>
                    <p class="text-muted">{{ $department->description ?? '' }}</p>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="text-primary">
                                <tr>
                                    <th>Staff Number</th>
                                    <th>Name</th>
                                    <th>Role</th>
                                    <th>Leave Balance</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($colleagues as $colleague)
                                    <tr class="{{ $colleague->id === $staff->id ? 'info' : '' }}">
                                        <td>
                                            <a href="{{ url('/staff/profile/' . $colleague->id) }}" class="text-primary">
                                                {{ $colleague->staff_number }}
                                            </a>
                                        </td>
                                        <td>
                                            {{ $colleague->title }} {{ $colleague->firstname }} {{ $colleague->lastname }}
                                            @if($colleague->id === $staff->id)
                                                <span class="badge badge-success">You</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-info">{{ $colleague->role->role_name ?? 'N/A' }}</span>
                                        </td>
                                        <td>{{ $colleague->total_leave_days }} days</td>
                                        <td>
                                            @if($colleague->is_active)
                                                <span class="text-success"><i class="material-icons">check_circle</i></span>
                                            @else
                                                <span class="text-danger"><i class="material-icons">cancel</i></span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">No colleagues found in this department.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="stats">
                        <i class="material-icons">group</i> {{ $colleagues->count() }} colleague(s) in
                        {{ $department->name ?? 'department' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection