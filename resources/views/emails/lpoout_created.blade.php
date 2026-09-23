<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Local Purchase Order</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css">
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
            <h2 style="margin: 0;">Local Purchase Order (LPO) Created</h2>
        </div>
        <p style="margin-top: 20px;">Please acknowledge receipt of this LPO and confirm your ability to fulfill the order as specified.</p>

        <div class="content">
            <p>Dear Vendor,</p>

            <p>A new Local Purchase Order (LPO) has been created and is ready for your review:</p>

            <div class="section-title">LPO Details</div>
            <table class="info-table">
                <tr>
                    <td>LPO Name:</td>
                    <td><strong>{{ $lpoout->name }}</strong></td>
                </tr>
                <tr>
                    <td>LPO Date:</td>
                    <td>{{ $lpoout->date ? \Carbon\Carbon::parse($lpoout->date)->format('Y-m-d') : '-' }}</td>
                </tr>
            </table>

            @php
                // Lump-sum orders are priced as one batch total, so the per-row
                // price columns are left out of the vendor's copy entirely.
                $isLump = $lpoout->isLumpSum();
                $showItemCode = $lpoout->hasItemCode();
            @endphp
            @if($lpoout->items && is_array($lpoout->items) && count($lpoout->items) > 0)
            <div class="section-title">Items</div>
            <div class="table-responsive">
                <table class="table table-bordered table-striped" style="font-size: 12px; margin-bottom: 0;">
                    <thead>
                        <tr style="background-color: #f0f0f0;">
                            <th>NO</th>
                            @if($showItemCode)
                                <th>ITEM CODE</th>
                            @endif
                            <th>DESCRIPTION</th>
                            <th>UNIT</th>
                            <th>QUANTITY</th>
                            @unless($isLump)
                                <th>UNIT PRICE (AED)</th>
                                <th>TOTAL (AED)</th>
                            @endunless
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lpoout->items as $idx => $item)
                        <tr>
                            <td>{{ $idx + 1 }}</td>
                            @if($showItemCode)
                                <td>{{ $item['item_code'] ?? '-' }}</td>
                            @endif
                            <td>{{ $item['description'] ?? '-' }}</td>
                            <td>{{ $item['unit'] ?? '-' }}</td>
                            <td>{{ $item['qty'] ?? '-' }}</td>
                            @unless($isLump)
                                <td>{{ number_format($item['unit_price'] ?? 0, 2) }}</td>
                                <td>{{ number_format($item['total'] ?? 0, 2) }}</td>
                            @endunless
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="section-title" style="margin-top: 20px;">Order Summary</div>
            <table class="info-table">
                <tr>
                    <td>Subtotal (AED):</td>
                    <td><strong>{{ number_format($lpoout->amount, 2) }}</strong></td>
                </tr>
                @if($lpoout->vat)
                <tr>
                    <td>VAT (5%) (AED):</td>
                    <td><strong>{{ number_format($lpoout->vat, 2) }}</strong></td>
                </tr>
                <tr>
                    <td>Total (AED):</td>
                    <td><strong>{{ number_format($lpoout->total_amount, 2) }}</strong></td>
                </tr>
                @else
                <tr>
                    <td>Total (AED):</td>
                    <td><strong>{{ number_format($lpoout->total_amount, 2) }}</strong></td>
                </tr>
                @endif
            </table>
            @endif

            @if($lpoout->terms)
            <div class="section-title">Terms & Conditions</div>
            <div class="bg-light p-2" style="font-size: 11px; line-height: 1.4;">
                {!! $lpoout->terms !!}
            </div>
            @endif

        </div>

        <div class="footer">
            <p>This is an automated email. Please do not reply to this email.</p>
            <p>&copy; {{ date('Y') }} FTS Portal. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
