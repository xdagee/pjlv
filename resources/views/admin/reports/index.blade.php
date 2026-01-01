@extends('layouts.admin')

@section('content')
    <div class="row">
        <!-- Statistics Cards -->
        <div class="col-lg-3 col-md-6 col-sm-6">
            <div class="card card-stats">
                <div class="card-header" data-background-color="blue">
                    <i class="material-icons">assignment</i>
                </div>
                <div class="card-content">
                    <p class="category">Total Requests</p>
                    <h3 class="card-title">{{ $stats['total_requests'] }}</h3>
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
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-content">
                    <form method="GET" action="/reports" class="form-inline">
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
                        <button type="submit" class="btn btn-primary btn-sm">Apply Filter</button>
                        <a href="/reports/export?year={{ $year }}" class="btn btn-success btn-sm"
                            style="margin-left: 10px;">
                            <i class="material-icons">download</i> Export CSV
                        </a>
                        <a href="/reports/export-pdf?year={{ $year }}" class="btn btn-info btn-sm"
                            style="margin-left: 10px;">
                            <i class="material-icons">picture_as_pdf</i> Export PDF
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Leave by Type -->
        <div class="col-lg-6 col-md-12">
            <div class="card">
                <div class="card-header" data-background-color="rose">
                    <h4 class="title">Leave by Type</h4>
                </div>
                <div class="card-content table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Leave Type</th>
                                <th>Requests</th>
                                <th>Days</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($leaveByType as $item)
                                <tr>
                                    <td>{{ $item->leaveType->leave_type_name ?? 'Unknown' }}</td>
                                    <td>{{ $item->total_requests }}</td>
                                    <td>{{ $item->total_days }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">No data available</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Top Leave Takers -->
        <div class="col-lg-6 col-md-12">
            <div class="card">
                <div class="card-header" data-background-color="orange">
                    <h4 class="title">Top Leave Takers</h4>
                </div>
                <div class="card-content table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Staff</th>
                                <th>Days Used</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topLeaveTakers as $item)
                                <tr>
                                    <td>{{ ($item->staff->firstname ?? '') . ' ' . ($item->staff->lastname ?? '') }}</td>
                                    <td>{{ $item->total_days }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center">No data available</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Leave by Department -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header" data-background-color="blue">
                    <h4 class="title">Leave by Department</h4>
                </div>
                <div class="card-content table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Department</th>
                                <th>Requests</th>
                                <th>Days Used</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($leaveByDepartment as $item)
                                <tr>
                                    <td>{{ $item->department->name ?? 'Unassigned' }}</td>
                                    <td>{{ $item->total_requests }}</td>
                                    <td>{{ $item->total_days }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">No data available</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Trend -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header" data-background-color="green">
                    <h4 class="title">Monthly Leave Trend ({{ $year }})</h4>
                </div>
                <div class="card-content">
                    <canvas id="monthlyChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var ctx = document.getElementById('monthlyChart').getContext('2d');
            var monthlyData = @json($monthlyTrend);

            var labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            var data = labels.map((_, i) => monthlyData[i + 1]?.total_days || 0);

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Days Used',
                        data: data,
                        backgroundColor: 'rgba(75, 192, 192, 0.6)',
                        borderColor: 'rgba(75, 192, 192, 1)',
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
        });
    </script>
@endsection