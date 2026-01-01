@extends('layouts.master')
@section('content')

    <div class="row">
        <!-- Stats Cards -->
        <div class="col-lg-3 col-md-6 col-sm-6">
            <div class="card card-stats">
                <div class="card-header" data-background-color="green">
                    <i class="material-icons">event_available</i>
                </div>
                <div class="card-content">
                    <p class="category">Total Allowance</p>
                    <h3 class="card-title">{{ $stats['total_allowance'] }} <small>days</small></h3>
                </div>
                <div class="card-footer">
                    <div class="stats">
                        <i class="material-icons">info</i> Annual Leave Entitlement
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6">
            <div class="card card-stats">
                <div class="card-header" data-background-color="orange">
                    <i class="material-icons">timer</i>
                </div>
                <div class="card-content">
                    <p class="category">Used</p>
                    <h3 class="card-title">{{ $stats['total_used'] }} <small>days</small></h3>
                </div>
                <div class="card-footer">
                    <div class="stats">
                        <i class="material-icons">check</i> Approved leaves taken
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6">
            <div class="card card-stats">
                <div class="card-header" data-background-color="blue">
                    <i class="material-icons">account_balance_wallet</i>
                </div>
                <div class="card-content">
                    <p class="category">Remaining</p>
                    <h3 class="card-title">{{ $stats['remaining'] }} <small>days</small></h3>
                </div>
                <div class="card-footer">
                    <div class="stats">
                        <i class="material-icons">trending_up</i> Available balance
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6">
            <div class="card card-stats">
                <div class="card-header" data-background-color="rose">
                    <i class="material-icons">pending_actions</i>
                </div>
                <div class="card-content">
                    <p class="category">Pending</p>
                    <h3 class="card-title">{{ $stats['pending'] }} <small>requests</small></h3>
                </div>
                <div class="card-footer">
                    <div class="stats">
                        <i class="material-icons">hourglass_empty</i> Awaiting approval
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Leave History Table -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header card-header-icon" data-background-color="purple">
                    <i class="material-icons">history</i>
                </div>
                <div class="card-content">
                    <h4 class="card-title">My Leave History
                        <a href="{{ url('/staff/reports/export') }}" class="btn btn-success btn-sm pull-right">
                            <i class="material-icons">download</i> Export CSV
                        </a>
                    </h4>
                    <div class="table-responsive">
                        <table class="table">
                            <thead class="text-primary">
                                <tr>
                                    <th>Type</th>
                                    <th>Dates</th>
                                    <th>Days</th>
                                    <th>Status</th>
                                    <th>Applied</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($leaves as $leave)
                                    @php
                                        $lastAction = $leave->leaveAction->last();
                                        $statusName = $lastAction?->leaveStatus?->status_name ?? 'Unknown';
                                        $statusClass = match (strtolower($statusName)) {
                                            'approved' => 'badge-success',
                                            'rejected', 'disapproved' => 'badge-danger',
                                            'recommended' => 'badge-info',
                                            'cancelled' => 'badge-secondary',
                                            default => 'badge-warning'
                                        };
                                    @endphp
                                    <tr>
                                        <td>{{ $leave->leaveType->leave_type_name ?? 'N/A' }}</td>
                                        <td>{{ \Carbon\Carbon::parse($leave->start_date)->format('d M') }} -
                                            {{ \Carbon\Carbon::parse($leave->end_date)->format('d M Y') }}</td>
                                        <td>{{ $leave->leave_days }}</td>
                                        <td><span class="badge {{ $statusClass }}">{{ $statusName }}</span></td>
                                        <td>{{ $leave->created_at->format('d M Y') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">No leave requests found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Leave Breakdown by Type -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header card-header-icon" data-background-color="blue">
                    <i class="material-icons">pie_chart</i>
                </div>
                <div class="card-content">
                    <h4 class="card-title">Breakdown by Type</h4>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Leave Type</th>
                                    <th class="text-right">Days</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($byType as $type => $days)
                                    <tr>
                                        <td>{{ $type ?? 'Unknown' }}</td>
                                        <td class="text-right"><strong>{{ $days }}</strong></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center text-muted">No data</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="card">
                <div class="card-header card-header-icon" data-background-color="green">
                    <i class="material-icons">analytics</i>
                </div>
                <div class="card-content">
                    <h4 class="card-title">Request Summary</h4>
                    <ul class="list-unstyled">
                        <li><i class="material-icons text-success">check_circle</i> Approved:
                            <strong>{{ $stats['approved'] }}</strong></li>
                        <li><i class="material-icons text-warning">hourglass_empty</i> Pending:
                            <strong>{{ $stats['pending'] }}</strong></li>
                        <li><i class="material-icons text-danger">cancel</i> Rejected:
                            <strong>{{ $stats['rejected'] }}</strong></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

@endsection