<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>LPO {{ $lpoout->lpo_invoice_no ?? '' }}</title>
    <style>
        @page {
            margin: 15mm 10mm 15mm 10mm;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #000;
            margin: 0;
            padding: 0;
        }
        .print-container {
            width: 100%;
        }
        .letterhead-img {
            width: 100%;
            max-width: 100%;
            height: auto;
            display: block;
        }
        .lpo-document {
            background-color: #FFFFFF;
            padding: 10px 0;
        }
        .lpo-title {
            text-align: center;
            margin-bottom: 15px;
        }
        .lpo-title h3 {
            margin: 0;
            font-size: 18px;
        }
        table {
            border-collapse: collapse;
            width: 100%;
        }
        .info-table td {
            vertical-align: top;
            padding: 3px 0;
        }
        .vendor-table td {
            border: 1px solid #000;
            padding: 10px;
            vertical-align: top;
            width: 50%;
        }
        .items-table {
            width: 100%;
            margin-bottom: 15px;
        }
        .items-table th {
            border: 1px solid #000;
            padding: 6px;
            font-size: 12px;
            text-align: left;
            background-color: #f5f5f5;
        }
        .items-table td {
            border: 1px solid #000;
            padding: 6px;
            font-size: 11px;
        }
        .totals-table {
            width: 100%;
            margin-bottom: 30px;
        }
        .totals-table td {
            border: 1px solid #000;
            padding: 8px;
            font-size: 12px;
        }
        p {
            margin: 3px 0;
        }
    </style>
</head>
<body>
    <div class="print-container">
        <!-- FTS Letterhead -->
        @php
            $imgPath = public_path('dist/img/fts_latter_head.jpeg');
            $imgData = base64_encode(file_get_contents($imgPath));
        @endphp
        <img class="letterhead-img" src="data:image/jpeg;base64,{{ $imgData }}" />

        <div class="lpo-document">
            <!-- Title -->
            <div class="lpo-title">
                <h3><strong>Local Purchase Order</strong></h3>
            </div>

            <!-- Company Info and Date -->
            <table class="info-table" style="margin-bottom: 15px;">
                <tr>
                    <td style="width: 50%;">
                        <p><strong>Fire Technical Services</strong></p>
                        {{-- Our own TRN, never the vendor's --}}
                        <p><strong>TRN:</strong> {{ config('purchase-orders.company_trn') }}</p>
                    </td>
                    <td style="width: 50%; text-align: right;">
                        <p><strong>Date:</strong> {{ $lpoout->date ? \Carbon\Carbon::parse($lpoout->date)->format('d/m/Y') : 'N/A' }}</p>
                    </td>
                </tr>
            </table>

            <!-- Vendor and Ship To Boxes -->
            <table class="vendor-table" style="margin-bottom: 15px;">
                <tr>
                    <td>
                        <p><strong>Vendor:</strong> {{ $lpoout->vendor->name ?? 'N/A' }}</p>
                        @if($lpoout->kindly_attn)
                        <p><strong>Kindly ATTN:</strong> {{ $lpoout->kindly_attn }}</p>
                        @endif
                    </td>
                    <td>
                        <p><strong>Ship to:</strong> Fire Technical Services</p>
                        <p><strong>Address:</strong> Ras Al Khaimah</p>
                        <p><strong>Contact:</strong> 055-4937236</p>
                    </td>
                </tr>
            </table>

            <!-- Reference Number -->
            <div style="margin-bottom: 10px;">
                <p><strong>Ref no:</strong> {{ $lpoout->lpo_invoice_no ?? 'N/A' }}</p>
            </div>

            <!-- Terms & Conditions -->
            @if($lpoout->terms)
            <div style="margin-bottom: 15px;">
                <p style="font-weight: bold;">Terms & Conditions</p>
                <div style="font-size: 11px; margin: 5px 0 5px 20px;">
                    {!! $lpoout->terms !!}
                </div>
            </div>
            @endif

            <!-- Items Table -->
            @php
                // Lump-sum orders are priced as one batch total, so the per-row
                // price columns are dropped rather than printed empty. Column
                // widths are redistributed to whatever is actually shown.
                $isLump = $lpoout->isLumpSum();
                $showItemCode = $lpoout->hasItemCode();
                $columnCount = 3 + ($showItemCode ? 1 : 0) + ($isLump ? 0 : 2);
                $codeWidth = $showItemCode ? 15 : 0;
                $priceWidth = $isLump ? 0 : 35;
                // 100% less NO (5), UNIT (10), QTY (10) and whichever optional columns are on
                $descriptionWidth = 75 - $codeWidth - $priceWidth;
            @endphp
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 5%; text-align: left;">NO</th>
                        @if($showItemCode)
                        <th style="width: {{ $codeWidth }}%; text-align: left;">ITEM CODE</th>
                        @endif
                        <th style="width: {{ $descriptionWidth }}%; text-align: left;">ITEM DESCRIPTION</th>
                        <th style="width: 10%; text-align: center;">UNIT</th>
                        <th style="width: 10%; text-align: center;">QTY</th>
                        @unless($isLump)
                        <th style="width: 17%; text-align: right;">UNIT PRICE (AED)</th>
                        <th style="width: 18%; text-align: right;">TOTAL (AED)</th>
                        @endunless
                    </tr>
                </thead>
                <tbody>
                    @if($lpoout->items && count($lpoout->items) > 0)
                        @foreach($lpoout->items as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            @if($showItemCode)
                            <td>{{ $item['item_code'] ?? '' }}</td>
                            @endif
                            <td>{{ $item['description'] ?? 'N/A' }}</td>
                            <td style="text-align: center;">{{ $item['unit'] ?? 'LS' }}</td>
                            <td style="text-align: center;">{{ $item['qty'] ?? 'LOT' }}</td>
                            @unless($isLump)
                            <td style="text-align: right;">{{ isset($item['unit_price']) ? number_format($item['unit_price'], 2) : '' }}</td>
                            <td style="text-align: right;">{{ isset($item['total']) ? number_format($item['total'], 2) : '' }}</td>
                            @endunless
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="{{ $columnCount }}" style="text-align: center;">No items available</td>
                        </tr>
                    @endif
                </tbody>
            </table>

            <!-- Totals -->
            <table class="totals-table">
                <tr>
                    <td style="width: 70%; text-align: right;"><strong>TOTAL AMOUNT (IN AED)</strong></td>
                    <td style="width: 30%; text-align: right;"><strong>AED {{ number_format($lpoout->amount, 2) }}</strong></td>
                </tr>
                @if($lpoout->vat > 0)
                <tr>
                    <td style="width: 70%; text-align: right;"><strong>VAT (5%) AMOUNT (IN AED)</strong></td>
                    <td style="width: 30%; text-align: right;"><strong>AED {{ number_format($lpoout->vat, 2) }}</strong></td>
                </tr>
                <tr>
                    <td style="width: 70%; text-align: right;"><strong>TOTAL SUM VALUE INCLUDING VAT (5%) (IN AED)</strong></td>
                    <td style="width: 30%; text-align: right;"><strong>AED {{ number_format($lpoout->total_amount, 2) }}</strong></td>
                </tr>
                @endif
            </table>

            <!-- Signature Section -->
            <div style="margin-top: 50px; text-align: center;">
                <p>This is a computer-generated document. No signature is required.</p>
            </div>
        </div>
    </div>
</body>
</html>
