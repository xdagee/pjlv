<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Analytics Report - {{ $year }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid #333;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #2c3e50;
        }

        .header p {
            margin: 5px 0 0;
            color: #666;
        }

        .section {
            margin-bottom: 25px;
        }

        .section-title {
            background-color: #3498db;
            color: white;
            padding: 8px 15px;
            margin-bottom: 15px;
            font-size: 14px;
            font-weight: bold;
        }

        .stats-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .stat-box {
            display: table-cell;
            width: 25%;
            text-align: center;
            padding: 10px;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
        }

        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
        }

        .stat-label {
            font-size: 11px;
            color: #666;
            text-transform: uppercase;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f5f5f5;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #fafafa;
        }

        .text-right {
            text-align: right;
        }

        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            font-size: 10px;
            color: #666;
            text-align: center;
        }

        .no-data {
            text-align: center;
            color: #999;
            font-style: italic;
            padding: 20px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Leave Analytics Report</h1>
        <p>
            Period: {{ $year }}{{ $monthName ? " - {$monthName}" : ' (Full Year)' }}
            <br>
            Generated: {{ $data['generated_at'] }}
        </p>
    </div>

    <!-- Overview Statistics -->
    <div class="section">
        <div class="section-title">Overview Statistics</div>
        <div class="stats-grid">
            <div class="stat-box">
                <div class="stat-value">{{ $data['overview']['total_requests'] }}</div>
                <div class="stat-label">Total Requests</div>
            </div>
            <div class="stat-box">
                <div class="stat-value">{{ $data['overview']['approved'] }}</div>
                <div class="stat-label">Approved</div>
            </div>
            <div class="stat-box">
                <div class="stat-value">{{ $data['overview']['pending'] }}</div>
                <div class="stat-label">Pending</div>
            </div>
            <div class="stat-box">
                <div class="stat-value">{{ $data['overview']['total_days'] }}</div>
                <div class="stat-label">Days Used</div>
            </div>
        </div>
        <table>
            <tr>
                <th>Metric</th>
                <th class="text-right">Value</th>
            </tr>
            <tr>
                <td>Rejected Requests</td>
                <td class="text-right">{{ $data['overview']['rejected'] }}</td>
            </tr>
            <tr>
                <td>Average Days per Request</td>
                <td class="text-right">{{ $data['overview']['avg_days_per_request'] }}</td>
            </tr>
            <tr>
                <td>Approval Rate</td>
                <td class="text-right">{{ $data['overview']['approval_rate'] }}%</td>
            </tr>
            <tr>
                <td>Departments with Leaves</td>
                <td class="text-right">{{ $data['overview']['departments_with_leaves'] }}</td>
            </tr>
        </table>
    </div>

    <!-- Leaves by Department -->
    <div class="section">
        <div class="section-title">Leaves by Department</div>
        @if(count($data['by_department']) > 0)
            <table>
                <thead>
                    <tr>
                        <th>Department</th>
                        <th class="text-right">Requests</th>
                        <th class="text-right">Days Used</th>
                        <th class="text-right">Unique Staff</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['by_department'] as $dept)
                        <tr>
                            <td>{{ $dept->department_name }}</td>
                            <td class="text-right">{{ $dept->total_requests }}</td>
                            <td class="text-right">{{ $dept->total_days }}</td>
                            <td class="text-right">{{ $dept->unique_staff }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="no-data">No department data available for this period.</div>
        @endif
    </div>

    <!-- Leaves by Type -->
    <div class="section">
        <div class="section-title">Leaves by Type</div>
        @if(count($data['by_type']) > 0)
            <table>
                <thead>
                    <tr>
                        <th>Leave Type</th>
                        <th class="text-right">Allocated Days</th>
                        <th class="text-right">Requests</th>
                        <th class="text-right">Days Used</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['by_type'] as $type)
                        <tr>
                            <td>{{ $type->leave_type_name }}</td>
                            <td class="text-right">{{ $type->allocated_days }}</td>
                            <td class="text-right">{{ $type->total_requests }}</td>
                            <td class="text-right">{{ $type->total_days }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="no-data">No leave type data available for this period.</div>
        @endif
    </div>

    <!-- Monthly Trends -->
    <div class="section">
        <div class="section-title">Monthly Trends ({{ $year }})</div>
        <table>
            <thead>
                <tr>
                    <th>Month</th>
                    <th class="text-right">Requests</th>
                    <th class="text-right">Days Used</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['monthly_trends'] as $trend)
                    <tr>
                        <td>{{ $trend->month_name }}</td>
                        <td class="text-right">{{ $trend->total_requests }}</td>
                        <td class="text-right">{{ $trend->total_days }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Top Leave Takers -->
    <div class="section">
        <div class="section-title">Top Leave Takers</div>
        @if(count($data['top_leave_takers']) > 0)
            <table>
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
                    @foreach($data['top_leave_takers'] as $index => $staff)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $staff->firstname }} {{ $staff->lastname }}</td>
                            <td>{{ $staff->department_name }}</td>
                            <td class="text-right">{{ $staff->total_requests }}</td>
                            <td class="text-right">{{ $staff->total_days }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="no-data">No staff data available for this period.</div>
        @endif
    </div>

    <div class="footer">
        PJLV Leave Management System - Analytics Report
        <br>
        This report is confidential and intended for administrative use only.
    </div>
</body>

</html>