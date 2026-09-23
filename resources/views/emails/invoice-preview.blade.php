<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 13px;
            color: #333;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        .invoice-container {
            background-color: #ffffff;
            max-width: 800px;
            margin: 20px auto;
            padding: 30px;
            border: 1px solid #ddd;
        }
        .invoice-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #d81b60;
        }
        .invoice-header h1 {
            margin: 0;
            font-size: 24px;
            color: #d81b60;
            text-transform: uppercase;
        }
        .invoice-info {
            margin-bottom: 20px;
        }
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        .info-row {
            display: table-row;
        }
        .info-label {
            display: table-cell;
            width: 35%;
            font-weight: bold;
            padding: 5px 10px;
            border-bottom: 1px solid #eee;
        }
        .info-value {
            display: table-cell;
            width: 65%;
            padding: 5px 10px;
            border-bottom: 1px solid #eee;
        }
        .bill-to {
            margin-top: 20px;
            margin-bottom: 20px;
        }
        .bill-to-title {
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 10px;
        }
        .bill-to-content {
            padding: 10px;
            background-color: #f9f9f9;
            border-left: 3px solid #d81b60;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .items-table th {
            background-color: #d81b60;
            color: white;
            padding: 10px;
            text-align: left;
            font-weight: bold;
        }
        .items-table td {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        .items-table tbody tr:hover {
            background-color: #f5f5f5;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .totals-section {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 2px solid #ddd;
        }
        .total-row {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }
        .total-label {
            display: table-cell;
            width: 60%;
            text-align: right;
            font-weight: bold;
            padding-right: 20px;
        }
        .total-value {
            display: table-cell;
            width: 40%;
            text-align: right;
            padding-left: 20px;
        }
        .grand-total {
            background-color: #d81b60;
            color: white;
            padding: 10px;
            font-size: 16px;
            font-weight: bold;
        }
        .notes-section {
            margin-top: 20px;
            padding: 15px;
            background-color: #f9f9f9;
            border-left: 3px solid #d81b60;
        }
        .notes-title {
            font-weight: bold;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Invoice Header -->
        <div class="invoice-header">
            <h1>INVOICE</h1>
        </div>

        <!-- Invoice Details -->
        <div class="invoice-info">
            <div class="info-row">
                <div class="info-label">Invoice Number:</div>
                <div class="info-value">{{ $invoice->invoice_no }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Invoice Date:</div>
                <div class="info-value">{{ \Carbon\Carbon::parse($invoice->start_date)->format('d/m/Y') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Due Date:</div>
                <div class="info-value">{{ \Carbon\Carbon::parse($invoice->delivery_date)->format('d/m/Y') }}</div>
            </div>
            @if($invoice->quotation->ref_no)
            <div class="info-row">
                <div class="info-label">Reference Number:</div>
                <div class="info-value">{{ $invoice->quotation->ref_no }}</div>
            </div>
            @endif
            @if($invoice->quotation->subject)
            <div class="info-row">
                <div class="info-label">Project:</div>
                <div class="info-value">{{ $invoice->quotation->subject }}</div>
            </div>
            @endif
        </div>

        <!-- Bill To Section -->
        @if($invoice->quotation->company)
        <div class="bill-to">
            <div class="bill-to-title">BILL TO:</div>
            <div class="bill-to-content">
                <strong>{{ $invoice->quotation->company->name }}</strong><br>
                @if($invoice->quotation->company->address)
                    {{ $invoice->quotation->company->address }}<br>
                @endif
                @if($invoice->quotation->company->city)
                    {{ $invoice->quotation->company->city }}@if($invoice->quotation->company->country), {{ $invoice->quotation->company->country }}@endif<br>
                @endif
                @if($invoice->quotation->company->email)
                    Email: {{ $invoice->quotation->company->email }}<br>
                @endif
                @if($invoice->quotation->company->phone)
                    Phone: {{ $invoice->quotation->company->phone }}
                @endif
            </div>
        </div>
        @endif

        <!-- Items Table -->
        @if($invoice->invoice_product_details && count($invoice->invoice_product_details) > 0 || $invoice->invoice_service_details && count($invoice->invoice_service_details) > 0)
        <table class="items-table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th class="text-center">Qty</th>
                    <th class="text-right">Unit Price</th>
                    <th class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->invoice_product_details as $item)
                <tr>
                    <td>{{ $item->product }}</td>
                    <td class="text-center">{{ $item->qty ?? 1 }}</td>
                    <td class="text-right">{{ $invoice->currency }} {{ number_format($item->rate ?? 0, 2) }}</td>
                    <td class="text-right">{{ $invoice->currency }} {{ number_format($item->amount ?? 0, 2) }}</td>
                </tr>
                @endforeach
                @foreach($invoice->invoice_service_details as $item)
                <tr>
                    <td>{{ $item->service_name ?? 'Service' }}</td>
                    <td class="text-center">1</td>
                    <td class="text-right">{{ $invoice->currency }} {{ number_format($item->amount ?? 0, 2) }}</td>
                    <td class="text-right">{{ $invoice->currency }} {{ number_format($item->amount ?? 0, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        <!-- Totals Section -->
        <div class="totals-section">
            @if($invoice->invoice_product_details)
            <div class="total-row">
                <div class="total-label">Subtotal:</div>
                <div class="total-value">{{ $invoice->currency }} {{ number_format($invoice->invoice_product_details->sum('amount') ?? 0, 2) }}</div>
            </div>
            @endif
            
            @if($invoice->vat)
            <div class="total-row">
                <div class="total-label">VAT ({{ $invoice->vat }}%):</div>
                <div class="total-value">{{ $invoice->currency }} {{ number_format(($invoice->total_amount ?? 0) * ($invoice->vat ?? 0) / (100 + ($invoice->vat ?? 0)), 2) }}</div>
            </div>
            @endif

            <div class="total-row grand-total">
                <div class="total-label" style="color: white;">TOTAL DUE:</div>
                <div class="total-value" style="color: white;">{{ $invoice->currency }} {{ number_format($invoice->total_amount ?? 0, 2) }}</div>
            </div>
        </div>

        <!-- Notes Section -->
        @if($invoice->note1 || $invoice->note2)
        <div class="notes-section">
            @if($invoice->note1)
            <div class="notes-title">Terms & Conditions:</div>
            <div>{!! $invoice->note1 !!}</div>
            @endif
            
            @if($invoice->note2)
            <div class="notes-title" style="margin-top: 15px;">Payment Instructions:</div>
            <div>{!! $invoice->note2 !!}</div>
            @endif
        </div>
        @endif

        <!-- Footer -->
        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; font-size: 12px; color: #666; text-align: center;">
            <p>Thank you for your business. If you have any questions, please contact us.</p>
            <p style="margin: 5px 0;">FTS - FIRE TECHNICAL SERVICES | Accounts: accounts.rak@example.com</p>
        </div>
    </div>
</body>
</html>
