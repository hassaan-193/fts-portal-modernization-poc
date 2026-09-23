@php
    // Lump-sum orders carry no per-row price, so those columns are dropped from
    // both the on-screen items table and the embedded print block below.
    $isLump = $lpoout->isLumpSum();
    $showItemCode = $lpoout->hasItemCode();
@endphp
<!-- Normal View - Shows on Screen -->
<div class="screen-only">
    <div class="row">
        <div class="col-md-4 col-sm-6">
            <!-- LPO Invoice No Field -->
            <li class="callout callout-danger list-group-item mb-3 shadow">
                <b>@lang('models/lpoouts.fields.lpo_invoice_no')</b> <br>
                @php
                    $quotation = optional($lpoout->project)->quotation ?? null;
                @endphp
                @if($quotation && $quotation->id)
                    <a class="text-left text-primary" href="{{ route('quotations.show', $quotation->id) }}">{{ $lpoout->lpo_invoice_no ?? 'N/A' }}</a>
                @else
                    <a class="text-left">{{ $lpoout->lpo_invoice_no ?? 'N/A' }}</a>
                @endif
            </li>
            
            <!-- Name Field -->
            <li class="callout callout-danger list-group-item mb-3 shadow">
                <b>@lang('models/lpoouts.fields.name')</b> <br>
                <a class="text-left">{{ $lpoout->name }}</a>
            </li>
            
            <!-- Date Field -->
            <li class="callout callout-danger list-group-item mb-3 shadow">
                <b>@lang('models/lpoouts.fields.date')</b> <br>
                <a class="text-left">{{ $lpoout->date }}</a>
            </li>

            <!-- TRN No Field (our own company TRN, never the vendor's) -->
            <li class="callout callout-danger list-group-item mb-3 shadow">
                <b>TRN No (Company)</b> <br>
                <a class="text-left">{{ config('purchase-orders.company_trn') }}</a>
            </li>
        </div>

        <div class="col-md-4 col-sm-6">
            <!-- Lpo Type Out Field -->
            <li class="callout callout-danger list-group-item mb-3 shadow">
                <b>@lang('models/lpoouts.fields.lpo_out_type_id')</b> <br>
                <a class="text-left">{{ $lpoout->lpo_out_type->name }}</a>
            </li>

            @if($lpoout->project_id)
            <!-- Project Field -->
            <li class="callout callout-danger list-group-item mb-3 shadow">
                <b>@lang('models/lpoouts.fields.project_id')</b> <br>
                <a class="text-left">{{ $lpoout->project->subject ?? 'N/A' }}</a>
            </li>
            @endif
            
            <!-- Amount Field -->
            <li class="callout callout-danger list-group-item mb-3 shadow">
                <b>@lang('models/lpoouts.fields.amount')</b> <br>
                <a class="text-left">{{ number_format($lpoout->amount, 2) }} AED</a>
            </li>

            <!-- VAT Field -->
            <li class="callout callout-danger list-group-item mb-3 shadow">
                <b>@lang('models/lpoouts.fields.vat')</b> <br>
                <a class="text-left">{{ $lpoout->vat > 0 ? number_format($lpoout->vat, 2) . ' AED' : 'No VAT' }}</a>
            </li>
        </div>

        <div class="col-md-4 col-sm-6">
            <!-- Vendor Id Field -->
            <li class="callout callout-danger list-group-item mb-3 shadow">
                <b>@lang('models/lpoouts.fields.vendor_id')</b> <br>
                <a class="text-left text-primary" href="{{ route('vendors.show',$lpoout->vendor_id) }}">{{ $lpoout->vendor->name }}</a>
            </li>

            <!-- Kindly Attn Field -->
            <li class="callout callout-danger list-group-item mb-3 shadow">
                <b>Kindly Attn</b> <br>
                <a class="text-left">{{ $lpoout->kindly_attn ?? 'N/A' }}</a>
            </li>

            <!-- Payment Type Field -->
            <li class="callout callout-danger list-group-item mb-3 shadow">
                <b>Payment Type</b> <br>
                <a class="text-left">{{ $lpoout->payment_type ?? 'N/A' }}</a>
            </li>

            @if($lpoout->payment_type == 'Cheque' && $lpoout->cheque_date)
            <!-- Cheque Date Field -->
            <li class="callout callout-danger list-group-item mb-3 shadow">
                <b>Cheque Date</b> <br>
                <a class="text-left">{{ $lpoout->cheque_date }}</a>
            </li>
            @endif

            <!-- Total Amount Field -->
            <li class="callout callout-danger list-group-item mb-3 shadow">
                <b>Total Amount </b> <br>
                <a class="text-left">{{ number_format($lpoout->total_amount, 2) }} AED</a>
            </li>
        </div>
    </div>
    

    <!-- Payment Preference Section -->
    <div class="row mt-4">
        <div class="col-md-12">
            <h4 class="text-danger">@lang('models/lpoouts.fields.lpo_payment_preference')</h4>
            <hr>
        </div>
    </div>

    

    @if($lpoout->lpo_payment_preference)
        <!-- Custom Payment Preference -->
        <div class="row">
            <div class="col-md-4 col-sm-6">
                <li class="callout callout-warning list-group-item mb-3 shadow">
                    <b>Payment Preference (Custom)</b> <br>
                    <a class="text-left">
                        @if($lpoout->lpo_payment_preference === 'cod')
                            Cash on Delivery
                        @elseif($lpoout->lpo_payment_preference === 'pdc')
                            PDC (Post Dated Check)
                        @else
                            {{ $lpoout->lpo_payment_preference }}
                        @endif
                    </a>
                </li>
            </div>
            @if($lpoout->lpo_payment_preference === 'pdc')
            <div class="col-md-4 col-sm-6">
                <li class="callout callout-warning list-group-item mb-3 shadow">
                    <b>@lang('models/lpoouts.fields.lpo_pdc_number_of_days')</b> <br>
                    <a class="text-left">{{ $lpoout->lpo_pdc_number_of_days }}</a>
                </li>
            </div>
            <div class="col-md-4 col-sm-6">
                <li class="callout callout-warning list-group-item mb-3 shadow">
                    <b>@lang('models/lpoouts.fields.lpo_pdc_payment_option')</b> <br>
                    <a class="text-left">
                        @if($lpoout->lpo_pdc_payment_option === 'on_delivery_amount')
                            On Delivery
                        @elseif($lpoout->lpo_pdc_payment_option === 'on_payment_release_amount')
                            On PO Release
                        @else
                            {{ $lpoout->lpo_pdc_payment_option }}
                        @endif
                    </a>
                </li>
            </div>
            @endif
        </div>
    @else
        <!-- Vendor's Default Preference -->
        <div class="row">
            <div class="col-md-4 col-sm-6">
                <li class="callout callout-info list-group-item mb-3 shadow">
                    <b>Payment Preference (Vendor Default)</b> <br>
                    <a class="text-left">
                        @if($lpoout->vendor->payment_preference === 'cod')
                            Cash on Delivery
                        @elseif($lpoout->vendor->payment_preference === 'pdc')
                            PDC (Post Dated Check)
                        @else
                            {{ $lpoout->vendor->payment_preference ?? 'Not Set' }}
                        @endif
                    </a>
                </li>
            </div>
            @if($lpoout->vendor->payment_preference === 'pdc')
            <div class="col-md-4 col-sm-6">
                <li class="callout callout-info list-group-item mb-3 shadow">
                    <b>@lang('models/vendors.fields.pdc_number_of_days')</b> <br>
                    <a class="text-left">{{ $lpoout->vendor->pdc_number_of_days ?? '-' }}</a>
                </li>
            </div>
            <div class="col-md-4 col-sm-6">
                <li class="callout callout-info list-group-item mb-3 shadow">
                    <b>@lang('models/vendors.fields.pdc_payment_option')</b> <br>
                    <a class="text-left">
                        @if($lpoout->vendor->pdc_payment_option === 'on_delivery_amount')
                            On Delivery
                        @elseif($lpoout->vendor->pdc_payment_option === 'on_payment_release_amount')
                            On PO Release
                        @else
                            {{ $lpoout->vendor->pdc_payment_option ?? '-' }}
                        @endif
                    </a>
                </li>
            </div>
            @endif
        </div>
    @endif



    <!-- Items Table (Normal View) -->
    @if($lpoout->items && count($lpoout->items) > 0)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-danger">
                    <h5 class="mb-0 text-white">Items</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>NO</th>
                                    @if($showItemCode)
                                        <th>Item Code</th>
                                    @endif
                                    <th>Description</th>
                                    <th>Unit</th>
                                    <th>QTY</th>
                                    @unless($isLump)
                                        <th>Unit Price (AED)</th>
                                        <th>Total (AED)</th>
                                    @endunless
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($lpoout->items as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    @if($showItemCode)
                                        <td>{{ $item['item_code'] ?? '-' }}</td>
                                    @endif
                                    <td>{{ $item['description'] ?? 'N/A' }}</td>
                                    <td>{{ $item['unit'] ?? 'LS' }}</td>
                                    <td>{{ $item['qty'] ?? 'N/A' }}</td>
                                    @unless($isLump)
                                        <td>{{ isset($item['unit_price']) && $item['unit_price'] > 0 ? number_format($item['unit_price'], 2) : '-' }}</td>
                                        <td>{{ isset($item['total']) && $item['total'] > 0 ? number_format($item['total'], 2) : '-' }}</td>
                                    @endunless
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    <table class="table table-borderless">
        <tr>
            <th style="width:70%">TOTAL AMOUNT (IN AED)</th>
            <td>{{ number_format($lpoout->amount, 2) }}</td>
        </tr>

        @if($lpoout->vat)
        <tr>
            <th>VAT (5%) (IN AED)</th>
            <td>{{ number_format($lpoout->vat, 2) }}</td>
        </tr>
        <tr>
            <th>TOTAL SUM VALUE INCLUDING VAT (5%) (IN AED)</th>
            <td>{{ number_format($lpoout->total_amount, 2) }}</td>
        </tr>
        @endif
    </table>

                    {{-- Vendor Invoice (PaymentInvoice) Section --}}
            <div class="col-md-12 mt-3">
                <div class="bg-white card-primary card-danger">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0">Vendor Invoices</h3>
                        <a href="{{ route('invoiceRequests.create_with_lpoout', $lpoout->id) }}"
                           class="btn btn-sm btn-danger btn-flat">
                            <i class="fa fa-plus"></i> Request Payment
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-bordered table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Invoice No</th>
                                    <th>Amount (AED)</th>
                                    <th>VAT (AED)</th>
                                    <th>Total (AED)</th>
                                    <th>Status</th>
                                    <th>Payment</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($lpoout->paymentInvoices as $pi)
                                <tr>
                                    <td>{{ $pi->invoice_no }}</td>
                                    <td>{{ number_format($pi->amount, 2) }}</td>
                                    <td>{{ number_format($pi->vat, 2) }}</td>
                                    <td>{{ number_format($pi->total_amount, 2) }}</td>
                                    <td>
                                        @if($pi->status)
                                            <span class="badge badge-success">Paid</span>
                                        @else
                                            <span class="badge badge-warning">Unpaid</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($pi->transaction)
                                            <small class="text-muted">{{ $pi->transaction->date_time }}</small>
                                        @else
                                            <a href="{{ route('payments.create') }}" class="btn btn-xs btn-outline-danger">
                                                Record Payment
                                            </a>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('paymentInvoices.show', $pi->id) }}"
                                           class="btn btn-xs btn-info">View</a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">
                                        No vendor invoices yet.
                                        <a href="{{ route('invoiceRequests.create_with_lpoout', $lpoout->id) }}">Request one.</a>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                            @if($lpoout->paymentInvoices->count())
                            <tfoot>
                                <tr class="font-weight-bold">
                                    <td>Totals</td>
                                    <td>{{ number_format($lpoout->paymentInvoices->sum('amount'), 2) }}</td>
                                    <td>{{ number_format($lpoout->paymentInvoices->sum('vat'), 2) }}</td>
                                    <td>{{ number_format($lpoout->paymentInvoices->sum('total_amount'), 2) }}</td>
                                    <td colspan="3">
                                        {{ $lpoout->paymentInvoices->where('status', 1)->count() }} paid /
                                        {{ $lpoout->paymentInvoices->where('status', 0)->count() }} unpaid
                                    </td>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>

    <div class="row" id="terms_section">
        <div class="col-md-12 col-sm-12">
            <div class="form-group">
                {!! Form::label('terms', 'Terms & Conditions:') !!}
                {!! Form::textarea('terms', isset($lpoout) ? $lpoout->terms : '
            <ul>
                <li>Invoice must accompany goods or to be mailed before the time of collection, LPO number should appear on all invoices</li>
                <li>Payment: 90 days PDC, based on partial material deliveries & partial payments</li>
                <li>Goods (Materials, products or services) to be supplied as specified in the Purchase order, FTS reserves the right to accept / reject any items not up to our required quality or specifications.</li>
                <li>Failure to prepare the shipment on the date specified or subsequently agreed shall entitle FTS to cancel order without penalties to FTS or refuse to accept any subsequent delivery of the goods which the supplier attempts to make.</li>
                <li>Any rejected material due to manufacturing errors, the supplier is full responsible about the replacement and FTS has the right to hold the payment until the issue is resolved & job is delivered.</li>
                <li>Delivery: Delivery of items to be as per the attached sheet in the LPO provided to be delivered at RAK Airport.</li>
                <li>All the design drawings, supporting documents with UL/FM certifications, test certificates, Equipment Warranties for two (2) years, Functional Manuals and related documents under scope.</li>
                <li>All Material submittals with relevant UL/FM approval compliance documents, MTC, Supply & warranty certificates to be under scope.</li>
                <li>Strictly to follow UL/FM approval with standard designs & to provide all the supporting documents for Gate passes etc.</li>
                <li>Penalty Clause: 5% delay penalty against passing of each week as per client T&C, FTS Reserves the right to cancel this PO upon delays in design submissions, material delivery delays etc without any obligations/Liabilities on MS FTS</li>
            </ul>
                ', ['class' => 'form-control summernote']) !!}
            </div>
        </div>
    </div>

    <button onclick="window.print()" class="btn btn-danger" style="padding: 8px 20px; font-size: 14px;">
        Print Report
    </button>
</div>

<!-- Print View - Professional LPO Format with FTS Letterhead -->
<div class="print-only">
    <div class="print-container">
        <!-- FTS Letterhead -->
        <header class="print-header">
            <img class="fts_img" src="{{asset('dist/img/fts_latter_head.jpeg')}}" style="width:100%; margin:auto;" />
        </header>

        <!-- LPO Document -->
        <div class="lpo-document">
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
            @php $printColumnCount = 3 + ($showItemCode ? 1 : 0) + ($isLump ? 0 : 2); @endphp
            <table class="lpo-items-table" style="width: 100%; border-collapse: collapse; margin-bottom: 15px;">
                <thead>
                    <tr style="background-color: #f0f0f0;">
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
                            <td style="border: 1px solid #000; padding: 6px; text-align: right; font-size: 11px;">{{ isset($item['unit_price']) ? number_format($item['unit_price'], 0) : '' }}</td>
                            <td style="border: 1px solid #000; padding: 6px; text-align: right; font-size: 11px;">{{ isset($item['total']) ? number_format($item['total'], 0) : '' }}</td>
                            @endunless
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="{{ $printColumnCount }}" style="border: 1px solid #000; padding: 6px; text-align: center; font-size: 11px;">No items available</td>
                        </tr>
                    @endif
                </tbody>
            </table>

            <!-- Totals Table -->
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 30px;">
                <tr>
                    <td style="width: 70%; border: 1px solid #000; padding: 8px; text-align: right; font-size: 12px;"><strong>TOTAL AMOUNT (IN AED)</strong></td>
                    <td style="width: 30%; border: 1px solid #000; padding: 8px; text-align: right; font-size: 12px;"><strong>AED {{ number_format($lpoout->amount, 0) }}</strong></td>
                </tr>
                @if($lpoout->vat > 0)
                <tr>
                    <td style="width: 70%; border: 1px solid #000; padding: 8px; text-align: right; font-size: 12px;"><strong>VAT (5%) AMOUNT (IN AED)</strong></td>
                    <td style="width: 30%; border: 1px solid #000; padding: 8px; text-align: right; font-size: 12px;"><strong>AED {{ number_format($lpoout->vat, 0) }}</strong></td>
                </tr>
                <tr>
                    <td style="width: 70%; border: 1px solid #000; padding: 8px; text-align: right; font-size: 12px;"><strong>TOTAL SUM VALUE INCLUDING VAT (5%) (IN AED)</strong></td>
                    <td style="width: 30%; border: 1px solid #000; padding: 8px; text-align: right; font-size: 12px;"><strong>AED {{ number_format($lpoout->total_amount, 0) }}</strong></td>
                </tr>
                @endif
            </table>

            <!-- Signature Section -->
            <div style="margin-top: 50px; display: flex; justify-content: center;">
				<p>This is a computer-generated document. No signature is required.</p>
            </div>


        </div>


    </div>

	<div class="row">
		<div class="col-xs-12 text-center">
			<img class="fts_img images" src="{{asset('dist/img/fts_letter_footer.jpg')}}" style="width:100%;margin:auto;" />
			<img class="expert_img images" src="{{asset('dist/img/experts_letter_footer.jpeg')}}" style="width:100%; margin:auto; display:none;" />
			<img class="ftsits_img images" src="{{asset('dist/img/ftsits_letter_footer.jpeg')}}" style="width:100%;margin:auto; display:none;" />
		</div>
	</div>
</div>

@section('css')
@parent
<style>
    /* Hide print view on screen */
    .print-only {
        display: none;
    }

    /* Show normal view on screen */
    .screen-only {
        display: block;
    }

    /* Print styles */
    @media print {
        /* Hide everything except print content */
        body * {
            visibility: hidden;
        }

        .print-only,
        .print-only * {
            visibility: visible;
        }

        .print-only {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            display: block !important;
        }

        /* Hide screen view when printing */
        .screen-only {
            display: none !important;
        }

        /* Hide navigation, buttons, sidebars */
        .content-header,
        .breadcrumb,
        .card-header,
        .btn,
        nav,
        .sidebar,
        .main-header,
        .main-footer,
        .main-sidebar {
            display: none !important;
            visibility: hidden !important;
        }

        /* Reset page layout */
        body {
            margin: 0;
            padding: 0;
            background-color: white;
        }

        .content-wrapper {
            margin: 0 !important;
            padding: 0 !important;
        }

        /* Print container styling */
        .print-container {
            width: 100%;
            padding: 15px;
            background-color: white;
        }

        /* Letterhead */
        .print-header img {
            width: 100%;
            max-width: 100%;
            display: block;
            margin-bottom: 10px;
        }

        /* LPO Document */
        .lpo-document {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #000;
            padding: 10px;
        }

        .lpo-title h3 {
            margin: 10px 0 15px 0;
            font-size: 20px;
        }

        p {
            margin: 3px 0;
        }

        /* Prevent page breaks inside important elements */
        .lpo-items-table {
            page-break-inside: auto;
        }

        .lpo-items-table tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        table {
            page-break-inside: avoid;
        }

        /* Remove any extra margins/padding */
        @page {
            margin: 10mm;
        }
    }
</style>
@endsection

@section('scripts')
@parent
<script src="{{ asset('plugins/summernote/summernote-bs4.min.js') }}"></script>
<script>
$(function () {
    $('.summernote').summernote({
        height: 250, // set editor height
        toolbar: [
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['insert', ['link']],
            ['view', ['fullscreen', 'codeview']]
        ]
    });
});
</script>
@endsection