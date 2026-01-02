@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header" data-background-color="purple">
                    <h4 class="title">All Leave Applications</h4>
                    <p class="category">View all leave requests in the organization</p>
                </div>
                <div class="card-content">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Staff</th>
                                    <th>Department</th>
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
                                    @php
                                        $latestAction = $leave->leaveAction->sortByDesc('created_at')->first();
                                        $status = $latestAction?->leaveStatus?->status_name ?? 'Unknown';
                                        $statusClass = match ($status) {
                                            'Approved' => 'label-success',
                                            'Rejected', 'Disapproved' => 'label-danger',
                                            'Recommended' => 'label-info',
                                            'Cancelled' => 'label-default',
                                            default => 'label-warning',
                                        };
                                    @endphp
                                    <tr>
                                        <td>{{ $leave->staff->firstname ?? '' }} {{ $leave->staff->lastname ?? '' }}</td>
                                        <td>{{ $leave->staff->department->department_name ?? '-' }}</td>
                                        <td>{{ $leave->leaveType->leave_type_name ?? '-' }}</td>
                                        <td>{{ \Carbon\Carbon::parse($leave->start_date)->format('d M Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($leave->end_date)->format('d M Y') }}</td>
                                        <td>{{ $leave->leave_days }}</td>
                                        <td><span class="label {{ $statusClass }}">{{ $status }}</span></td>
                                        <td>
                                            <a href="{{ url('/leaves/' . $leave->id) }}" class="btn btn-info btn-simple btn-xs"
                                                title="View">
                                                <i class="material-icons">visibility</i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No leave requests found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($leaves->hasPages())
                        <div class="text-center">
                            {{ $leaves->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection