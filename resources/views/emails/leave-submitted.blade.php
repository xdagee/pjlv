<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>New Leave Request</title>
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
            background: #4CAF50;
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
            border-left: 4px solid #4CAF50;
        }

        .footer {
            text-align: center;
            padding: 20px;
            color: #666;
            font-size: 12px;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>New Leave Request</h1>
        </div>
        <div class="content">
            <p>Hello,</p>
            <p>A new leave request has been submitted and requires your attention.</p>

            <div class="details">
                <h3>Request Details</h3>
                <p><strong>Employee:</strong> {{ $applicant->firstname }} {{ $applicant->lastname }}</p>
                <p><strong>Staff Number:</strong> {{ $applicant->staff_number }}</p>
                <p><strong>Start Date:</strong> {{ $leave->start_date }}</p>
                <p><strong>End Date:</strong> {{ $leave->end_date }}</p>
                <p><strong>Duration:</strong> {{ $leave->leave_days }} day(s)</p>
            </div>

            <p>Please login to the system to review and take action on this request.</p>

            <p style="text-align: center;">
                <a href="{{ url('/leaves/' . $leave->id) }}" class="btn">View Request</a>
            </p>
        </div>
        <div class="footer">
            <p>This is an automated message from the Leave Management System.</p>
        </div>
    </div>
</body>

</html>