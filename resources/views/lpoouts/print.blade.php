@extends('layouts.master')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark text-weight-bold">@lang('crud.detail') @lang('models/lpoouts.singular')</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{!! route('lpoouts.index') !!}">@lang('models/lpoouts.singular')</a></li>
                        <li class="breadcrumb-item active">@lang('crud.detail')</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        @include('flash::message')
        <div class="row">
            <div class="col-md-12">
                <div class="bg-white card-primary card-maroon">
                    <div class="card-header">
                        <h3 class="card-title">@lang('models/lpoouts.singular') Details</h3>
                    </div>

                    <div class="card-body">
                        <div class="print-container">
    <!-- FTS Letterhead -->
    <header class="print-header">
        <img class="fts_img" src="{{ asset('dist/img/fts_latter_head.jpeg') }}" style="width:100%; margin:auto;" />
    </header>

    <!-- LPO Document -->
    <div class="lpo-document" style="background-color: #FFFFFF !important;" >
        <!-- Title -->
        <div class="lpo-title text-center">
            <h3><strong>Local Purchase Order</strong></h3>
        </div>

        <!-- Company Info and Date -->
        <div class="lpo-info-row">
            <table style="width: 100%; margin-bottom: 15px;">
                <tr>
                    <td style="width: 50%; vertical-align: top;">
                        <p style="margin: 3px 0;"><strong>Fire Technical Services</strong></p>
                        {{-- Our own TRN, never the vendor's --}}
                        <p style="margin: 3px 0;"><strong>TRN:</strong> {{ config('purchase-orders.company_trn') }}</p>
                    </td>
                    <td style="width: 50%; text-align: right; vertical-align: top;">
                        <p style="margin: 3px 0;"><strong>Date:</strong> {{ $lpoout->date ? \Carbon\Carbon::parse($lpoout->date)->format('d/m/Y') : 'N/A' }}</p>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Vendor and Ship To Boxes -->
        <table style="width: 100%; margin-bottom: 15px; border-collapse: collapse;">
            <tr>
                <td style="width: 50%; border: 1px solid #000; padding: 10px; vertical-align: top;">
                    <p style="margin: 3px 0;"><strong>Vendor:</strong> {{ $lpoout->vendor->name ?? 'N/A' }}</p>
                    @if($lpoout->kindly_attn)
                    <p style="margin: 3px 0;"><strong>Kindly ATTN:</strong> {{ $lpoout->kindly_attn }}</p>
                    @endif
                </td>
                <td style="width: 50%; border: 1px solid #000; padding: 10px; vertical-align: top;">
                    <p style="margin: 3px 0;"><strong>Ship to:</strong> Fire Technical Services</p>
                    <p style="margin: 3px 0;"><strong>Address:</strong> Ras Al Khaimah</p>
                    <p style="margin: 3px 0;"><strong>Contact:</strong> 055-4937236</p>
                </td>
            </tr>
        </table>

        <!-- Reference Number -->
        <div style="margin-bottom: 10px;">
            <p style="margin: 3px 0;"><strong>Ref no:</strong> {{ $lpoout->lpo_invoice_no ?? 'N/A' }}</p>
        </div>

        <!-- Terms & Conditions -->
        @if($lpoout->terms)
        <div style="margin-bottom: 15px;">
            <p style="margin: 5px 0; font-weight: bold;">Terms & Conditions</p>
            <div style="font-size: 11px; margin: 5px 0 5px 20px; padding: 0;">
                {!! $lpoout->terms !!}
            </div>
        </div>
        @endif

        <!-- Items Table -->
        @php
            // Lump-sum orders are priced as one batch total, so the per-row price
            // columns are dropped entirely rather than printed empty.
            $isLump = $lpoout->isLumpSum();
            $showItemCode = $lpoout->hasItemCode();
            $columnCount = 3 + ($showItemCode ? 1 : 0) + ($isLump ? 0 : 2);
        @endphp
        <table class="lpo-items-table" style="width: 100%; border-collapse: collapse; margin-bottom: 15px;">
            <thead>
                <tr style="background-color: #FFFFFF;">
                    <th style="border: 1px solid #000; padding: 6px; text-align: left; font-size: 12px;">NO</th>
                    @if($showItemCode)
                    <th style="border: 1px solid #000; padding: 6px; text-align: left; font-size: 12px;">ITEM CODE</th>
                    @endif
                    <th style="border: 1px solid #000; padding: 6px; text-align: left; font-size: 12px;">ITEM DESCRIPTION</th>
                    <th style="border: 1px solid #000; padding: 6px; text-align: center; font-size: 12px;">UNIT</th>
                    <th style="border: 1px solid #000; padding: 6px; text-align: center; font-size: 12px;">QTY</th>
                    @unless($isLump)
                    <th style="border: 1px solid #000; padding: 6px; text-align: right; font-size: 12px;">UNIT PRICE (AED)</th>
                    <th style="border: 1px solid #000; padding: 6px; text-align: right; font-size: 12px;">TOTAL (AED)</th>
                    @endunless
                </tr>
            </thead>
            <tbody>
                @if($lpoout->items && count($lpoout->items) > 0)
                    @foreach($lpoout->items as $index => $item)
                    <tr>
                        <td style="border: 1px solid #000; padding: 6px; font-size: 11px;">{{ $index + 1 }}</td>
                        @if($showItemCode)
                        <td style="border: 1px solid #000; padding: 6px; font-size: 11px;">{{ $item['item_code'] ?? '' }}</td>
                        @endif
                        <td style="border: 1px solid #000; padding: 6px; font-size: 11px;">{{ $item['description'] ?? 'N/A' }}</td>
                        <td style="border: 1px solid #000; padding: 6px; text-align: center; font-size: 11px;">{{ $item['unit'] ?? 'LS' }}</td>
                        <td style="border: 1px solid #000; padding: 6px; text-align: center; font-size: 11px;">{{ $item['qty'] ?? 'LOT' }}</td>
                        @unless($isLump)
                        <td style="border: 1px solid #000; padding: 6px; text-align: right; font-size: 11px;">{{ isset($item['unit_price']) ? number_format($item['unit_price'], 2) : '' }}</td>
                        <td style="border: 1px solid #000; padding: 6px; text-align: right; font-size: 11px;">{{ isset($item['total']) ? number_format($item['total'], 2) : '' }}</td>
                        @endunless
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="{{ $columnCount }}" style="border: 1px solid #000; padding: 6px; text-align: center; font-size: 11px;">No items available</td>
                    </tr>
                @endif
            </tbody>
        </table>

        <!-- Totals Table -->
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 30px;">
            <tr>
                <td style="width: 70%; border: 1px solid #000; padding: 8px; text-align: right; font-size: 12px;"><strong>TOTAL AMOUNT (IN AED)</strong></td>
                <td style="width: 30%; border: 1px solid #000; padding: 8px; text-align: right; font-size: 12px;"><strong>AED {{ number_format($lpoout->amount, 2) }}</strong></td>
            </tr>
            @if($lpoout->vat > 0)
            <tr>
                <td style="width: 70%; border: 1px solid #000; padding: 8px; text-align: right; font-size: 12px;"><strong>VAT (5%) AMOUNT (IN AED)</strong></td>
                <td style="width: 30%; border: 1px solid #000; padding: 8px; text-align: right; font-size: 12px;"><strong>AED {{ number_format($lpoout->vat, 2) }}</strong></td>
            </tr>
            <tr>
                <td style="width: 70%; border: 1px solid #000; padding: 8px; text-align: right; font-size: 12px;"><strong>TOTAL SUM VALUE INCLUDING VAT (5%) (IN AED)</strong></td>
                <td style="width: 30%; border: 1px solid #000; padding: 8px; text-align: right; font-size: 12px;"><strong>AED {{ number_format($lpoout->total_amount, 2) }}</strong></td>
            </tr>
            @endif
        </table>

        <!-- Signature Section -->
        <div style="margin-top: 50px; display: flex; justify-content: center;">
            <p>This is a computer-generated document. No signature is required.</p>
        </div>
    </div>
</div>

{{-- Download Merged PDF button (hidden during print) --}}
<div class="text-center my-3 no-print">
    <a href="{{ route('lpoouts.downloadPdf', $lpoout->id) }}" class="btn btn-primary">
        <i class="fa fa-file-pdf-o"></i> Download Merged PDF (LPO + Attachments)
    </a>
</div>

<style>
    @media print {
        .no-print { display: none !important; }
    }
</style>

{{-- Auto print --}}
<script>
    window.addEventListener('DOMContentLoaded', function () {
        window.print();
    });
</script>  
                  </div>
                </div>
            </div>        
        </div>
    </div>
@endsection
