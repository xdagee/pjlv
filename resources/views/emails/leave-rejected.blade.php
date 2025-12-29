<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Leave Request Rejected</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: #f44336;
            color: white;
            padding: 20px;
            text-align: center;
        }

        .content {
            padding: 20px;
            background: #f9f9f9;
        }

        .details {
            background: white;
            padding: 15px;
            margin: 15px 0;
            border-left: 4px solid #f44336;
        }

        .reason {
            background: #fff3cd;
            padding: 15px;
            margin: 15px 0;
            border: 1px solid #ffc107;
        }

        .footer {
            text-align: center;
            padding: 20px;
            color: #666;
            font-size: 12px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Leave Request Rejected</h1>
        </div>
        <div class="content">
            <p>Dear {{ $applicant->firstname }},</p>
            <p>We regret to inform you that your leave request has been rejected.</p>

            <div class="details">
                <h3>Request Details</h3>
                <p><strong>Start Date:</strong> {{ $leave->start_date }}</p>
                <p><strong>End Date:</strong> {{ $leave->end_date }}</p>
                <p><strong>Duration:</strong> {{ $leave->leave_days }} day(s)</p>
                <p><strong>Rejected By:</strong> {{ $rejector->firstname }} {{ $rejector->lastname }}</p>
            </div>

            @if($reason)
                <div class="reason">
                    <h4>Reason for Rejection:</h4>
                    <p>{{ $reason }}</p>
                </div>
            @endif

            <p>If you have any questions, please contact your supervisor or HR department.</p>
        </div>
        <div class="footer">
            <p>This is an automated message from the Leave Management System.</p>
        </div>
    </div>
</body>

</html>