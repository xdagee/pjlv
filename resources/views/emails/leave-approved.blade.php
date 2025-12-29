<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Leave Request Approved</title>
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
            background: #2196F3;
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
            border-left: 4px solid #2196F3;
        }

        .footer {
            text-align: center;
            padding: 20px;
            color: #666;
            font-size: 12px;
        }

        .success {
            color: #4CAF50;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>✓ Leave Request Approved</h1>
        </div>
        <div class="content">
            <p>Dear {{ $applicant->firstname }},</p>
            <p class="success">Great news! Your leave request has been approved.</p>

            <div class="details">
                <h3>Approved Leave Details</h3>
                <p><strong>Start Date:</strong> {{ $leave->start_date }}</p>
                <p><strong>End Date:</strong> {{ $leave->end_date }}</p>
                <p><strong>Duration:</strong> {{ $leave->leave_days }} day(s)</p>
                <p><strong>Approved By:</strong> {{ $approver->firstname }} {{ $approver->lastname }}</p>
            </div>

            <p>Please ensure you complete any handover tasks before your leave begins.</p>
            <p>Enjoy your time off!</p>
        </div>
        <div class="footer">
            <p>This is an automated message from the Leave Management System.</p>
        </div>
    </div>
</body>

</html>