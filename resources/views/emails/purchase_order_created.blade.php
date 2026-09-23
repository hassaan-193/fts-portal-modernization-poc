<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Purchase Order Request</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
        }
        .email-container {
            max-width: 600px;
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
            width: 40%;
        }
        .section-title {
            font-weight: bold;
            background-color: #f0f0f0;
            padding: 8px;
            margin-top: 15px;
            margin-bottom: 10px;
        }
        .footer {
            padding: 15px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #ddd;
        }
        .badge {
            display: inline-block;
            padding: 5px 10px;
            background-color: #5cb85c;
            color: white;
            border-radius: 3px;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <p style="margin-top: 20px;">Please review this purchase order and add cost information in the system.</p>
        
        <div class="header">
            <h2 style="margin: 0;">New Purchase Order Request</h2>
        </div>

        <div class="content">
            <p>Dear Department Reviewer,</p>

            <p>A new Purchase Order request has been created and is awaiting your review and cost information:</p>

            <div class="section-title">Purchase Order Details</div>
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
                    <td>Date:</td>
                    <td>{{ $po->date ? $po->date->format('Y-m-d') : '-' }}</td>
                </tr>
                <tr>
                    <td>Due Date:</td>
                    <td>{{ $po->due_date ? $po->due_date->format('Y-m-d') : '-' }}</td>
                </tr>
                @if($po->delivery_date)
                <tr>
                    <td>Delivery Date:</td>
                    <td>{{ $po->delivery_date->format('Y-m-d') }}</td>
                </tr>
                @endif
                @if($po->quotation)
                <tr>
                    <td>Quotation:</td>
                    <td>{{ $po->quotation->name ?? '-' }}</td>
                </tr>
                @endif
                @if($po->project)
                <tr>
                    <td>Project:</td>
                    <td>{{ $po->project->subject ?? '-' }}</td>
                </tr>
                @endif
            </table>

            <div class="section-title">LPOUT Information</div>
            <table class="info-table">
                <tr>
                    <td>Vendor:</td>
                    <td>{{ ($po->vendor && $po->vendor->name) ? $po->vendor->name : '-' }}</td>
                </tr>
                <tr>
                    <td>TRN No (Company):</td>
                    <td>{{ config('purchase-orders.company_trn') }}</td>
                </tr>
                <tr>
                    <td>Kindly Attn:</td>
                    <td>{{ $po->lpout_kindly_attn ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Payment Type:</td>
                    <td>{{ $po->lpout_payment_type ?? '-' }}</td>
                </tr>
            </table>

            @if($po->other_info)
            <div class="section-title">Additional Information</div>
            <p>{{ $po->other_info }}</p>
            @endif

            @php
                // Lump-sum requests carry no per-row cost, so those columns are dropped.
                $showCost = !$po->isLumpSum() && isset($po->items[0]['cost']);
                $showItemCode = $po->hasItemCode();
            @endphp
            @if($po->items && is_array($po->items) && count($po->items) > 0)
            <div class="section-title">Items</div>
            <table class="table table-bordered table-striped" style="font-size: 12px;">
                <thead>
                    <tr style="background-color: #f0f0f0;">
                        @if($showItemCode)
                        <th>Item Code</th>
                        @endif
                        <th>Material Name</th>
                        <th>Qty</th>
                        <th>Unit</th>
                        @if($showCost)
                        <th>Cost (AED)</th>
                        <th>Total (AED)</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($po->items as $item)
                    <tr>
                        @if($showItemCode)
                        <td>{{ $item['item_code'] ?? '-' }}</td>
                        @endif
                        <td>{{ $item['material_name'] ?? '-' }}</td>
                        <td>{{ $item['quantity'] ?? '-' }}</td>
                        <td>{{ $item['unit'] ?? '-' }}</td>
                        @if($showCost)
                        <td>{{ number_format($item['cost'] ?? 0, 2) }}</td>
                        <td>{{ number_format($item['total'] ?? 0, 2) }}</td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @if($po->isLumpSum() && $po->lpout_manual_total)
            <p><strong>Lump-Sum Total (AED):</strong> {{ number_format($po->lpout_manual_total, 2) }}</p>
            @endif
            @endif

        </div>

        <div class="footer">
            <p>This is an automated email. Please do not reply to this email.</p>
            <p>&copy; {{ date('Y') }} FTS Portal. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
