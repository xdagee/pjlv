@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header" data-background-color="purple">
                    <h4 class="title">Leave Requests</h4>
                    <p class="category">View and manage leave applications</p>
                </div>
                <div class="card-content">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <div class="toolbar" style="margin-bottom: 20px;">
                        <a href="/leaves/apply" class="btn btn-primary">
                            <i class="material-icons">add</i> Apply for Leave
                        </a>
                    </div>

                    <table class="table table-hover">
                        <thead>
                            <tr>
                                @if(in_array($staff->role_id ?? 5, [1, 2]))
                                    <th>Staff</th>
                                @endif
                                <th>Leave Type</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Days</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($leaves as $leave)
                                <tr>
                                    @if(in_array($staff->role_id ?? 5, [1, 2]))
                                        <td>{{ ($leave->staff->firstname ?? '') . ' ' . ($leave->staff->lastname ?? '') }}</td>
                                    @endif
                                    <td>{{ $leave->leaveType->leave_type_name ?? 'N/A' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($leave->start_date)->format('M d, Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($leave->end_date)->format('M d, Y') }}</td>
                                    <td>{{ $leave->leave_days }}</td>
                                    <td>
                                        @php
                                            $latestAction = $leave->leaveAction->last();
                                            $status = $latestAction ? $latestAction->leaveStatus->status_name ?? 'Pending' : 'Pending';
                                            $badgeClass = match ($status) {
                                                'Approved' => 'success',
                                                'Disapproved', 'Rejected' => 'danger',
                                                'Recommended' => 'info',
                                                'Cancelled' => 'default',
                                                default => 'warning'
                                            };
                                        @endphp
                                        <span class="label label-{{ $badgeClass }}">{{ $status }}</span>
                                    </td>
                                    <td>
                                        <a href="/leaves/{{ $leave->id }}" class="btn btn-info btn-sm" title="View">
                                            <i class="material-icons">visibility</i>
                                        </a>
                                        @if(in_array($staff->role_id ?? 5, [1, 2, 3, 4]) && $status === 'Pending')
                                            <a href="/leaves/{{ $leave->id }}/edit" class="btn btn-success btn-sm" title="Review">
                                                <i class="material-icons">check_circle</i>
                                            </a>
                                        @endif
                                        @if($leave->staff_id === ($staff->id ?? 0) && in_array($status, ['Pending', 'Unattended']))
                                            <form action="/leaves/{{ $leave->id }}/cancel" method="POST" style="display:inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-warning btn-sm" title="Cancel"
                                                    onclick="return confirm('Cancel this request?')">
                                                    <i class="material-icons">cancel</i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">No leave requests found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{ $leaves->links() }}
                </div>
            </div>
        </div>
    </div>

    <style>
        .label {
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 12px;
            color: white;
        }

        .label-success {
            background-color: #4caf50;
        }

        .label-danger {
            background-color: #f44336;
        }

        .label-warning {
            background-color: #ff9800;
        }

        .label-info {
            background-color: #00bcd4;
        }

        .label-default {
            background-color: #9e9e9e;
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }

        .alert-success {
            background-color: #dff0d8;
            color: #3c763d;
        }

        .alert-danger {
            background-color: #f2dede;
            color: #a94442;
        }
    </style>
@endsection