@extends('layouts.master')

@section('content')
    <div class="row">
        <!-- Pending Leave Requests -->
        <div class="col-lg-3 col-md-6 col-sm-6">
            <div class="card card-stats">
                <div class="card-header" data-background-color="orange">
                    <i class="material-icons">pending_actions</i>
                </div>
                <div class="card-content">
                    <p class="category">Pending Requests</p>
                    <h3 class="card-title">{{ $pendingLeaves }}</h3>
                </div>
                <div class="card-footer">
                    <div class="stats">
                        <i class="material-icons">update</i> Awaiting action
                    </div>
                </div>
            </div>
        </div>

        <!-- Staff On Leave Today -->
        <div class="col-lg-3 col-md-6 col-sm-6">
            <div class="card card-stats">
                <div class="card-header" data-background-color="rose">
                    <i class="material-icons">person_off</i>
                </div>
                <div class="card-content">
                    <p class="category">On Leave Today</p>
                    <h3 class="card-title">{{ $staffOnLeaveToday }}</h3>
                </div>
                <div class="card-footer">
                    <div class="stats">
                        <i class="material-icons">people</i> of {{ $totalStaff }} staff
                    </div>
                </div>
            </div>
        </div>

        <!-- Leave Balance -->
        <div class="col-lg-3 col-md-6 col-sm-6">
            <div class="card card-stats">
                <div class="card-header" data-background-color="green">
                    <i class="material-icons">event_available</i>
                </div>
                <div class="card-content">
                    <p class="category">Your Leave Balance</p>
                    <h3 class="card-title">{{ $balanceBreakdown['remaining'] }} <small>days</small></h3>
                </div>
                <div class="card-footer">
                    <div class="stats">
                        <i class="material-icons">info</i> {{ $balanceBreakdown['total_used'] }} used of
                        {{ $balanceBreakdown['total_allowance'] }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Apply -->
        <div class="col-lg-3 col-md-6 col-sm-6">
            <div class="card card-stats">
                <div class="card-header" data-background-color="blue">
                    <i class="material-icons">add_circle</i>
                </div>
                <div class="card-content">
                    <p class="category">Quick Action</p>
                    <a href="/leaves/apply" class="btn btn-primary btn-sm">Apply for Leave</a>
                </div>
                <div class="card-footer">
                    <div class="stats">
                        <i class="material-icons">flight_takeoff</i> Request time off
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Leave Requests -->
        <div class="col-lg-8 col-md-12">
            <div class="card">
                <div class="card-header" data-background-color="purple">
                    <h4 class="title">Recent Leave Requests</h4>
                    <p class="category">Your last 5 leave applications</p>
                </div>
                <div class="card-content table-responsive">
                    <table class="table table-hover">
                        <thead class="text-warning">
                            <tr>
                                <th>Type</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Days</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentLeaves as $leave)
                                <tr>
                                    <td>{{ $leave->leaveType->leave_type_name ?? 'N/A' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($leave->start_date)->format('M d, Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($leave->end_date)->format('M d, Y') }}</td>
                                    <td>{{ $leave->leave_days }}</td>
                                    <td>
                                        @php
                                            $latestAction = $leave->leaveAction->last();
                                            $status = $latestAction ? $latestAction->leaveStatus->status_name ?? 'Pending' : 'Pending';
                                            $badgeClass = match ($status) {
                                                'Approved' => 'badge-success',
                                                'Disapproved', 'Rejected' => 'badge-danger',
                                                'Recommended' => 'badge-info',
                                                default => 'badge-warning'
                                            };
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">{{ $status }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">No leave requests yet</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <a href="/leaves" class="btn btn-simple btn-primary">View All Leaves</a>
                </div>
            </div>
        </div>

        <!-- Upcoming Holidays -->
        <div class="col-lg-4 col-md-12">
            <div class="card">
                <div class="card-header" data-background-color="green">
                    <h4 class="title">Upcoming Holidays</h4>
                    <p class="category">Next 30 days</p>
                </div>
                <div class="card-content">
                    <ul class="list-unstyled">
                        @forelse($upcomingHolidays as $holiday)
                            <li class="py-2 border-bottom">
                                <i class="material-icons text-success">celebration</i>
                                <strong>{{ \Carbon\Carbon::parse($holiday->date)->format('M d') }}</strong>
                                - {{ \Carbon\Carbon::parse($holiday->date)->format('l') }}
                            </li>
                        @empty
                            <li class="py-2 text-muted">No holidays in the next 30 days</li>
                        @endforelse
                    </ul>
                </div>
                <div class="card-footer">
                    <a href="/calendar" class="btn btn-simple btn-success">View Calendar</a>
                </div>
            </div>

            <!-- Leave Usage Breakdown -->
            @include('components.leave-balance-card', ['breakdown' => $balanceBreakdown])
        </div>
    </div>

    <style>
        .badge {
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: 500;
        }

        .badge-success {
            background-color: #4caf50;
            color: white;
        }

        .badge-danger {
            background-color: #f44336;
            color: white;
        }

        .badge-warning {
            background-color: #ff9800;
            color: white;
        }

        .badge-info {
            background-color: #00bcd4;
            color: white;
        }

        .py-2 {
            padding-top: 8px;
            padding-bottom: 8px;
        }

        .border-bottom {
            border-bottom: 1px solid #eee;
        }
    </style>
@endsection