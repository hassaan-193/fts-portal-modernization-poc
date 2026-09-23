{!! Form::hidden('revised_from_lpoout_id', isset($po) ? $po->revised_from_lpoout_id : null) !!}
{!! Form::hidden('lpout_terms', isset($po) ? $po->lpout_terms : null) !!}

<div class="row">

    <div class="col-md-3 col-sm-6">
        <div class="form-group">
            {!! Form::label('request_number', 'Request Number:') !!}
            {!! Form::text('request_number', null, [
                'class' => 'form-control', 
                 'readonly' => true
            ]) !!}
            <small class="form-text text-muted">Request number will be generated automatically</small>
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <div class="form-group">
            {!! Form::label('date', 'Date:') !!}
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">
                        <i class="far fa-calendar-alt"></i>
                    </span>
                </div>
                {!! Form::text('date', isset($po) ? $po->date : \Carbon\Carbon::now()->toDateString(), ['class' => ($errors->has('date')) ? 'form-control is-invalid' : 'form-control', 'id' => 'date']) !!}
                @if ($errors->has('date'))
                    <span class="invalid-feedback">
                        <strong>{{ $errors->first('date') }}</strong>
                    </span>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <div class="form-group">
            {!! Form::label('due_date', 'Due Date:') !!}
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">
                        <i class="far fa-calendar-alt"></i>
                    </span>
                </div>
                {!! Form::text('due_date', isset($po) ? $po->due_date : null, ['class' => ($errors->has('due_date')) ? 'form-control is-invalid' : 'form-control', 'id' => 'due_date']) !!}
                @if ($errors->has('due_date'))
                    <span class="invalid-feedback">
                        <strong>{{ $errors->first('due_date') }}</strong>
                    </span>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <div class="form-group">
            {!! Form::label('delivery_date', 'Delivery Date:') !!}
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">
                        <i class="far fa-calendar-alt"></i>
                    </span>
                </div>
                {!! Form::text('delivery_date', isset($po) ? $po->delivery_date : null, ['class' => ($errors->has('delivery_date')) ? 'form-control is-invalid' : 'form-control', 'id' => 'delivery_date']) !!}
                @if ($errors->has('delivery_date'))
                    <span class="invalid-feedback">
                        <strong>{{ $errors->first('delivery_date') }}</strong>
                    </span>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <div class="form-group">
            {!! Form::label('request_type', 'Request Type:') !!}
            {!! Form::select('request_type', $requestTypes, isset($po) ? $po->request_type : null, ['class' => ($errors->has('request_type')) ? 'form-control is-invalid' : 'form-control', 'id' => 'request_type']) !!}
            @if ($errors->has('request_type'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('request_type') }}</strong>
                </span>
            @endif
        </div>
    </div>

</div>

<div class="row" id="quotation_section" style="display: none;">
    <div class="col-md-3 col-sm-6">
        <div class="form-group">
            {!! Form::label('quotation_id', 'LPO In:') !!}
            {!! Form::select('quotation_id', $quotations ?? [], isset($po) ? $po->quotation_id : null, ['class' => ($errors->has('quotation_id')) ? 'form-control is-invalid' : 'form-control', 'id' => 'quotation_id', 'placeholder' => 'Select an LPO In']) !!}
            @if ($errors->has('quotation_id'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('quotation_id') }}</strong>
                </span>
            @endif
        </div>
    </div>
</div>

<div class="row" id="project_section" style="display: none;">
    <div class="col-md-3 col-sm-6">
        <div class="form-group">
            {!! Form::label('project_id', 'Project (AMC):') !!}
            {!! Form::select('project_id', $projects ?? [], isset($po) ? $po->project_id : null, ['class' => ($errors->has('project_id')) ? 'form-control is-invalid' : 'form-control', 'id' => 'project_id', 'placeholder' => 'Select a project']) !!}
            @if ($errors->has('project_id'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('project_id') }}</strong>
                </span>
            @endif
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="form-group">
            {!! Form::label('other_info', 'Other Information:') !!}
            {!! Form::textarea('other_info', isset($po) ? $po->other_info : null, ['class' => ($errors->has('other_info')) ? 'form-control is-invalid' : 'form-control', 'id' => 'other_info', 'rows' => 3, 'placeholder' => 'Enter any additional information']) !!}
            @if ($errors->has('other_info'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('other_info') }}</strong>
                </span>
            @endif
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="form-group">
            {!! Form::label('files', 'Attach Files:') !!}
            <div class="custom-file">
                {!! Form::file('files[]', ['class' => 'custom-file-input', 'id' => 'files', 'multiple' => true, 'accept' => '.pdf']) !!}
                {!! Form::label('files', 'Choose files', ['class' => 'custom-file-label']) !!}
            </div>
            <small class="form-text text-muted">You can upload PDF file only</small>
        </div>
    </div>
</div>

<!-- LPOUT SECTION - MOVED TO STEP 1 -->
<hr class="my-4">
<div class="row">
    <div class="col-sm-12">
        <h4 class="text-danger mb-3">Local Purchase Outbound (LPOUT) Information</h4>
    </div>
</div>

<div class="row">
    <!-- Name Field -->
    <!-- <div class="col-md-3 col-sm-6">
        <div class="form-group">
            {!! Form::label('name', 'LPOUT Name:') !!}
            {!! Form::text('name', isset($po) ? $po->lpout_name : null, [
                'class' => ($errors->has('name')) ? 'form-control is-invalid' : 'form-control'
            ]) !!}
            @if ($errors->has('name'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('name') }}</strong>
                </span>
            @endif
        </div>
    </div> -->

    <!-- Vendor Field -->
    <div class="col-md-3 col-sm-6">
        <div class="form-group">
            {!! Form::label('vendor_id', 'Vendor:') !!}
            {!! Form::select('vendor_id', $vendors ?? [], isset($po) ? $po->lpout_vendor_id : null, [
                'class' => ($errors->has('vendor_id')) ? 'form-control is-invalid' : 'form-control',
                'id' => 'vendor_id',
                'placeholder' => 'Select Vendor'
            ]) !!}
            @if ($errors->has('vendor_id'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('vendor_id') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <!-- TRN No Field (our own company TRN - the vendor's TRN is never sent out) -->
    <div class="col-md-3 col-sm-6">
        <div class="form-group">
            {!! Form::label('trn_no', 'TRN No (Company):') !!}
            {!! Form::text('trn_no', config('purchase-orders.company_trn'), [
                'class' => ($errors->has('trn_no')) ? 'form-control is-invalid' : 'form-control',
                'id' => 'trn_no',
                'readonly' => true
            ]) !!}
            @if ($errors->has('trn_no'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('trn_no') }}</strong>
                </span>
            @endif
            <small class="form-text text-muted">Fire Technical Services' TRN - printed on the LPO sent to the vendor.</small>
        </div>
    </div>

    <!-- Kindly Attn Field -->
    <div class="col-md-3 col-sm-6">
        <div class="form-group">
            {!! Form::label('kindly_attn', 'Kindly Attn:') !!}
            {!! Form::text('kindly_attn', isset($po) ? $po->lpout_kindly_attn : null, [
                'class' => ($errors->has('kindly_attn')) ? 'form-control is-invalid' : 'form-control'
            ]) !!}
            @if ($errors->has('kindly_attn'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('kindly_attn') }}</strong>
                </span>
            @endif
        </div>
    </div>
</div>

<div class="row">
    <!-- LPOUT Date Field -->
    <div class="col-md-3 col-sm-6">
        <div class="form-group">
            {!! Form::label('lpout_date', 'LPOUT Date:') !!}
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">
                        <i class="far fa-calendar-alt"></i>
                    </span>
                </div>
                {!! Form::text('lpout_date', isset($po) ? $po->lpout_date : null, [
                    'class' => ($errors->has('lpout_date')) ? 'form-control is-invalid' : 'form-control',
                    'id' => 'lpout_date'
                ]) !!}
                @if ($errors->has('lpout_date'))
                    <span class="invalid-feedback">
                        <strong>{{ $errors->first('lpout_date') }}</strong>
                    </span>
                @endif
            </div>
        </div>
    </div>

    <!-- Payment Type Field -->
    <div class="col-md-3 col-sm-6">
        <div class="form-group">
            {!! Form::label('payment_type', 'Payment Type:') !!}
            {!! Form::select('payment_type', ['Cash' => 'Cash', 'Cheque' => 'Cheque'], isset($po) ? $po->lpout_payment_type : null, [
                'class' => 'form-control',
                'id' => 'payment_type'
            ]) !!}
        </div>
    </div>

    <!-- Cheque Date Field (hidden by default) -->
    <div class="col-md-3 col-sm-6" id="cheque_date_div" style="display:none;">
        <div class="form-group">
            {!! Form::label('cheque_date', 'Cheque Date:') !!}
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">
                        <i class="far fa-calendar-alt"></i>
                    </span>
                </div>
                {!! Form::text('cheque_date', isset($po) ? $po->lpout_cheque_date : null, [
                    'class' => 'form-control',
                    'id' => 'cheque_date'
                ]) !!}
            </div>
        </div>
    </div>
</div>

<!-- PAYMENT PREFERENCE SECTION - MOVED TO STEP 1 -->
<div class="row mt-4">
    <div class="col-sm-12">
        <h5 class="text-danger">Payment Preference</h5>
        <hr>
    </div>

    <div class="col-md-12 col-sm-12">
        <!-- Payment Preference Option -->
        <div class="form-group">
            {!! Form::label('lpo_payment_preference_option', 'Payment Preference Option:') !!}
            <div class="mt-2">
                <label class="custom-control custom-radio">
                    {!! Form::radio('lpo_payment_preference_option', 'default', (isset($po) && $po->lpout_payment_preference_option === 'default') || !isset($po), [
                        'class' => 'custom-control-input',
                        'id' => 'option_default'
                    ]) !!}
                    <span class="custom-control-label" for="option_default">Use Vendor's Default Preference</span>
                </label>
                <label class="custom-control custom-radio mt-2">
                    {!! Form::radio('lpo_payment_preference_option', 'custom', isset($po) && $po->lpout_payment_preference_option === 'custom', [
                        'class' => 'custom-control-input',
                        'id' => 'option_custom'
                    ]) !!}
                    <span class="custom-control-label" for="option_custom">Use Custom Payment Preference</span>
                </label>
            </div>
        </div>
    </div>

    <!-- Custom Payment Preference Text Input -->
    <div class="col-sm-12" id="custom_preference_section" style="display: none;">
        <div class="row mt-3">
            <div class="col-md-6 col-sm-12">
                <div class="form-group">
                    {!! Form::label('custom_payment_preference', 'Custom Payment Preference:') !!}
                    {!! Form::textarea('custom_payment_preference', isset($po) ? $po->payment_preference : null, [
                        'class' => ($errors->has('custom_payment_preference')) ? 'form-control is-invalid' : 'form-control',
                        'rows' => 3,
                        'placeholder' => 'Enter custom payment preference details'
                    ]) !!}
                    @if ($errors->has('custom_payment_preference'))
                        <span class="invalid-feedback">
                            <strong>{{ $errors->first('custom_payment_preference') }}</strong>
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- PRICING MODE -->
@php $pricingMode = old('pricing_mode', isset($po) ? ($po->lpout_pricing_mode ?? 'unit') : 'unit'); @endphp
<div class="row">
    <div class="col-sm-12 mt-4">
        <div class="form-group">
            <label class="d-block font-weight-bold">Pricing Mode:</label>
            <label class="radio-inline mr-3">
                {!! Form::radio('pricing_mode', 'unit', $pricingMode !== 'lump', ['id' => 'pricing_unit', 'class' => 'pricing_mode_radio']) !!}
                <span class="ml-1">Per-Unit (cost per item)</span>
            </label>
            <label class="radio-inline">
                {!! Form::radio('pricing_mode', 'lump', $pricingMode === 'lump', ['id' => 'pricing_lump', 'class' => 'pricing_mode_radio']) !!}
                <span class="ml-1">Lump-Sum (single batch total)</span>
            </label>
            <small class="form-text text-muted">Choose <strong>Lump-Sum</strong> when you only have one total price for the whole order (no per-unit cost). The cost columns are hidden and you enter the Total Amount below. VAT is applied at the department review step.</small>
        </div>
    </div>
</div>

<!-- ITEM CODE TOGGLE - works with either pricing mode -->
@php $hasItemCode = (bool) old('has_item_code', isset($po) ? $po->has_item_code : false); @endphp
<div class="row">
    <div class="col-sm-12">
        <div class="form-group">
            <label class="checkbox-inline font-weight-bold">
                {!! Form::hidden('has_item_code', 0) !!}
                {!! Form::checkbox('has_item_code', 1, $hasItemCode, ['id' => 'has_item_code']) !!}
                <span class="ml-1">Capture Item Code</span>
            </label>
            <small class="form-text text-muted">Adds an <strong>ITEM CODE</strong> column before the material name. It carries through to department review, admin approval, the LPO print and the vendor email.</small>
        </div>
    </div>
</div>

<!-- ITEMS TABLE -->
<div class="row">
    <div class="col-sm-12 mt-4">
        <h5><strong>Items</strong> <small class="text-muted">(Optional)</small></h5>
        <table class="table table-bordered" id="itemsTable">
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
            <tbody></tbody>
        </table>
        <button type="button" class="btn btn-outline-primary" id="addRow">+ Add Row</button>
        <small class="form-text text-muted d-block mt-2">You can add items now or leave it empty - items can be added or modified in the next step</small>
    </div>
</div>

<!-- LUMP-SUM TOTAL (shown only in Lump-Sum mode) -->
<div class="row" id="manual_total_section" style="display: none;">
    <div class="col-md-3 col-sm-6">
        <div class="form-group">
            {!! Form::label('manual_total', 'Total Amount (AED):') !!}
            {!! Form::number('manual_total', old('manual_total', isset($po) ? $po->lpout_manual_total : null), [
                'class' => 'form-control',
                'id' => 'manual_total',
                'min' => '0',
                'step' => '0.01',
                'placeholder' => 'Total price for the whole order'
            ]) !!}
            <small class="form-text text-muted">Single total price for the whole batch (no per-unit cost).</small>
        </div>
    </div>
</div>

@php $isResubmission = isset($po) && $po->exists && $po->isSentBack(); @endphp
<div class="row" style="margin-top: 30px;">
    <div class="col-sm-12">
        <div class="form-group">
            {!! Form::submit($isResubmission ? 'Save & Resubmit to Department' : __('crud.save'), ['class' => 'btn btn-danger btn-flat btn-lg']) !!}
            <a href="{{ route('purchase-orders.index') }}" class="btn btn-outline-danger btn-flat btn-lg text-maroon">@lang('crud.cancel')</a>
        </div>
    </div>
</div>

@section('css')
@parent
    <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css')}}">
    <link rel="stylesheet" href="{{ asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{ asset('plugins/daterangepicker/daterangepicker.css')}}">
@endsection

@section('scripts')
@parent
<script src="{{ asset('plugins/select2/js/select2.full.min.js')}}"></script>
<script src="{{ asset('plugins/daterangepicker/daterangepicker.js')}}"></script>

<script>
    $(document).ready(function() {
        // Initialize Select2 for Vendor, Quotation, and Project
        $('#vendor_id').select2({
            theme: 'bootstrap4',
            placeholder: 'Select Vendor',
            allowClear: true
        });

        $('#quotation_id').select2({
            theme: 'bootstrap4',
            placeholder: 'Select an LPO In',
            allowClear: true
        });

        $('#project_id').select2({
            theme: 'bootstrap4',
            placeholder: 'Select Project',
            allowClear: true
        });

        // Initialize date pickers
        $('#date').daterangepicker({
            singleDatePicker: true,
            timePicker: false,
            locale: { format: 'YYYY-MM-DD' }
        });

        $('#due_date').daterangepicker({
            singleDatePicker: true,
            timePicker: false,
            locale: { format: 'YYYY-MM-DD' }
        });

        $('#delivery_date').daterangepicker({
            singleDatePicker: true,
            timePicker: false,
            locale: { format: 'YYYY-MM-DD' }
        });

        $('#lpout_date').daterangepicker({
            singleDatePicker: true,
            timePicker: false,
            locale: { format: 'YYYY-MM-DD' }
        });

        $('#cheque_date').daterangepicker({
            singleDatePicker: true,
            timePicker: false,
            locale: { format: 'YYYY-MM-DD' }
        });

        // --- HANDLE REQUEST TYPE DROPDOWN ---
        function handleRequestTypeChange() {
            const requestType = $('#request_type').val();
            
            $('#quotation_section').hide();
            $('#project_section').hide();
            
            if (requestType === 'project') {
                $('#quotation_section').show();
            } else if (requestType === 'maintenance') {
                $('#project_section').show();
            }
        }

        // Initialize on load
        handleRequestTypeChange();

        // Listen for changes
        $('#request_type').on('change', handleRequestTypeChange);

        // --- HANDLE PAYMENT TYPE (Show/Hide Cheque Date) ---
        function toggleChequeDate() {
            if ($('#payment_type').val() === 'Cheque') {
                $('#cheque_date_div').show();
            } else {
                $('#cheque_date_div').hide();
                $('#cheque_date').val('');
            }
        }

        toggleChequeDate();
        $('#payment_type').on('change', toggleChequeDate);

        // --- HANDLE PAYMENT PREFERENCE OPTION ---
        function togglePaymentPreferenceOption() {
            const option = $('input[name="lpo_payment_preference_option"]:checked').val();
            
            if (option === 'custom') {
                $('#custom_preference_section').show();
            } else {
                $('#custom_preference_section').hide();
                $('#custom_payment_preference').val('');
            }
        }

        togglePaymentPreferenceOption();
        $('input[name="lpo_payment_preference_option"]').on('change', togglePaymentPreferenceOption);

        // --- HANDLE FILE INPUT LABEL UPDATE ---
        $('#files').on('change', function() {
            var fileCount = this.files.length;
            var label = $(this).siblings('.custom-file-label');
            if (fileCount === 1) {
                label.text(this.files[0].name);
            } else if (fileCount > 1) {
                label.text(fileCount + ' files selected');
            } else {
                label.text('Choose files');
            }
        });

        // --- BUILD ROW HTML ---
        function buildRowHtml() {
            return `
                <tr>
                    <td class="row-number" style="width: 60px;"></td>
                    <td data-col="item_code" style="width: 150px;"><input type="text" data-field="item_code" class="form-control" /></td>
                    <td><input type="text" data-field="material_name" class="form-control" /></td>
                    <td><input type="text" data-field="unit" class="form-control" /></td>
                    <td style="width: 120px;"><input type="number" min="0" step="any" data-field="quantity" class="form-control qty" /></td>
                    <td data-col="cost" style="width: 150px;"><input type="number" min="0" step="0.01" data-field="cost" class="form-control unit_price" /></td>
                    <td data-col="total" style="width: 150px;"><input type="number" min="0" step="0.01" data-field="total" class="form-control total" readonly /></td>
                    <td style="width: 100px;"><button type="button" class="btn btn-danger btn-sm removeRow">X</button></td>
                </tr>
            `;
        }

        // --- UPDATE ROW INDEXES ---
        function updateRowIndexes() {
            $('#itemsTable tbody tr').each(function(i) {
                const idx = i;
                $(this).find('.row-number').text(idx + 1);
                $(this).find('input').each(function() {
                    const field = $(this).data('field');
                    if (field) {
                        $(this).attr('name', `items[${idx}][${field}]`);
                    }
                });
            });
        }

        // --- ADD ROW ---
        $('#addRow').click(function() {
            $('#itemsTable tbody').append(buildRowHtml());
            updateRowIndexes();
            // Ensure the new row's optional columns respect the current toggles
            applyPricingMode();
            applyItemCode();
        });

        // --- REMOVE ROW ---
        $(document).on('click', '.removeRow', function() {
            $(this).closest('tr').remove();
            updateRowIndexes();
        });

        // --- LOAD EXISTING ITEMS (old input on validation failure, or saved data in edit mode) ---
        @php
            $itemsData = null;
            if (old('items')) {
                $itemsData = old('items');
            } elseif (isset($po) && $po->items) {
                $itemsData = is_string($po->items) ? json_decode($po->items, true) : $po->items;
            }
        @endphp
        @if($itemsData && is_array($itemsData) && count($itemsData) > 0)
        var existingItems = @json($itemsData);
        if (existingItems && existingItems.length > 0) {
            existingItems.forEach(function(item) {
                $('#itemsTable tbody').append(buildRowHtml());
                var lastRow = $('#itemsTable tbody tr:last');
                lastRow.find('input[data-field="item_code"]').val(item.item_code || '');
                lastRow.find('input[data-field="material_name"]').val(item.material_name || '');
                lastRow.find('input[data-field="unit"]').val(item.unit || '');
                lastRow.find('input[data-field="quantity"]').val(item.quantity || '');
                lastRow.find('input[data-field="cost"]').val(item.cost || '');
                lastRow.find('input[data-field="total"]').val(item.total || '');
            });
            updateRowIndexes();
            $('#itemsTable tbody tr').find('input[data-field="cost"]').each(function() {
                $(this).trigger('input');
            });
        }
        @endif

        // --- AUTO CALCULATE TOTAL WHEN QTY OR COST CHANGES ---
        $(document).on('input', '.qty, .unit_price', function() {
            const row = $(this).closest('tr');
            const qty = parseFloat(row.find('input[data-field="quantity"]').val()) || 0;
            const cost = parseFloat(row.find('input[data-field="cost"]').val()) || 0;
            
            if (!isNaN(cost) && cost !== 0) {
                const total = qty * cost;
                row.find('input[data-field="total"]').val(total.toFixed(2)).prop('readonly', true);
            } else {
                row.find('input[data-field="total"]').prop('readonly', false);
            }
        });

        updateRowIndexes();

        // TRN is our own company TRN and never changes with the vendor, so there
        // is deliberately no vendor -> TRN auto-fill here any more.

        // --- PRICING MODE (Per-Unit vs Lump-Sum) ---
        function isLumpMode() {
            return $('input[name="pricing_mode"]:checked').val() === 'lump';
        }

        function applyPricingMode() {
            const lump = isLumpMode();

            // In lump mode the per-row costs are meaningless, so the columns are
            // hidden entirely (and disabled, so nothing is submitted for them).
            $('#itemsTable [data-col="cost"], #itemsTable [data-col="total"]').toggle(!lump);
            $('#itemsTable tbody tr').each(function() {
                $(this).find('input[data-field="cost"]').prop('disabled', lump);
                $(this).find('input[data-field="total"]').prop('disabled', lump);
            });

            // Show the single batch-total field only in lump mode
            $('#manual_total_section').toggle(lump);

            // Clear the manual total when leaving lump mode so it is not persisted
            if (!lump) {
                $('#manual_total').val('');
            }
        }

        // --- ITEM CODE COLUMN (independent of pricing mode) ---
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

        $('input[name="pricing_mode"]').on('change', applyPricingMode);
        $('#has_item_code').on('change', applyItemCode);

        // Apply on load (handles edit mode where either toggle may already be on)
        applyPricingMode();
        applyItemCode();

        // --- CONFIRM BEFORE SUBMIT ---
        $('#itemsTable').closest('form').on('submit', function(e) {
            const message = @json($isResubmission
                ? 'Resubmit this corrected request to the department for review?'
                : 'Are you sure you want to submit this Purchase Order request?');
            if (!confirm(message)) {
                e.preventDefault();
            }
        });
    });
</script>
@endsection
