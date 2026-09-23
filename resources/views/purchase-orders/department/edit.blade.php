@extends('layouts.master')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark text-weight-bold">Review Purchase Order</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{!! route('purchase-orders.departmentIndex') !!}">Department Review</a></li>
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
                    <h3 class="card-title">{{ $po->request_number }} - Add Cost Information</h3>
                </div>
                
                <div class="card-body">
                    @if($po->sent_back_count)
                        <div class="alert alert-warning">
                            <i class="fa fa-undo"></i>
                            This request was sent back to the requester
                            <strong>{{ $po->sent_back_count }}</strong> time{{ $po->sent_back_count > 1 ? 's' : '' }}.
                            @if($po->sent_back_notes)
                                <br><strong>Last reason:</strong> {{ $po->sent_back_notes }}
                            @endif
                            @if($po->isSentBack())
                                <br>It is currently <strong>with the requester</strong> and has not been resubmitted yet.
                            @endif
                        </div>
                    @endif
                    <div class="row mb-3">
                        <div class="col-md-2">
                            <strong>Request Number:</strong><br>{{ $po->request_number ?? '-' }}
                        </div>
                        <div class="col-md-2">
                            <strong>Request Type:</strong><br>{{ ucfirst($po->request_type) }}
                        </div>
                        @if($po->project_id && $po->project && $po->project->id)
                            <div class="col-md-2">
                                <strong>Project:</strong><br>{{ $po->project->subject ?? '-' }}
                            </div>
                        @else
                            <div class="col-md-2">
                                <strong>Quotation:</strong><br>{{ $po->quotation->name ?? '-' }}
                            </div>
                        @endif

                        <div class="col-md-2">
                            <strong>Company:</strong><br>{{ $po->getCompanyName() }}
                        </div>
                        <div class="col-md-2">
                            <strong>Vendor:</strong><br>
                            @if($po->lpout_vendor_id && $po->vendor && $po->vendor->id)
                                {{ $po->vendor->name }}
                            @else
                                -
                            @endif
                        </div>
                        <div class="col-md-2">
                            <strong>TRN No (Company):</strong><br>{{ config('purchase-orders.company_trn') }}
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-2">
                            <strong>Date:</strong><br>{{ $po->date->format('Y-m-d') ?? '-' }}
                        </div>
                        <div class="col-md-2">
                            <strong>Due Date:</strong><br>{{ $po->due_date ? $po->due_date->format('Y-m-d') : '-' }}
                        </div>
                        <div class="col-md-2">
                            <strong>Delivery Date:</strong><br>{{ $po->delivery_date ? $po->delivery_date->format('Y-m-d') : '-' }}
                        </div>
                        <div class="col-md-2">
                            <strong>Kindly Attn:</strong><br>{{ $po->lpout_kindly_attn ?? '-' }}
                        </div>
                        <div class="col-md-3">
                            <strong>Status:</strong><br>
                            <span class="badge badge-warning">{{ $po->department_status }}</span>
                        </div>
                    </div>

                    <hr>

                    <!-- Attached Files Section -->
                    <div class="mb-4">
                        <h5><i class="fa fa-paperclip"></i> Attached Files</h5>
                        @php $files = $po->getMedia(); @endphp
                        @if($files->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>File Name</th>
                                            <th>Type</th>
                                            <th>Size</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($files as $file)
                                            <tr>
                                                <td><i class="fa fa-file"></i> {{ $file->name }}</td>
                                                <td><small class="text-muted">{{ strtoupper($file->mime_type) }}</small></td>
                                                <td><small class="text-muted">{{ number_format($file->size / 1024, 2) }} KB</small></td>
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
                    </div>

                    <hr>

                    <!-- Other Information Section -->
                    @if($po->other_info)
                    <div class="mb-4">
                        <h5><i class="fa fa-info-circle"></i> Other Information</h5>
                        <div class="bg-light p-3 rounded">
                            {{ $po->other_info }}
                        </div>
                    </div>

                    <hr>
                    @endif

                    <!-- LPOUT INFORMATION (READ-ONLY - From Step 1) -->
                    @if($po->lpout_name)
                    <div class="mb-4">
                        <h4 class="text-danger mb-3">LPOUT Information (From Step 1)</h4>
                        
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
                                    <td>{{ $po->lpout_date ? $po->lpout_date->format('Y-m-d') : '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Payment Type:</strong></td>
                                    <td>{{ $po->lpout_payment_type ?? '-' }}</td>
                                </tr>
                                @if($po->lpout_payment_type === 'Cheque')
                                <tr>
                                    <td><strong>Cheque Date:</strong></td>
                                    <td>{{ $po->lpout_cheque_date ? $po->lpout_cheque_date->format('Y-m-d') : '-' }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td><strong>Payment Preference Option:</strong></td>
                                    <td>{{ $po->lpout_payment_preference_option ?? '-' }}</td>
                                </tr>
                                @if($po->payment_preference)
                                <tr>
                                    <td><strong>Custom Payment Preference:</strong></td>
                                    <td>{!! nl2br($po->payment_preference) !!}</td>
                                </tr>
                                @endif
                            </table>
                        </div>

                        <hr class="my-4">
                    </div>
                    @endif
                    
                    {!! Form::open(['route' => ['purchase-orders.departmentUpdate', $po->id], 'method' => 'POST', 'enctype' => 'multipart/form-data']) !!}

                    <!-- Attachment Management -->
                    <!-- <div class="mb-4">
                        <h5><i class="fa fa-upload"></i> Manage Attachments for LPO Merge</h5>
                        <p class="text-muted"><small>The PDF attachments shown above will be merged with the final LPO document. You may optionally upload additional files or replacements below.</small></p>
                        <div class="form-group">
                            {!! Form::label('files[]', 'Upload Additional Attachments (PDF only):') !!}
                            <div class="custom-file">
                                {!! Form::file('files[]', ['class' => 'custom-file-input', 'id' => 'dept_files', 'multiple' => true, 'accept' => '.pdf']) !!}
                                <label class="custom-file-label" for="dept_files">Choose file(s)...</label>
                            </div>
                            <small class="form-text text-muted">Only PDF files are accepted for LPO merge. Multiple files allowed.</small>
                        </div>
                    </div>

                    <hr> -->

                    <!-- VAT Selection (Department decides) -->
                    <div class="row mb-4">
                        <div class="col-md-3 col-sm-6">
                            <div class="form-group pt-2">
                                {!! Form::label('vat', 'Apply VAT (5%):') !!}
                                <label class="checkbox-inline">
                                    {!! Form::hidden('vat', 0) !!}
                                    {!! Form::checkbox('vat', 1, $po->lpout_vat ? true : false) !!}
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Pricing Mode Selection -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="d-block font-weight-bold">Pricing Mode:</label>
                                <label class="radio-inline mr-3">
                                    {!! Form::radio('pricing_mode', 'unit', ($po->lpout_pricing_mode ?? 'unit') !== 'lump', ['id' => 'pricing_unit', 'class' => 'pricing_mode_radio']) !!}
                                    <span class="ml-1">Per-Unit (cost per item)</span>
                                </label>
                                <label class="radio-inline">
                                    {!! Form::radio('pricing_mode', 'lump', ($po->lpout_pricing_mode ?? 'unit') === 'lump', ['id' => 'pricing_lump', 'class' => 'pricing_mode_radio']) !!}
                                    <span class="ml-1">Lump-Sum (single batch total)</span>
                                </label>
                                <small class="form-text text-muted">Choose <strong>Lump-Sum</strong> when there is only one total price for the whole order (no per-unit cost). The cost columns are hidden and you enter the Total Amount directly below; VAT still applies to that total.</small>
                            </div>
                        </div>
                    </div>

                    <!-- Item Code Toggle - works with either pricing mode -->
                    @php $hasItemCode = (bool) old('has_item_code', $po->has_item_code); @endphp
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="checkbox-inline font-weight-bold">
                                    {!! Form::hidden('has_item_code', 0) !!}
                                    {!! Form::checkbox('has_item_code', 1, $hasItemCode, ['id' => 'has_item_code']) !!}
                                    <span class="ml-1">Capture Item Code</span>
                                </label>
                                <small class="form-text text-muted">Adds an <strong>ITEM CODE</strong> column before the description. It carries through to admin approval, the LPO print and the vendor email.</small>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 1: REQUEST COST INFORMATION (Items Table) -->
                    <div class="mb-4">
                        <h4 class="text-danger mb-3">Items for Local Purchase Outbound</h4>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="itemsTable">
                                <thead class="bg-light">
                                    <tr>
                                        <th>NO</th>
                                        <th data-col="item_code">ITEM CODE</th>
                                        <th>MATERIAL NAME (DESCRIPTION)</th>
                                        <th>UNIT</th>
                                        <th>QUANTITY</th>
                                        <th data-col="cost">COST PER UNIT (AED)</th>
                                        <th data-col="total">TOTAL COST (AED)</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        // Priority: old() after validation failure → saved lpout_items → original step-1 items
                                        if (old('lpout_items')) {
                                            $itemsToDisplay = old('lpout_items');
                                        } elseif ($po->lpout_items && is_array($po->lpout_items) && count($po->lpout_items) > 0) {
                                            $itemsToDisplay = is_string($po->lpout_items) ? json_decode($po->lpout_items, true) : $po->lpout_items;
                                        } else {
                                            $originalItems = is_string($po->items) ? json_decode($po->items, true) : $po->items;
                                            $itemsToDisplay = $originalItems;
                                        }
                                    @endphp
                                    @if($itemsToDisplay && is_array($itemsToDisplay) && count($itemsToDisplay) > 0)
                                        @foreach($itemsToDisplay as $idx => $item)
                                            <tr>
                                                <td class="row-number">{{ $idx + 1 }}</td>
                                                <td data-col="item_code">
                                                    <input type="text" data-field="item_code" name="lpout_items[{{ $idx }}][item_code]" class="form-control" value="{{ $item['item_code'] ?? '' }}" />
                                                </td>
                                                <td>
                                                    <input type="text" data-field="description" name="lpout_items[{{ $idx }}][description]" class="form-control" value="{{ $item['description'] ?? $item['material_name'] ?? '' }}" />
                                                </td>
                                                <td>
                                                    <input type="text" data-field="unit" name="lpout_items[{{ $idx }}][unit]" class="form-control" value="{{ $item['unit'] ?? '' }}" />
                                                </td>
                                                <td>
                                                    <input type="number" min="0" step="any" data-field="qty" name="lpout_items[{{ $idx }}][qty]" class="form-control qty" value="{{ $item['qty'] ?? $item['quantity'] ?? '' }}" />
                                                </td>
                                                <td data-col="cost">
                                                    <input type="number" min="0" step="0.01" data-field="unit_price" name="lpout_items[{{ $idx }}][unit_price]" class="form-control unit_price" value="{{ $item['unit_price'] ?? $item['cost'] ?? '' }}" />
                                                </td>
                                                <td data-col="total">
                                                    <input type="number" min="0" step="0.01" data-field="total" name="lpout_items[{{ $idx }}][total]" class="form-control total" value="{{ $item['total'] ?? '' }}" readonly />
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-danger btn-sm removeRow">X</button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="8" class="text-center">No items</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        <button type="button" class="btn btn-outline-primary" id="addRow">+ Add Row</button>
                    </div>

                    <hr class="my-4">

                    <!-- Totals Section -->
                    <div class="row mt-3">
                        <div class="col-sm-12">
                            <table class="table table-borderless">
                                <tr>
                                    <th style="width:70%">TOTAL AMOUNT (IN AED)</th>
                                    <td>
                                        <input type="number" min="0" step="0.01" class="form-control" id="total_amount_display" name="manual_total" value="{{ old('manual_total', ($po->lpout_pricing_mode ?? 'unit') === 'lump' ? $po->lpout_manual_total : '') }}" readonly>
                                        <small class="form-text text-muted lump-hint" style="display:none;">Enter the total price for the whole order.</small>
                                    </td>
                                </tr>
                                <tbody id="vatSection" style="display:none;">
                                    <tr>
                                        <th>VAT (5%) (IN AED)</th>
                                        <td><input type="text" class="form-control" id="vat_amount_display" readonly></td>
                                    </tr>
                                    <tr>
                                        <th>TOTAL SUM VALUE INCLUDING VAT (5%) (IN AED)</th>
                                        <td><input type="text" class="form-control" id="grand_total_display" readonly></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Terms & Conditions -->
                    <div class="row mt-3">
                        <div class="col-md-12 col-sm-12">
                            <div class="form-group">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label for="terms">Terms & Conditions:</label>
                                    @if($po->lpout_vendor_id && $po->vendor && $po->vendor->id)
                                        <button type="button" id="load_po_vendor_terms_btn" class="btn btn-sm btn-outline-info">
                                            <i class="fa fa-refresh"></i> Load Vendor Terms
                                        </button>
                                    @endif
                                </div>
                                {!! Form::textarea('terms', $po->lpout_terms ?? '
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
                                ', [
                                    'class' => 'form-control summernote',
                                    'rows' => 6
                                ]) !!}
                            </div>
                        </div>
                    </div>

                    <!-- Forward Priority Selection -->
                    @if($po->department_status !== 'Approved' || !$po->status || $po->status !== 'Pending Admin Approval')
                    <div class="row mt-3 mb-3">
                        <div class="col-md-12">
                            <div class="form-group">
                                {!! Form::label('urgency_level', 'Forward Priority:') !!}
                                <div class="mt-2">
                                    <label class="radio-inline mr-3">
                                        {!! Form::radio('urgency_level', 'normal', true, ['id' => 'urgency_normal']) !!}
                                        <span class="ml-1">Normal</span>
                                    </label>
                                    <label class="radio-inline">
                                        {!! Form::radio('urgency_level', 'urgent', false, ['id' => 'urgency_urgent']) !!}
                                        <span class="ml-1">🚨 Urgent</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="form-group">
                        @if($po->department_status === 'Approved' && $po->status === 'Pending Admin Approval')
                            <button type="button" class="btn btn-secondary btn-flat" disabled>
                                ✓ Already Forwarded to Admin
                            </button>
                        @else
                            <button type="submit" name="action" value="save" class="btn btn-primary btn-flat">Save Changes</button>
                            <button type="submit" name="action" value="forward" class="btn btn-success btn-flat">Forward to Admin</button>
                        @endif
                        <a href="{!! route('purchase-orders.departmentIndex') !!}" class="btn btn-secondary btn-flat">Cancel</a>
                    </div>

                    {!! Form::close() !!}

                    <!-- Step 2 -> Step 1: return the request to the requester for correction -->
                    @if(!($po->department_status === 'Approved' && $po->status === 'Pending Admin Approval'))
                        <hr class="my-4">
                        <div class="bg-light p-3 rounded">
                            <h5><i class="fa fa-undo"></i> Send Back to Requester</h5>
                            <p class="text-muted mb-2">
                                <small>Use this when the request needs correcting at Step 1. The requester keeps the same
                                request number, fixes it, and submits it back to this review queue. Any cost information you
                                entered above must be saved first &mdash; sending back does not save it.</small>
                            </p>
                            {!! Form::open(['route' => ['purchase-orders.departmentSendBack', $po->id], 'method' => 'POST', 'id' => 'send-back-form']) !!}
                                <div class="form-group">
                                    {!! Form::label('send_back_notes', 'Reason (shared with the requester):') !!}
                                    {!! Form::textarea('notes', null, ['class' => 'form-control', 'id' => 'send_back_notes', 'rows' => 3, 'required' => true, 'placeholder' => 'Explain what needs to be corrected']) !!}
                                </div>
                                {!! Form::button('<i class="fa fa-undo"></i> Send Back to Requester', [
                                    'type' => 'submit',
                                    'class' => 'btn btn-warning btn-flat',
                                    'onclick' => "return confirm('Send this request back to the requester for correction?')"
                                ]) !!}
                            {!! Form::close() !!}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('css')
@parent
    <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css')}}">
    <link rel="stylesheet" href="{{ asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{ asset('plugins/daterangepicker/daterangepicker.css')}}">
@endsection

@section('scripts')
<script src="{{ asset('plugins/select2/js/select2.full.min.js')}}"></script>
<script src="{{ asset('plugins/bs-custom-file-input/bs-custom-file-input.min.js')}}"></script>
<script src="{{ asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
<script src="{{ asset('plugins/summernote/summernote-bs4.min.js') }}"></script>

<script>
    $(document).ready(function() {
        bsCustomFileInput.init();

        // Update file input label with selected filenames
        $('#dept_files').on('change', function() {
            var fileNames = [];
            for (var i = 0; i < this.files.length; i++) {
                fileNames.push(this.files[i].name);
            }
            $(this).next('.custom-file-label').text(fileNames.length > 0 ? fileNames.join(', ') : 'Choose file(s)...');
        });


        // --- Build a new row for items ---
        function buildRowHtml() {
            return `
                <tr>
                    <td class="row-number"></td>
                    <td data-col="item_code"><input type="text" data-field="item_code" class="form-control" /></td>
                    <td><input type="text" data-field="description" class="form-control" /></td>
                    <td><input type="text" data-field="unit" class="form-control" /></td>
                    <td><input type="number" min="0" step="any" data-field="qty" class="form-control qty" /></td>
                    <td data-col="cost"><input type="number" min="0" step="any" data-field="unit_price" class="form-control unit_price" /></td>
                    <td data-col="total"><input type="number" min="0" step="any" data-field="total" class="form-control total" readonly /></td>
                    <td><button type="button" class="btn btn-danger btn-sm removeRow">X</button></td>
                </tr>
            `;
        }

        // --- Update row numbering and field names ---
        function updateRowIndexes() {
            $('#itemsTable tbody tr').each(function(i) {
                const idx = i;
                // Update row number
                $(this).find('.row-number').text(idx + 1);
                
                // Update field names for all rows
                $(this).find('input').each(function() {
                    const field = $(this).data('field');
                    if (field) {
                        const currentName = $(this).attr('name');
                        // Check if this row was dynamically added (new row)
                        if (!currentName || currentName.indexOf('lpout_items') === -1) {
                            // This is a new row, set proper naming
                            $(this).attr('name', `lpout_items[${idx}][${field}]`);
                        } else if (currentName.includes('lpout_items[') && !currentName.includes(`lpout_items[${idx}]`)) {
                            // Update index for newly added rows that were inserted
                            const newName = currentName.replace(/lpout_items\[\d+\]/, `lpout_items[${idx}]`);
                            $(this).attr('name', newName);
                        }
                    }
                });
            });
        }

        // --- Add Row ---
        $('#addRow').click(function() {
            $('#itemsTable tbody').append(buildRowHtml());
            updateRowIndexes();
            // Ensure the new row's optional columns respect the current toggles
            applyPricingMode();
            applyItemCode();
        });

        // --- Remove Row ---
        $(document).on('click', '.removeRow', function() {
            $(this).closest('tr').remove();
            updateRowIndexes();
            calculateTotals();
        });

        // --- Auto calculate total if unit price is given ---
        $(document).on('input', '.qty, .unit_price', function() {
            const row = $(this).closest('tr');
            const qty = parseFloat(row.find('input[data-field="qty"]').val()) || 0;
            const price = parseFloat(row.find('input[data-field="unit_price"]').val());

            if (!isNaN(price) && price !== 0) {
                const total = qty * price;
                row.find('input[data-field="total"]').val(total.toFixed(2)).prop('readonly', true);
            } else {
                row.find('input[data-field="total"]').prop('readonly', false);
            }

            calculateTotals();
        });

        // --- Manual total change triggers full recalculation ---
        $(document).on('input', '.total', function() {
            calculateTotals();
        });

        // --- Show/Hide VAT section (check if PO has VAT set) ---
        function toggleVatSection() {
            const hasVat = $('input[name="vat"]').is(':checked');
            if (hasVat) {
                $('#vatSection').show();
                calculateTotals();
            } else {
                $('#vatSection').hide();
            }
        }

        // Initialize VAT section on load
        toggleVatSection();

        // --- VAT Checkbox Change Handler ---
        $('input[name="vat"]').on('change', function() {
            toggleVatSection();
        });

        // --- Pricing mode helpers ---
        function isLumpMode() {
            return $('input[name="pricing_mode"]:checked').val() === 'lump';
        }

        // Hide/show the cost columns and switch the manual total field based on pricing mode
        function applyPricingMode() {
            const lump = isLumpMode();

            // In lump mode the per-row costs are meaningless, so the columns are
            // hidden entirely (and disabled, so nothing is submitted for them).
            $('#itemsTable [data-col="cost"], #itemsTable [data-col="total"]').toggle(!lump);
            $('#itemsTable tbody tr').each(function() {
                $(this).find('input[data-field="unit_price"]').prop('disabled', lump);
                $(this).find('input[data-field="total"]').prop('disabled', lump);
            });

            // In lump mode the Total Amount is typed by the user; otherwise it is derived
            $('#total_amount_display').prop('readonly', !lump);
            $('.lump-hint').toggle(lump);

            calculateTotals();
        }

        // --- Item code column (independent of pricing mode) ---
        function applyItemCode() {
            const enabled = $('#has_item_code').is(':checked');

            $('#itemsTable [data-col="item_code"]').toggle(enabled);
            $('#itemsTable tbody tr').each(function() {
                const input = $(this).find('input[data-field="item_code"]');
                input.prop('disabled', !enabled);
                if (!enabled) {
                    input.val('');
                }
            });
        }

        // --- Recalculate subtotal, VAT, and grand total ---
        function calculateTotals() {
            let subtotal;

            if (isLumpMode()) {
                // Batch total is entered directly in the Total Amount field
                subtotal = parseFloat($('#total_amount_display').val()) || 0;
            } else {
                // Sum all item totals
                subtotal = 0;
                $('#itemsTable tbody tr').each(function() {
                    const rowTotal = parseFloat($(this).find('input[data-field="total"]').val()) || 0;
                    subtotal += rowTotal;
                });
                $('#total_amount_display').val(subtotal.toFixed(2));
            }

            // Calculate VAT if checkbox is checked
            const hasVat = $('input[name="vat"]').is(':checked');
            if (hasVat) {
                const vat = subtotal * 0.05;
                const grandTotal = subtotal + vat;

                $('#vat_amount_display').val(vat.toFixed(2));
                $('#grand_total_display').val(grandTotal.toFixed(2));
            }
        }

        // --- Pricing mode / item code change handlers ---
        $('input[name="pricing_mode"]').on('change', applyPricingMode);
        $('#has_item_code').on('change', applyItemCode);

        // --- Manual total input (lump mode) recalculates VAT ---
        $('#total_amount_display').on('input', function() {
            if (isLumpMode()) {
                calculateTotals();
            }
        });

        // Initialize numbering
        updateRowIndexes();

        // Trigger input events on all unit_price fields to compute row totals from stored data
        $('#itemsTable tbody tr').each(function() {
            $(this).find('input[data-field="unit_price"]').trigger('input');
        });

        // Apply the toggles on load (hides cost columns in lump mode) and recalc totals
        applyPricingMode();
        applyItemCode();

        // --- Auto-Load Vendor Terms & Conditions on Page Load ---
        function loadVendorTerms(showAlert = false) {
            var vendorId = {{ $po->lpout_vendor_id ?? 'null' }};
            console.log('loadVendorTerms called, vendorId:', vendorId);
            if (!vendorId) {
                console.log('No vendor ID, skipping load');
                return;
            }

            var url = '/vendors/' + vendorId + '/terms-and-conditions';
            console.log('Loading from URL:', url);

            $.ajax({
                url: url,
                type: 'GET',
                success: function(data) {
                    console.log('AJAX success, data:', data);
                    if (data.terms_and_conditions && data.terms_and_conditions.trim()) {
                        console.log('Setting Summernote content');
                        console.log('Summernote elements found:', $('.summernote').length);
                        try {
                            $('.summernote').summernote('code', data.terms_and_conditions);
                            console.log('Content set successfully');
                        } catch (e) {
                            console.log('Error setting content:', e);
                        }
                        if (showAlert) {
                            alert('Vendor terms loaded successfully');
                        }
                    } else if (showAlert) {
                        alert('This vendor does not have custom terms & conditions. Using default.');
                    }
                },
                error: function(xhr, status, error) {
                    console.log('AJAX error:', status, error, xhr.responseText);
                    if (showAlert) {
                        alert('Error loading vendor terms');
                    }
                }
            });
        }

        // Initialize Summernote
        $('.summernote').summernote({
            height: 250,
            toolbar: [
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['insert', ['link']],
                ['view', ['fullscreen', 'codeview']]
            ]
        });

        // Auto-load vendor terms AFTER Summernote is initialized
        loadVendorTerms(false);

        // Manual reload button click
        $('#load_po_vendor_terms_btn').on('click', function(e) {
            e.preventDefault();
            loadVendorTerms(true);
        });

        // --- CONFIRM BEFORE SUBMIT (message depends on which button was clicked) ---
        var clickedAction = 'forward';
        $('button[name="action"]').on('click', function() {
            clickedAction = $(this).val();
        });
        $('#itemsTable').closest('form').on('submit', function(e) {
            var message = clickedAction === 'save'
                ? 'Save changes to this Purchase Order without forwarding it to Admin?'
                : 'Are you sure you want to forward this Purchase Order to Admin?';
            if (!confirm(message)) {
                e.preventDefault();
            }
        });
    });
</script>
@endsection
