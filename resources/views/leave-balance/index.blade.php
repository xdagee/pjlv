@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header" data-background-color="purple">
                    <h4 class="title">Leave Balance Dashboard</h4>
                    <p class="category">Your complete leave balance overview</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row">
        <!-- Total Allocated -->
        <div class="col-lg-4 col-md-6 col-sm-6">
            <div class="card card-stats">
                <div class="card-header" data-background-color="blue">
                    <i class="material-icons">event_note</i>
                </div>
                <div class="card-content">
                    <p class="category">Total Allocated</p>
                    <h3 class="card-title" aria-label="Total allocated leave days">
                        {{ $balanceBreakdown['total_allowance'] }} <small>days</small>
                    </h3>
                </div>
                <div class="card-footer">
                    <div class="stats">
                        <i class="material-icons">calendar_today</i> Annual entitlement
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Used -->
        <div class="col-lg-4 col-md-6 col-sm-6">
            <div class="card card-stats">
                <div class="card-header" data-background-color="orange">
                    <i class="material-icons">event_busy</i>
                </div>
                <div class="card-content">
                    <p class="category">Total Used</p>
                    <h3 class="card-title" aria-label="Total used leave days">
                        {{ $balanceBreakdown['total_used'] }} <small>days</small>
                    </h3>
                </div>
                <div class="card-footer">
                    <div class="stats">
                        <i class="material-icons">check_circle</i> Approved & pending leaves
                    </div>
                </div>
            </div>
        </div>

        <!-- Remaining Balance -->
        <div class="col-lg-4 col-md-6 col-sm-6">
            <div class="card card-stats">
                <div class="card-header" data-background-color="green">
                    <i class="material-icons">event_available</i>
                </div>
                <div class="card-content">
                    <p class="category">Remaining Balance</p>
                    <h3 class="card-title" aria-label="Remaining leave days">
                        {{ $balanceBreakdown['remaining'] }} <small>days</small>
                    </h3>
                </div>
                <div class="card-footer">
                    <div class="stats">
                        <i class="material-icons">info</i> Available for new requests
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Leave Breakdown by Type -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header" data-background-color="green">
                    <h4 class="title">Leave Breakdown by Type</h4>
                    <p class="category">Detailed balance per leave category</p>
                </div>
                <div class="card-content table-responsive">
                    <table class="table table-hover" aria-label="Leave balance breakdown by type">
                        <thead class="text-success">
                            <tr>
                                <th>Leave Type</th>
                                <th class="text-center">Allocated</th>
                                <th class="text-center">Used</th>
                                <th class="text-center">Remaining</th>
                                <th style="width: 30%">Usage</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($balanceBreakdown['by_type'] as $typeName => $data)
                                @php
                                    $allocated = $data['allocated'] ?? 0;
                                    $used = $data['used'] ?? 0;
                                    $remaining = $data['remaining'] ?? 0;
                                    $percentage = $allocated > 0 ? round(($used / $allocated) * 100) : 0;

                                    // Color coding based on usage
                                    $progressClass = 'progress-bar-success';
                                    if ($percentage > 75) {
                                        $progressClass = 'progress-bar-danger';
                                    } elseif ($percentage > 50) {
                                        $progressClass = 'progress-bar-warning';
                                    }
                                @endphp
                                <tr>
                                    <td>
                                        <strong>{{ $typeName }}</strong>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-info">{{ $allocated }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-warning">{{ $used }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-success">{{ $remaining }}</span>
                                    </td>
                                    <td>
                                        <div class="progress" style="height: 20px; margin-bottom: 0;" role="progressbar"
                                            aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100"
                                            aria-label="{{ $typeName }} usage: {{ $percentage }}%">
                                            <div class="progress-bar {{ $progressClass }}"
                                                style="width: {{ $percentage }}%; line-height: 20px;">
                                                @if($percentage > 15)
                                                    {{ $percentage }}%
                                                @endif
                                            </div>
                                        </div>
                                        @if($percentage <= 15 && $allocated > 0)
                                            <small class="text-muted">{{ $percentage }}% used</small>
                                        @elseif($allocated == 0)
                                            <small class="text-muted">N/A</small>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">
                                        <i class="material-icons" style="vertical-align: middle;">info</i>
                                        No leave types configured
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <div class="legend">
                        <span class="legend-item">
                            <i class="material-icons text-success" style="font-size: 14px;">circle</i>
                            <small>0-50% used</small>
                        </span>
                        <span class="legend-item">
                            <i class="material-icons text-warning" style="font-size: 14px;">circle</i>
                            <small>51-75% used</small>
                        </span>
                        <span class="legend-item">
                            <i class="material-icons text-danger" style="font-size: 14px;">circle</i>
                            <small>76-100% used</small>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="row">
        <div class="col-md-12">
            <a href="/dashboard" class="btn btn-default">
                <i class="material-icons">arrow_back</i> Back to Dashboard
            </a>
            <a href="/leaves/apply" class="btn btn-success">
                <i class="material-icons">add</i> Apply for Leave
            </a>
            <a href="/leaves" class="btn btn-info">
                <i class="material-icons">list</i> View My Leaves
            </a>
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

        .badge-warning {
            background-color: #ff9800;
            color: white;
        }

        .badge-info {
            background-color: #00bcd4;
            color: white;
        }

        .badge-danger {
            background-color: #f44336;
            color: white;
        }

        .progress {
            background-color: #e9ecef;
            border-radius: 4px;
            overflow: hidden;
        }

        .progress-bar {
            text-align: center;
            color: white;
            font-size: 11px;
            font-weight: 500;
            transition: width 0.6s ease;
        }

        .progress-bar-success {
            background-color: #4caf50;
        }

        .progress-bar-warning {
            background-color: #ff9800;
        }

        .progress-bar-danger {
            background-color: #f44336;
        }

        .legend {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .card-stats .card-header i {
            font-size: 36px;
        }

        .btn i.material-icons {
            vertical-align: middle;
            margin-right: 5px;
        }

        @media (max-width: 768px) {
            .legend {
                justify-content: center;
            }
        }
    </style>
@endsection