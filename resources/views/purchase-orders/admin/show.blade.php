@extends('layouts.master')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark text-weight-bold">Approve Purchase Order</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{!! route('purchase-orders.adminIndex') !!}">Admin Approval</a></li>
                    <li class="breadcrumb-item active">Review</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="row">
        <div class="col-md-12">
            @include('flash::message')
            
            <div class="bg-white card-primary card-maroon">
                <div class="card-header">
                    <h3 class="card-title">{{ $po->request_number }} - Final Approval</h3>
                </div>
                
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-2">
                            <strong>Request Type:</strong><br>{{ ucfirst($po->request_type) }}
                        </div>
                        <div class="col-md-3">
                        @if($po->project_id && $po->project)
                            <div class="col-md-4">
                                <strong>Project:</strong><br>{{ $po->project->subject ?? '-' }}
                            </div>
                        @else
                            <div class="col-md-4">
                                <strong>Quotation:</strong><br>{{ $po->quotation->name ?? '-' }}
                            </div>
                        @endif
                        </div>
                        <div class="col-md-2">
                            <strong>Date:</strong><br>{{ $po->date->format('Y-m-d') ?? '-' }}
                        </div>
                        <div class="col-md-2">
                            <strong>Status:</strong><br>
                            <span class="badge badge-info">{{ $po->status }}</span>
                        </div>
                        <div class="col-md-2">
                            <strong>Dept Status:</strong><br>
                            <span class="badge badge-success">{{ $po->department_status }}</span>
                        </div>
                    </div>

                    @php
                        $resolvedProject = null;
                        $showProjectStats = false;
                        $poContractValue = 0;
                        $poSpent = 0;
                        $poRemaining = 0;

                        if ($po->request_type === 'project') {
                            // Try to get project from PO's project_id
                            if ($po->project_id && $po->project && $po->project->id) {
                                $resolvedProject = $po->project;
                            }
                            // Fallback: Try to get from quotation's project
                            elseif ($po->quotation && optional($po->quotation)->project) {
                                $resolvedProject = $po->quotation->project;
                            }

                            // If we found a project, calculate from it
                            if ($resolvedProject) {
                                $poVendorPoPaid = 0;
                                foreach ($resolvedProject->lpoouts->where('is_latest_revision', true) as $lp) {
                                    foreach ($lp->paymentInvoices as $pi) {
                                        if ($pi->transaction && $pi->transaction->status == 1) {
                                            $poVendorPoPaid += $pi->total_amount;
                                        }
                                    }
                                }
                                $poContractValue = optional($resolvedProject->quotation)->amount ?? 0;
                                $poSpent = $poVendorPoPaid + $resolvedProject->labour_charges + $resolvedProject->petty_cash->sum('total_amount');
                                $poRemaining = $poContractValue - $poSpent;
                                $showProjectStats = true;
                            }
                            // If no project but quotation exists, calculate from quotation only
                            elseif ($po->quotation) {
                                $poContractValue = $po->quotation->amount ?? 0;
                                $poSpent = 0; // Without project data, we can't calculate spent amount
                                $poRemaining = $poContractValue;
                                $showProjectStats = true;
                            }
                        }
                    @endphp
                    @if($showProjectStats)
                        <div class="row mb-3">
                            <div class="col-lg-4 col-6">
                                <div class="small-box bg-info">
                                    <div class="inner">
                                        <h4>{{ number_format($poContractValue, 2) }}</h4>
                                        <p>Project Contract Value (AED)</p>
                                    </div>
                                    <div class="icon"><i class="fas fa-file-contract"></i></div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-6">
                                <div class="small-box bg-warning">
                                    <div class="inner">
                                        <h4>{{ number_format($poSpent, 2) }}</h4>
                                        <p>Spent So Far (AED)</p>
                                    </div>
                                    <div class="icon"><i class="fas fa-money-bill-wave"></i></div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-6">
                                <div class="small-box {{ $poRemaining < 0 ? 'bg-danger' : 'bg-success' }}">
                                    <div class="inner">
                                        <h4>{{ number_format($poRemaining, 2) }}</h4>
                                        <p>Remaining (AED)</p>
                                    </div>
                                    <div class="icon"><i class="fas fa-balance-scale"></i></div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <hr>

                    {{-- Vendor Information Section --}}
                    @if($po->vendor)
                        <h5 class="mb-3"><i class="fas fa-building"></i> Vendor Information</h5>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="bg-light p-3 rounded">
                                    <table class="table table-borderless table-sm mb-0">
                                        <tr>
                                            <td style="width: 35%;"><strong>Vendor Name:</strong></td>
                                            <td>{{ $po->vendor->name ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Email:</strong></td>
                                            <td>
                                                @if($po->vendor->email)
                                                    <a href="mailto:{{ $po->vendor->email }}">{{ $po->vendor->email }}</a>
                                                @else
                                                    <span class="text-danger"><i class="fas fa-exclamation-circle"></i> Not configured</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @php $additionalVendorEmails = $po->vendor->additionalEmails(); @endphp
                                        <tr>
                                            <td><strong>Additional Emails:</strong></td>
                                            <td>
                                                @forelse($additionalVendorEmails as $additionalVendorEmail)
                                                    <a href="mailto:{{ $additionalVendorEmail }}">{{ $additionalVendorEmail }}</a>@if(!$loop->last)<br>@endif
                                                @empty
                                                    <span class="text-muted">-</span>
                                                @endforelse
                                            </td>
                                        </tr>

                                    </table>
                                </div>
                            </div>

                        </div>
                        <hr>
                    @endif

                    @if($po->lpout_name)
                        <h5>LPOUT Information (to be created)</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <tr>
                                    <td><strong>LPOUT Name:</strong></td>
                                    <td>{{ $po->lpout_name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Vendor:</strong></td>
                                    <td>
                                        @if($po->lpout_vendor_id && $po->vendor)
                                            {{ $po->vendor->name }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>TRN (Company):</strong></td>
                                    <td>{{ config('purchase-orders.company_trn') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Kindly Attn:</strong></td>
                                    <td>{{ $po->lpout_kindly_attn ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Date:</strong></td>
                                    <td>{{ $po->lpout_date->format('Y-m-d') ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Payment Type:</strong></td>
                                    <td>{{ $po->lpout_payment_type ?? '-' }}</td>
                                </tr>
                                @if($po->lpout_payment_type === 'cheque')
                                    <tr>
                                        <td><strong>Cheque Date:</strong></td>
                                        <td>{{ $po->lpout_cheque_date ?? '-' }}</td>
                                    </tr>
                                @endif
                                <tr>
                                    <td><strong>VAT:</strong></td>
                                    <td>{{ $po->lpout_vat ?? 0 }}%</td>
                                </tr>
                                <tr>
                                    <td><strong>Payment Preference:</strong></td>
                                    <td>{{ $po->lpout_payment_preference_option ?? '-' }}</td>
                                </tr>
                                @if($po->lpout_payment_preference_option === 'custom')
                                    <tr>
                                        <td><strong>Payment Preference Details:</strong></td>
                                        <td>{{ $po->lpout_payment_preference ?? '-' }}</td>
                                    </tr>
                                @endif
                                @if($po->lpout_pdc_payment_option)
                                    <tr>
                                        <td><strong>PDC Payment Option:</strong></td>
                                        <td>{{ $po->lpout_pdc_payment_option ?? '-' }} ({{ $po->lpout_pdc_number_of_days ?? 0 }} days)</td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                        <hr class="my-4">
                    @endif

                    <!-- Items Table (Always show if data exists) -->
                    @php
                        // Lump-sum: single batch total entered manually (no per-row costs)
                        $isLump = $po->isLumpSum();
                        $showItemCode = $po->hasItemCode();
                        $hasLpoutItems = $po->lpout_items && is_array($po->lpout_items) && count($po->lpout_items) > 0;
                    @endphp
                    @if($isLump || $hasLpoutItems)
                        @php
                            $subtotal = $isLump
                                ? floatval($po->lpout_manual_total ?? 0)
                                : collect($po->lpout_items)->sum('total');
                        @endphp
                        @if($hasLpoutItems)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-sm">
                                <thead class="bg-light">
                                    <tr>
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
                                    @foreach($po->lpout_items as $idx => $item)
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
                        @elseif($isLump)
                        <p class="text-muted"><small>Lump-sum request — no individual item rows were entered.</small></p>
                        @endif

                        <!-- Totals Section -->
                        <div class="row mt-3 mb-4">
                            <div class="col-sm-8 offset-sm-4">
                                <table class="table table-borderless">
                                    <tr>
                                        <th style="width:70%">TOTAL AMOUNT (IN AED)</th>
                                        <td><strong>AED {{ number_format($subtotal, 2) }}</strong></td>
                                    </tr>
                                    @if($po->lpout_vat)
                                        @php
                                            $vat = ($subtotal * 5) / 100;
                                            $grandTotal = $subtotal + $vat;
                                        @endphp
                                        <tr>
                                            <th>VAT (5%) (IN AED)</th>
                                            <td><strong>AED {{ number_format($vat, 2) }}</strong></td>
                                        </tr>
                                        <tr>
                                            <th>TOTAL SUM VALUE INCLUDING VAT (5%) (IN AED)</th>
                                            <td><strong>AED {{ number_format($grandTotal, 2) }}</strong></td>
                                        </tr>
                                    @endif
                                </table>
                            </div>
                        </div>

                        <hr class="my-4">
                    @elseif($po->items && is_array($po->items) && count($po->items) > 0)
                        <h6 class="mt-4 mb-3"><i class="fa fa-list"></i> Items (From Initial Request)</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-sm">
                                <thead class="bg-light">
                                    <tr>
                                        <th>NO</th>
                                        @if($po->hasItemCode())
                                            <th>ITEM CODE</th>
                                        @endif
                                        <th>DESCRIPTION</th>
                                        <th>QUANTITY</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($po->items as $idx => $item)
                                        <tr>
                                            <td>{{ $idx + 1 }}</td>
                                            @if($po->hasItemCode())
                                                <td>{{ $item['item_code'] ?? '-' }}</td>
                                            @endif
                                            <td>{{ $item['material_name'] ?? $item['description'] ?? '-' }}</td>
                                            <td>{{ $item['quantity'] ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <p class="text-muted"><small>Note: Department hasn't filled in pricing yet. Items waiting for department review.</small></p>
                        <hr class="my-4">
                    @endif

                    <!-- Terms & Conditions -->
                    @if($po->lpout_terms)
                        <h6 class="mt-4 mb-3"><i class="fa fa-file-text"></i> Terms & Conditions</h6>
                        <div class="bg-light p-3 rounded mb-4">
                            {!! $po->lpout_terms !!}
                        </div>
                        <hr class="my-4">
                    @endif
                    <h5><i class="fa fa-paperclip"></i> Attached Files</h5>
                    @php $files = $po->getMedia(); @endphp
                    @if($files->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead class="bg-light">
                                    <tr>
                                        <th>File Name</th>
                                        <th>Type</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($files as $file)
                                        <tr>
                                            <td><i class="fa fa-file"></i> {{ $file->name }}</td>
                                            <td><small class="text-muted">{{ strtoupper($file->mime_type) }}</small></td>
                                            <td>
                                                <a href="{{ url('storage/' . $file->id . '/' . $file->file_name) }}" target="_blank" class="btn btn-sm btn-outline-primary" title="View file">
                                                    <i class="fa fa-eye"></i> View
                                                </a>
                                                <a href="{{ url('storage/' . $file->id . '/' . $file->file_name) }}" download class="btn btn-sm btn-outline-primary" title="Download file">
                                                    <i class="fa fa-download"></i> Download
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted"><small>No files attached to this Purchase Order.</small></p>
                    @endif

                    <hr>

                    {{-- Vendor Invoice (PaymentInvoice) Section — visible once LPO is generated --}}
                    @if($po->lpout_id && $po->lpout)
                    <h5><i class="fa fa-file-invoice"></i> Vendor Invoices
                        <small class="text-muted">against LPO {{ $po->lpout->lpo_invoice_no }}</small>
                        <a href="{{ route('invoiceRequests.create_with_lpoout', $po->lpout_id) }}"
                           class="btn btn-sm btn-danger btn-flat float-right">
                            <i class="fa fa-plus"></i> Request Invoice
                        </a>
                    </h5>
                    <div class="table-responsive mt-2 mb-4">
                        <table class="table table-bordered table-striped table-sm">
                            <thead class="bg-light">
                                <tr>
                                    <th>Invoice No</th>
                                    <th>Amount (AED)</th>
                                    <th>VAT (AED)</th>
                                    <th>Total (AED)</th>
                                    <th>Status</th>
                                    <th>Payment</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($po->lpout->paymentInvoices as $pi)
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
                                        <a href="{{ route('invoiceRequests.create_with_lpoout', $po->lpout_id) }}">Request one.</a>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                            @if($po->lpout->paymentInvoices->count())
                            <tfoot>
                                <tr class="font-weight-bold">
                                    <td>Totals</td>
                                    <td>{{ number_format($po->lpout->paymentInvoices->sum('amount'), 2) }}</td>
                                    <td>{{ number_format($po->lpout->paymentInvoices->sum('vat'), 2) }}</td>
                                    <td>{{ number_format($po->lpout->paymentInvoices->sum('total_amount'), 2) }}</td>
                                    <td colspan="3">
                                        {{ $po->lpout->paymentInvoices->where('status', 1)->count() }} paid /
                                        {{ $po->lpout->paymentInvoices->where('status', 0)->count() }} unpaid
                                    </td>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
                    <hr>
                    @endif

                    <h5>Make Final Decision</h5>

                    @if($po->status === 'Pending Admin Approval' || $po->status === 'Hold')
                    <div class="row">
                        <div class="col-md-4">
                            <div class="bg-light p-3 rounded">
                                <h6>Approve and Send Email</h6>
                                <button type="button" class="btn btn-success btn-flat" data-toggle="modal" data-target="#modal-vendor-email">Approve</button>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="bg-light p-3 rounded">
                                {!! Form::open(['route' => ['purchase-orders.adminReject', $po->id], 'method' => 'POST']) !!}
                                <h6>Reject</h6>
                                <div class="form-group">
                                    {!! Form::label('notes', 'Rejection Reason:') !!}
                                    {!! Form::textarea('notes', null, ['class' => 'form-control', 'rows' => 3, 'required' => true]) !!}
                                </div>
                                {!! Form::submit('Reject', ['class' => 'btn btn-danger btn-flat']) !!}
                                {!! Form::close() !!}
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="bg-light p-3 rounded">
                                {!! Form::open(['route' => ['purchase-orders.adminHold', $po->id], 'method' => 'POST']) !!}
                                <h6>Hold</h6>
                                <div class="form-group">
                                    {!! Form::label('notes', 'Hold Reason (Optional):') !!}
                                    {!! Form::textarea('notes', null, ['class' => 'form-control', 'rows' => 3]) !!}
                                </div>
                                {!! Form::submit('Put On Hold', ['class' => 'btn btn-warning btn-flat']) !!}
                                {!! Form::close() !!}
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="alert alert-info">
                        <strong>Decision Already Made:</strong> This Purchase Order has been <strong>{{ $po->status }}</strong>.
                        @if($po->status === 'Admin Approved')
                            LPOUT has been created.
                        @endif
                        @if($po->admin_notes)
                            <br><strong>Notes:</strong> {{ $po->admin_notes }}
                        @endif
                    </div>
                    @endif

                    <div class="mt-3">
                        <a href="{!! route('purchase-orders.adminIndex') !!}" class="btn btn-secondary btn-flat">Back</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Vendor Email Confirmation Modal --}}
<div class="modal fade" id="modal-vendor-email">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Send LPOUT Email to Vendor</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="vendor-email-form">
                <div class="modal-body">

                    <div class="form-group">
                        <label for="approval-notes-modal"><strong>Approval Notes</strong></label>
                        <textarea id="approval-notes-modal" class="form-control" rows="3"></textarea>
                    </div>
                    @php $vendorEmails = $po->vendor ? $po->vendor->allEmails() : []; @endphp
                    @if(!empty($vendorEmails))
                        <div class="form-group">
                            <label><strong>To (Vendor Emails):</strong></label>
                            @foreach($vendorEmails as $index => $vendorEmail)
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input vendor-to-checkbox"
                                           id="to-vendor-{{ $index }}"
                                           value="{{ $vendorEmail }}"
                                           checked>
                                    <label class="custom-control-label" for="to-vendor-{{ $index }}">
                                        {{ $vendorEmail }}
                                        @if($index === 0)
                                            <span class="badge badge-secondary">Primary</span>
                                        @endif
                                    </label>
                                </div>
                            @endforeach
                            <small class="form-text text-muted">Every address on the vendor's profile is included by default. Untick any that should not receive this LPO.</small>
                        </div>
                    @else
                        <div class="alert alert-warning" role="alert">
                            <i class="fas fa-exclamation-triangle"></i> <strong>No Vendor Email Configured</strong>
                            <p class="mb-0">The selected vendor does not have an email address configured in their profile. Please update the vendor profile with an email address before approving this purchase order.</p>
                        </div>
                    @endif

                    <hr>

                    @php
                        $defaultCCEmails = config('purchase-orders.admin_approval_notification_emails', []);
                        if (!is_array($defaultCCEmails)) {
                            $defaultCCEmails = [$defaultCCEmails];
                        }
                        $defaultCCEmails = array_filter($defaultCCEmails);
                    @endphp

                    <div class="form-group">
                        <label><strong>Default CC Recipients:</strong></label>
                        @if(!empty($defaultCCEmails))
                            @foreach($defaultCCEmails as $index => $email)
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input default-cc-checkbox" 
                                           id="cc-default-{{ $index }}" 
                                           value="{{ $email }}"
                                           checked>
                                    <label class="custom-control-label" for="cc-default-{{ $index }}">
                                        {{ $email }}
                                    </label>
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted"><small>No default CC recipients configured.</small></p>
                        @endif
                    </div>

                    <hr>

                    <div class="form-group">
                        <label for="additional-cc"><strong>Additional CC Emails</strong></label>
                        <textarea id="additional-cc" class="form-control" rows="3" ></textarea>
                        <small class="form-text text-muted">Enter email addresses separated by commas</small>
                    </div>


                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    @if(!empty($vendorEmails))
                        <button type="button" class="btn btn-success" onclick="submitVendorEmailForm({{ $po->id }})">Send & Approve</button>
                    @else
                        <button type="button" class="btn btn-success" disabled title="Vendor email is required">Send & Approve</button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function submitVendorEmailForm(poId) {
    // Collect the ticked vendor "To" addresses
    const toEmails = [];
    document.querySelectorAll('.vendor-to-checkbox:checked').forEach(checkbox => {
        const email = checkbox.value.trim();
        if (email && toEmails.indexOf(email) === -1) {
            toEmails.push(email);
        }
    });

    if (toEmails.length === 0) {
        alert('Select at least one vendor email to send this LPO to.');
        return;
    }

    // Collect selected default CC emails
    const selectedCC = [];
    document.querySelectorAll('.default-cc-checkbox:checked').forEach(checkbox => {
        const email = checkbox.value.trim();
        if (email && selectedCC.indexOf(email) === -1) {
            selectedCC.push(email);
        }
    });

    // Collect additional CC emails
    const additionalCCText = document.getElementById('additional-cc').value.trim();
    const additionalCC = [];
    if (additionalCCText) {
        additionalCCText.split(',').forEach(email => {
            email = email.trim();
            if (email && additionalCC.indexOf(email) === -1) {
                additionalCC.push(email);
            }
        });
    }

    // Combine all CC emails (remove duplicates)
    const allCCEmails = [];
    selectedCC.forEach(email => allCCEmails.push(email));
    additionalCC.forEach(email => {
        if (allCCEmails.indexOf(email) === -1) {
            allCCEmails.push(email);
        }
    });

    // Validate emails (basic validation)
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    for (let email of allCCEmails.concat(toEmails)) {
        if (!emailRegex.test(email)) {
            alert('Invalid email format: ' + email);
            return;
        }
    }

    // Get approval notes
    const notes = document.getElementById('approval-notes-modal').value.trim();

    // Get CSRF token from meta tag or hidden input
    let csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    if (!csrfToken) {
        csrfToken = document.querySelector('input[name="_token"]')?.value;
    }

    // Log for debugging
    console.log('Submitting vendor email approval:', {
        poId: poId,
        toEmails: toEmails,
        ccEmails: allCCEmails,
        notesLength: notes.length,
        hasCsrfToken: !!csrfToken
    });

    // Close modal using vanilla JS (jQuery might not be loaded)
    const modal = document.getElementById('modal-vendor-email');
    if (modal) {
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            try {
                new bootstrap.Modal(modal).hide();
            } catch(e) {
                console.log('Bootstrap modal hide failed:', e);
            }
        } else if (typeof jQuery !== 'undefined' && typeof $ !== 'undefined') {
            try {
                $('#modal-vendor-email').modal('hide');
            } catch(e) {
                console.log('jQuery modal not available');
            }
        } else {
            // Fallback: hide by removing show class
            modal.classList.remove('show');
            modal.style.display = 'none';
        }
    }

    // Use fetch with JSON for better data handling
    fetch('/purchase-orders-admin/' + poId + '/approve-with-email', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken || ''
        },
        body: JSON.stringify({
            notes: notes,
            to_emails: toEmails,
            cc_emails: allCCEmails
        })
    })
    .then(response => {
        console.log('Response status:', response.status);
        
        // Check if response is successful (2xx or 3xx)
        if (response.status >= 200 && response.status < 400) {
            // Try to parse as JSON first
            return response.json().then(data => {
                console.log('JSON response:', data);
                if (data.redirect) {
                    window.location.href = data.redirect;
                } else {
                    window.location.href = '/purchase-orders-admin/index';
                }
                return null;
            }).catch(() => {
                // If not JSON, just redirect
                console.log('Non-JSON response, redirecting...');
                window.location.href = '/purchase-orders-admin/index';
                return null;
            });
        } else {
            // Error response
            throw new Error('HTTP ' + response.status);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while submitting the approval. Check browser console for details.\n\nError: ' + error.message);
        
        // Try to reopen modal on error
        const modal = document.getElementById('modal-vendor-email');
        if (modal && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            new bootstrap.Modal(modal).show();
        } else if (typeof jQuery !== 'undefined' && typeof $ !== 'undefined') {
            try {
                $('#modal-vendor-email').modal('show');
            } catch(e) {
                console.log('Could not reopen modal');
            }
        }
    });
}

// Clear modal form on close
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('modal-vendor-email');
    if (modal) {
        modal.addEventListener('hidden.bs.modal', function() {
            document.getElementById('vendor-email-form').reset();
            document.getElementById('additional-cc').value = '';
            document.getElementById('approval-notes-modal').value = '';
            // Re-check all default CC checkboxes
            document.querySelectorAll('.default-cc-checkbox').forEach(checkbox => {
                checkbox.checked = true;
            });
        });
    }
});
</script>
@endsection
