<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Order Request Sent Back</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
        }
        .email-container {
            max-width: 700px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .header {
            background-color: #8b0000;
            color: white;
            padding: 15px;
            border-radius: 5px 5px 0 0;
            text-align: center;
        }
        .content {
            padding: 20px;
            background-color: #f9f9f9;
        }
        .info-table {
            width: 100%;
            margin: 15px 0;
            border-collapse: collapse;
        }
        .info-table tr {
            border-bottom: 1px solid #ddd;
        }
        .info-table td {
            padding: 8px;
        }
        .info-table td:first-child {
            font-weight: bold;
            width: 35%;
        }
        .section-title {
            font-weight: bold;
            background-color: #f0f0f0;
            padding: 8px;
            margin-top: 15px;
            margin-bottom: 10px;
        }
        .reason {
            background-color: #fff3cd;
            border: 1px solid #ffeeba;
            padding: 12px;
            border-radius: 4px;
        }
        .footer {
            padding: 15px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <div class="email-container">

        <div class="header">
            <h2 style="margin: 0;">Purchase Order Request Sent Back</h2>
        </div>

        <div class="content">
            <p>Hello,</p>

            <p>
                The department review has sent purchase order request
                <strong>{{ $po->request_number }}</strong> back to you for correction.
                Please update it and submit it again so it can continue to admin approval.
            </p>

            <div class="section-title">Request Details</div>
            <table class="info-table">
                <tr>
                    <td>Request Number:</td>
                    <td><strong>{{ $po->request_number }}</strong></td>
                </tr>
                <tr>
                    <td>Request Type:</td>
                    <td>{{ ucfirst($po->request_type) }}</td>
                </tr>
                <tr>
                    <td>Vendor:</td>
                    <td>{{ ($po->vendor && $po->vendor->name) ? $po->vendor->name : '-' }}</td>
                </tr>
                <tr>
                    <td>Date:</td>
                    <td>{{ $po->date ? $po->date->format('Y-m-d') : '-' }}</td>
                </tr>
            </table>

            <div class="section-title">Reason</div>
            <div class="reason">{!! nl2br(e($notes ?? $po->sent_back_notes ?? 'No reason provided.')) !!}</div>

            <p style="margin-top: 20px;">
                <a href="{{ route('purchase-orders.edit', $po->id) }}">Open the request to correct and resubmit it</a>
            </p>
        </div>

        <div class="footer">
            <p>This is an automated email. Please do not reply to this email.</p>
            <p>&copy; {{ date('Y') }} FTS Portal. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
