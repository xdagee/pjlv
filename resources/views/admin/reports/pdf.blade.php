<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Leave Report {{ $year }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #333;
        }

        h1 {
            text-align: center;
            color: #4a4a4a;
            border-bottom: 2px solid #4CAF50;
            padding-bottom: 10px;
        }

        h2 {
            color: #4CAF50;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            margin-top: 25px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .stats-grid {
            display: flex;
            justify-content: space-around;
            margin-bottom: 20px;
        }

        .stat-box {
            text-align: center;
            padding: 10px 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .stat-box .value {
            font-size: 24px;
            font-weight: bold;
            color: #4CAF50;
        }

        .stat-box .label {
            font-size: 10px;
            color: #777;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 10px;
            color: #999;
        }
    </style>
</head>

<body>
    <h1>Leave Report - {{ $year }}</h1>

    <h2>Summary Statistics</h2>
    <table>
        <tr>
            <th>Total Requests</th>
            <th>Approved</th>
            <th>Total Days Used</th>
        </tr>
        <tr>
            <td>{{ $stats['total_requests'] }}</td>
            <td>{{ $stats['approved'] }}</td>
            <td>{{ $stats['total_days'] }}</td>
        </tr>
    </table>

    <h2>Leave by Type</h2>
    <table>
        <tr>
            <th>Leave Type</th>
            <th>Requests</th>
            <th>Days</th>
        </tr>
        @forelse($leaveByType as $item)
            <tr>
                <td>{{ $item->leaveType->leave_type_name ?? 'Unknown' }}</td>
                <td>{{ $item->total_requests }}</td>
                <td>{{ $item->total_days }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="3" style="text-align: center;">No data</td>
            </tr>
        @endforelse
    </table>

    <h2>Leave by Department</h2>
    <table>
        <tr>
            <th>Department</th>
            <th>Requests</th>
            <th>Days</th>
        </tr>
        @forelse($leaveByDepartment as $item)
            <tr>
                <td>{{ $item->department->name ?? 'Unassigned' }}</td>
                <td>{{ $item->total_requests }}</td>
                <td>{{ $item->total_days }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="3" style="text-align: center;">No data</td>
            </tr>
        @endforelse
    </table>

    <h2>Top Leave Takers</h2>
    <table>
        <tr>
            <th>Staff Name</th>
            <th>Days Used</th>
        </tr>
        @forelse($topLeaveTakers as $item)
            <tr>
                <td>{{ ($item->staff->firstname ?? '') . ' ' . ($item->staff->lastname ?? '') }}</td>
                <td>{{ $item->total_days }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="2" style="text-align: center;">No data</td>
            </tr>
        @endforelse
    </table>

    <div class="footer">
        Generated on {{ now()->toDateTimeString() }} | PJLV Leave Management System
    </div>
</body>

</html>