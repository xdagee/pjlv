@extends('layouts.admin')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header" data-background-color="purple">
                    <h4 class="title">All Leave Requests</h4>
                    <p class="category">Manage all staff leave applications</p>
                </div>
                <div class="card-content">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Staff</th>
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
                                        <td>{{ $leave->staff->firstname ?? '' }} {{ $leave->staff->lastname ?? '' }}</td>
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
                                            <!-- Add approval/rejection actions link here if needed, generic view for now -->
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
    </div>
@endsection