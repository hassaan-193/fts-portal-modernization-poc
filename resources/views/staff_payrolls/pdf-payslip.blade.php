<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Pay Slip - {{ $payroll->profile->name }}</title>
    <style>
        @page {
            margin: 10mm 10mm 10mm 10mm;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.3;
            color: #000;
        }
        .container {
            max-width: 750px;
            margin: 0 auto;
        }
        .letterhead-img {
            width: 100%;
            max-width: 100%;
            height: auto;
            display: block;
            margin-bottom: 10px;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
        }
        .header h1 {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 0;
        }
        .employee-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 11px;
        }
        .employee-info-left {
            flex: 1;
        }
        .employee-info-right {
            flex: 1;
            text-align: right;
        }
        .employee-detail {
            margin-bottom: 3px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
        }
        table td {
            padding: 5px;
            border: 1px solid #000;
        }
        .label-col {
            width: 45%;
            font-weight: normal;
            background-color: #fff;
        }
        .value-col {
            width: 27.5%;
            text-align: right;
            background-color: #fff;
        }
        .value-col-center {
            width: 27.5%;
            text-align: center;
            background-color: #fff;
        }
        .section-header {
            font-weight: bold;
            background-color: #f0f0f0;
        }
        .total-row {
            font-weight: bold;
            background-color: #e8e8e8;
        }
        .notes-section {
            margin-top: 10px;
            padding: 8px;
            border: 1px solid #ccc;
            background-color: #fafafa;
            font-size: 10px;
        }
        .signature-section {
            margin-top: 20px;
            display: flex;
            justify-content: space-around;
            gap: 10px;
        }
        .signature-box {
            text-align: center;
            flex: 1;
            font-size: 10px;
        }
        .signature-line {
            border-top: 1px solid #000;
            margin-top: 25px;
            padding-top: 3px;
            min-height: 30px;
        }
        .signature-label {
            font-weight: bold;
            margin-top: 5px;
            font-size: 9px;
        }
        .footer {
            margin-top: 15px;
            text-align: center;
            font-size: 9px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- FTS Letterhead -->
        @php
            $imgPath = public_path('dist/img/fts_latter_head.jpeg');
            if(file_exists($imgPath)) {
                $imgData = base64_encode(file_get_contents($imgPath));
                $letterhead = true;
            } else {
                $letterhead = false;
            }
        @endphp
        @if($letterhead)
            <img class="letterhead-img" src="data:image/jpeg;base64,{{ $imgData }}" />
        @endif

        <!-- Header -->
        <div class="header">
            <h1>PAY SLIP</h1>
        </div>

        <!-- Employee and Period Info -->
        <div class="employee-info">
            <div class="employee-info-left">
                <div class="employee-detail"><strong>Name:</strong> {{ $payroll->profile->name ?? '' }}</div>
                <div class="employee-detail"><strong>Type:</strong> {{ $payroll->profile->staf_type ?? '' }}</div>
                <div class="employee-detail"><strong>Joining Date:</strong> {{ $payroll->profile->joining_date ? \Carbon\Carbon::parse($payroll->profile->joining_date)->format('d-m-Y') : '' }}</div>
            </div>
            <div class="employee-info-right">
                <div class="employee-detail"><strong>Date:</strong> {{ $payroll->date->format('d-m-Y') }}</div>
                <div class="employee-detail"><strong>Month Covered:</strong> {{ $payroll->date->format('F Y') }}</div>
            </div>
        </div>

        <!-- Main Payroll Table -->
        <table>
            <!-- Salary Section -->
            <tr>
                <td class="label-col section-header">SALARY</td>
                <td class="value-col section-header">{{ number_format($payroll->profile->total_salary ?? 0, 0) }}</td>
                <td class="value-col-center"></td>
            </tr>
            <tr>
                <td class="label-col">OVERTIME</td>
                <td class="value-col">{{ number_format(($payroll->profile->overtime_rate ?? 0) * ($payroll->hours ?? 0), 0) }}</td>
                <td class="value-col-center"></td>
            </tr>
            <tr>
                <td class="label-col">Hours</td>
                <td class="value-col">{{ number_format($payroll->hours ?? 0, 2) }}</td>
                <td class="value-col-center"></td>
            </tr>
            <tr class="total-row">
                <td class="label-col">Gross Salary</td>
                <td class="value-col">{{ number_format(($payroll->profile->total_salary ?? 0) + (($payroll->profile->overtime_rate ?? 0) * ($payroll->hours ?? 0)), 0) }}</td>
                <td class="value-col-center"></td>
            </tr>

            <!-- Deduction Section -->
            <tr>
                <td class="label-col section-header">DEDUCTIONS</td>
                <td class="value-col section-header"></td>
                <td class="value-col-center"></td>
            </tr>
            <tr>
                <td class="label-col">Absents</td>
                <td class="value-col">{{ number_format($payroll->absents ?? 0, 0) }}</td>
                <td class="value-col-center"></td>
            </tr>
            <tr>
                <td class="label-col">Advances / Minus Adjustment</td>
                <td class="value-col">{{ number_format($payroll->minus_adjustment ?? 0, 0) }}</td>
                <td class="value-col-center"></td>
            </tr>

            <!-- Adjustments Section -->
            <tr>
                <td class="label-col section-header">ADJUSTMENTS</td>
                <td class="value-col section-header"></td>
                <td class="value-col-center"></td>
            </tr>
            <tr>
                <td class="label-col">Plus Adjustment</td>
                <td class="value-col">{{ number_format($payroll->plus_adjustment ?? 0, 0) }}</td>
                <td class="value-col-center"></td>
            </tr>

            <!-- Net Salary -->
            <tr class="total-row">
                <td class="label-col">Net Salary / Total Amount</td>
                <td class="value-col">{{ number_format($payroll->total_amount ?? 0, 0) }}</td>
                <td class="value-col-center"></td>
            </tr>

            <!-- FTS Accounts -->
            <tr>
                <td class="label-col section-header">FTS Accounts</td>
                <td class="value-col section-header"></td>
                <td class="value-col-center"></td>
            </tr>
        </table>

        <!-- Notes -->
        @if($payroll->note)
        <div class="notes-section">
            <strong>Notes:</strong> {{ $payroll->note }}
        </div>
        @endif



        <!-- Footer -->
        <div class="footer">
            <p>This is a computer-generated document.</p>
            <p>Generated on: {{ now()->format('d-m-Y H:i:s') }}</p>
        </div>
    </div>
</body>
</html>
