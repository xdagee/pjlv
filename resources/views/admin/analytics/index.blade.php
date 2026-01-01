@extends('layouts.admin')

@section('content')
    <div class="row">
        <!-- Page Header -->
        <div class="col-md-12">
            <h3 class="title" style="margin-bottom: 20px;">
                <i class="material-icons" style="vertical-align: middle;">analytics</i>
                Leave Analytics Dashboard
            </h3>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-lg-3 col-md-6 col-sm-6">
            <div class="card card-stats">
                <div class="card-header" data-background-color="blue">
                    <i class="material-icons">assignment</i>
                </div>
                <div class="card-content">
                    <p class="category">Total Requests</p>
                    <h3 class="card-title">{{ $stats['total_requests'] }}</h3>
                </div>
                <div class="card-footer">
                    <div class="stats">
                        <i class="material-icons">date_range</i>
                        {{ $year }}{{ $month ? ' - ' . date('F', mktime(0, 0, 0, $month, 1)) : '' }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6">
            <div class="card card-stats">
                <div class="card-header" data-background-color="green">
                    <i class="material-icons">check_circle</i>
                </div>
                <div class="card-content">
                    <p class="category">Approved</p>
                    <h3 class="card-title">{{ $stats['approved'] }}</h3>
                </div>
                <div class="card-footer">
                    <div class="stats">
                        <i class="material-icons">trending_up</i> {{ $stats['approval_rate'] }}% approval rate
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6">
            <div class="card card-stats">
                <div class="card-header" data-background-color="orange">
                    <i class="material-icons">pending</i>
                </div>
                <div class="card-content">
                    <p class="category">Pending</p>
                    <h3 class="card-title">{{ $stats['pending'] }}</h3>
                </div>
                <div class="card-footer">
                    <div class="stats">
                        <i class="material-icons">hourglass_empty</i> Awaiting action
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6">
            <div class="card card-stats">
                <div class="card-header" data-background-color="purple">
                    <i class="material-icons">event_busy</i>
                </div>
                <div class="card-content">
                    <p class="category">Total Days Used</p>
                    <h3 class="card-title">{{ $stats['total_days'] }}</h3>
                </div>
                <div class="card-footer">
                    <div class="stats">
                        <i class="material-icons">trending_flat</i> Avg: {{ $stats['avg_days_per_request'] }} days/request
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-content">
                    <form method="GET" action="{{ route('admin.analytics.index') }}" class="form-inline">
                        <div class="form-group" style="margin-right: 15px;">
                            <label>Year:</label>
                            <select name="year" class="form-control" style="margin-left: 10px;">
                                @foreach($years as $y)
                                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group" style="margin-right: 15px;">
                            <label>Month:</label>
                            <select name="month" class="form-control" style="margin-left: 10px;">
                                <option value="">All Months</option>
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                        {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="material-icons">filter_list</i> Apply Filter
                        </button>
                        <a href="{{ route('admin.analytics.export', ['year' => $year, 'month' => $month]) }}"
                            class="btn btn-success btn-sm" style="margin-left: 10px;">
                            <i class="material-icons">download</i> Export CSV
                        </a>
                        <a href="{{ route('admin.analytics.export-pdf', ['year' => $year, 'month' => $month]) }}"
                            class="btn btn-info btn-sm" style="margin-left: 10px;">
                            <i class="material-icons">picture_as_pdf</i> Export PDF
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <!-- Monthly Trend Chart -->
        <div class="col-lg-8 col-md-12">
            <div class="card">
                <div class="card-header" data-background-color="green">
                    <h4 class="title">Monthly Leave Trend ({{ $year }})</h4>
                    <p class="category">Leave days used per month</p>
                </div>
                <div class="card-content">
                    <noscript>
                        <div class="alert alert-warning">
                            JavaScript is required to display charts. Please enable JavaScript.
                        </div>
                    </noscript>
                    <canvas id="monthlyChart" height="100"></canvas>
                </div>
            </div>
        </div>

        <!-- Yearly Comparison Chart -->
        <div class="col-lg-4 col-md-12">
            <div class="card">
                <div class="card-header" data-background-color="blue">
                    <h4 class="title">Yearly Comparison</h4>
                    <p class="category">Total days by year</p>
                </div>
                <div class="card-content">
                    <canvas id="yearlyChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Leave by Department -->
        <div class="col-lg-6 col-md-12">
            <div class="card">
                <div class="card-header" data-background-color="rose">
                    <h4 class="title">Leave by Department</h4>
                    <p class="category">Breakdown of leave usage per department</p>
                </div>
                <div class="card-content table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Department</th>
                                <th class="text-right">Requests</th>
                                <th class="text-right">Days</th>
                                <th class="text-right">Staff</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($leavesByDepartment as $item)
                                <tr>
                                    <td>{{ $item->department_name }}</td>
                                    <td class="text-right">{{ $item->total_requests }}</td>
                                    <td class="text-right">{{ $item->total_days }}</td>
                                    <td class="text-right">{{ $item->unique_staff }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No data available</td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($leavesByDepartment->count() > 0)
                            <tfoot>
                                <tr style="font-weight: bold; background-color: #f5f5f5;">
                                    <td>Total</td>
                                    <td class="text-right">{{ $leavesByDepartment->sum('total_requests') }}</td>
                                    <td class="text-right">{{ $leavesByDepartment->sum('total_days') }}</td>
                                    <td class="text-right">{{ $leavesByDepartment->sum('unique_staff') }}</td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        <!-- Leave by Type -->
        <div class="col-lg-6 col-md-12">
            <div class="card">
                <div class="card-header" data-background-color="orange">
                    <h4 class="title">Leave by Type</h4>
                    <p class="category">Usage breakdown by leave category</p>
                </div>
                <div class="card-content table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Leave Type</th>
                                <th class="text-right">Allocated</th>
                                <th class="text-right">Requests</th>
                                <th class="text-right">Days Used</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($leavesByType as $item)
                                <tr>
                                    <td>{{ $item->leave_type_name }}</td>
                                    <td class="text-right">{{ $item->allocated_days }}</td>
                                    <td class="text-right">{{ $item->total_requests }}</td>
                                    <td class="text-right">{{ $item->total_days }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No data available</td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($leavesByType->count() > 0)
                            <tfoot>
                                <tr style="font-weight: bold; background-color: #f5f5f5;">
                                    <td>Total</td>
                                    <td class="text-right">-</td>
                                    <td class="text-right">{{ $leavesByType->sum('total_requests') }}</td>
                                    <td class="text-right">{{ $leavesByType->sum('total_days') }}</td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Leave Takers -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header" data-background-color="purple">
                    <h4 class="title">Top Leave Takers</h4>
                    <p class="category">Staff members with highest leave usage</p>
                </div>
                <div class="card-content table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Staff Name</th>
                                <th>Department</th>
                                <th class="text-right">Requests</th>
                                <th class="text-right">Days Used</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topLeaveTakers as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $item->firstname }} {{ $item->lastname }}</td>
                                    <td>{{ $item->department_name }}</td>
                                    <td class="text-right">{{ $item->total_requests }}</td>
                                    <td class="text-right">{{ $item->total_days }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No data available</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Department Pie Chart -->
    <div class="row">
        <div class="col-lg-6 col-md-12">
            <div class="card">
                <div class="card-header" data-background-color="rose">
                    <h4 class="title">Department Distribution</h4>
                    <p class="category">Leave days by department</p>
                </div>
                <div class="card-content">
                    <canvas id="departmentPieChart" height="200"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-md-12">
            <div class="card">
                <div class="card-header" data-background-color="orange">
                    <h4 class="title">Leave Type Distribution</h4>
                    <p class="category">Leave days by type</p>
                </div>
                <div class="card-content">
                    <canvas id="leaveTypePieChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Monthly Trend Chart
            var monthlyCtx = document.getElementById('monthlyChart');
            if (monthlyCtx) {
                var monthlyData = @json($monthlyTrends);
                var labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                var days = labels.map((_, i) => monthlyData[i + 1]?.total_days || 0);
                var requests = labels.map((_, i) => monthlyData[i + 1]?.total_requests || 0);

                new Chart(monthlyCtx.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Days Used',
                            data: days,
                            backgroundColor: 'rgba(75, 192, 192, 0.6)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 1,
                            yAxisID: 'y'
                        }, {
                            label: 'Requests',
                            data: requests,
                            type: 'line',
                            borderColor: 'rgba(255, 99, 132, 1)',
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            fill: false,
                            tension: 0.3,
                            yAxisID: 'y1'
                        }]
                    },
                    options: {
                        responsive: true,
                        interaction: {
                            mode: 'index',
                            intersect: false
                        },
                        scales: {
                            y: {
                                type: 'linear',
                                display: true,
                                position: 'left',
                                beginAtZero: true,
                                title: { display: true, text: 'Days' }
                            },
                            y1: {
                                type: 'linear',
                                display: true,
                                position: 'right',
                                beginAtZero: true,
                                title: { display: true, text: 'Requests' },
                                grid: { drawOnChartArea: false }
                            }
                        }
                    }
                });
            }

            // Yearly Comparison Chart
            var yearlyCtx = document.getElementById('yearlyChart');
            if (yearlyCtx) {
                var yearlyData = @json($yearlyTrends);
                var yearLabels = Object.values(yearlyData).map(d => d.year);
                var yearDays = Object.values(yearlyData).map(d => d.total_days);

                new Chart(yearlyCtx.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: yearLabels,
                        datasets: [{
                            label: 'Total Days',
                            data: yearDays,
                            backgroundColor: [
                                'rgba(255, 99, 132, 0.6)',
                                'rgba(54, 162, 235, 0.6)',
                                'rgba(75, 192, 192, 0.6)'
                            ],
                            borderColor: [
                                'rgba(255, 99, 132, 1)',
                                'rgba(54, 162, 235, 1)',
                                'rgba(75, 192, 192, 1)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }

            // Department Pie Chart
            var deptPieCtx = document.getElementById('departmentPieChart');
            if (deptPieCtx) {
                var deptData = @json($leavesByDepartment);
                var deptLabels = deptData.map(d => d.department_name);
                var deptValues = deptData.map(d => d.total_days);
                var deptColors = deptLabels.map((_, i) => `hsl(${(i * 360 / deptLabels.length)}, 70%, 60%)`);

                new Chart(deptPieCtx.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: deptLabels,
                        datasets: [{
                            data: deptValues,
                            backgroundColor: deptColors
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            }

            // Leave Type Pie Chart
            var typePieCtx = document.getElementById('leaveTypePieChart');
            if (typePieCtx) {
                var typeData = @json($leavesByType);
                var typeLabels = typeData.map(d => d.leave_type_name);
                var typeValues = typeData.map(d => d.total_days);
                var typeColors = [
                    'rgba(255, 99, 132, 0.8)',
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(255, 206, 86, 0.8)',
                    'rgba(75, 192, 192, 0.8)',
                    'rgba(153, 102, 255, 0.8)',
                    'rgba(255, 159, 64, 0.8)'
                ];

                new Chart(typePieCtx.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: typeLabels,
                        datasets: [{
                            data: typeValues,
                            backgroundColor: typeColors
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            }
        });
    </script>
@endsection