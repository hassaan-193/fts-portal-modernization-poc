<div class="row">
    @if(isset($lpoout))
    <div class="col-md-3 col-sm-6">
        <!-- lpo_invoice_no Field (Edit Mode - Show existing) -->
        <div class="form-group">
            {!! Form::label('lpo_invoice_no', __('models/lpoouts.fields.lpo_invoice_no').':') !!}
            {!! Form::text('lpo_invoice_no', null, [
                'class' => 'form-control', 
                'readonly' => true
            ]) !!}
            <small class="form-text text-muted">Invoice number cannot be changed</small>
        </div>
    </div>
    @else
    <div class="col-md-3 col-sm-6">
        <!-- lpo_invoice_no Field (Create Mode - Auto-generated) -->
        <div class="form-group">
            {!! Form::label('lpo_invoice_no', __('models/lpoouts.fields.lpo_invoice_no').':') !!}
            {!! Form::text('lpo_invoice_no', null, [
                'class' => 'form-control', 
                'readonly' => true,
            ]) !!}
            <small class="form-text text-muted">LPO number will be generated automatically</small>
        </div>
    </div>
    @endif

    <div class="col-md-3 col-sm-6">
        <!-- Name Field -->
        <div class="form-group">
            {!! Form::label('name', __('models/lpoouts.fields.name').':') !!}
            {!! Form::text('name', null, ['class' => ($errors->has('name')) ? 'form-control is-invalid' : 'form-control']) !!}
            @if ($errors->has('name'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('name') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <!-- Lpo Type Out Field -->
        <div class="form-group">
            {!! Form::label('lpo_out_type_id', __('models/lpoouts.fields.lpo_out_type_id').':') !!}
            {!! Form::select('lpo_out_type_id', $lpo_out_typeItems, null, ['class' => ($errors->has('lpo_out_type_id')) ? 'form-control is-invalid' : 'form-control' ,'id'=>'lpo_out_type_id']) !!}
            @if ($errors->has('lpo_out_type_id'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('lpo_out_type_id') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="col-md-3 col-sm-6" {{ ($errors->has('project_id')) || ( isset($lpoout) && ($lpoout->project_id != null) ) ? '' : 'hidden' }}  id="div_project">
        <!-- Project Field -->
        <div class="form-group">
            {!! Form::label('project_id', __('models/lpoouts.fields.project_id').':') !!}
            {!! Form::select('project_id', $projectItems, null, ['class' => ($errors->has('project_id')) ? 'form-control is-invalid' : 'form-control' ,'id'=>'project_id']) !!}
            @if ($errors->has('project_id'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('project_id') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <!-- Vendor Id Field -->
        <div class="form-group">
            {!! Form::label('vendor_id', __('models/lpoouts.fields.vendor_id').':') !!}
            {!! Form::select('vendor_id', $vendorItems, null, ['class' => ($errors->has('vendor_id')) ? 'form-control is-invalid' : 'form-control' ,'id'=>'vendor_id']) !!}
            @if ($errors->has('vendor_id'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('vendor_id') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <!-- TRN No Field (our own company TRN - the vendor's TRN is never sent out) -->
        <div class="form-group">
            {!! Form::label('trn_no', 'TRN No (Company):') !!}
            {!! Form::text('trn_no', config('purchase-orders.company_trn'), ['class' => ($errors->has('trn_no')) ? 'form-control is-invalid' : 'form-control','id' => 'trn_no','readonly' => true]) !!}
            @if ($errors->has('trn_no'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('trn_no') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <!-- Kindly Attn Field -->
        <div class="form-group">
            {!! Form::label('kindly_attn', 'Kindly Attn:') !!}
            {!! Form::text('kindly_attn', null, ['class' => ($errors->has('kindly_attn')) ? 'form-control is-invalid' : 'form-control']) !!}
            @if ($errors->has('kindly_attn'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('kindly_attn') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <!-- Date Field -->
        <div class="form-group">
            {!! Form::label('date', __('models/lpoouts.fields.date').':') !!}
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">
                        <i class="far fa-calendar-alt"></i>
                    </span>
                </div>
                {!! Form::text('date', null, ['class' => ($errors->has('date')) ? 'form-control is-invalid' : 'form-control' ,'id'=>'date']) !!}
                @if ($errors->has('date'))
                    <span class="invalid-feedback">
                        <strong>{{ $errors->first('date') }}</strong>
                    </span>
                @endif
            </div>
        </div>
    </div>

    <!-- Hidden Amount Field - calculated from items total -->
    {!! Form::hidden('amount', null, ['id' => 'amount_hidden']) !!}

    <div class="col-md-3 col-sm-6">
        <!-- Payment Type Field -->
        <div class="form-group">
            {!! Form::label('payment_type', 'Payment Type:') !!}
            {!! Form::select('payment_type', ['Cash' => 'Cash', 'Cheque' => 'Cheque'], null, [
                'class' => 'form-control',
                'id' => 'payment_type'
            ]) !!}
        </div>
    </div>

    <div class="col-md-3 col-sm-6" id="cheque_date_div" style="display:none;">
        <!-- Cheque Date Field -->
        <div class="form-group">
            {!! Form::label('cheque_date', 'Cheque Date:') !!}
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">
                        <i class="far fa-calendar-alt"></i>
                    </span>
                </div>
                {!! Form::text('cheque_date', null, ['class' => 'form-control', 'id' => 'cheque_date']) !!}
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <!-- File Field -->
        <div class="form-group pt-1">
            <div class="custom-file mt-4">
                {!! Form::file('file',['class' => 'custom-file-input']) !!}
                {!! Form::label('file', __('models/lpoouts.fields.file').':' , ['class' => 'custom-file-label']) !!}
            </div>
        </div>
    </div>

    <!-- VAT Field -->
    <div class="col-md-3 col-sm-6">
        <div class="form-group pt-1">
            <div class="custom-file mt-4">
                {!! Form::label('vat', __('models/lpoouts.fields.vat').':') !!}
                <label class="checkbox-inline">
                    {!! Form::hidden('vat', 0) !!}
                    {!! Form::checkbox('vat', '1', null) !!}
                </label>
            </div>
        </div>
    </div>

    <!-- Payment Preference Section -->
    <div class="col-sm-12 mt-4">
        <h4 class="text-danger">@lang('models/lpoouts.fields.lpo_payment_preference')</h4>
        <hr>
    </div>

    <div class="col-md-12 col-sm-12">
        <!-- Payment Preference Option -->
        <div class="form-group">
            {!! Form::label('lpo_payment_preference_option', 'Payment Preference Option:') !!}
            <div class="mt-2">
                <label class="custom-control custom-radio">
                    {!! Form::radio('lpo_payment_preference_option', 'default', true, ['class' => 'custom-control-input', 'id' => 'option_default']) !!}
                    <span class="custom-control-label" for="option_default">Use Vendor's Default Preference</span>
                </label>
                <label class="custom-control custom-radio mt-2">
                    {!! Form::radio('lpo_payment_preference_option', 'custom', false, ['class' => 'custom-control-input', 'id' => 'option_custom']) !!}
                    <span class="custom-control-label" for="option_custom">Use Custom Payment Preference</span>
                </label>
            </div>
        </div>
    </div>

    <!-- Default Payment Preference Display -->
    <div class="col-sm-12" id="default_preference_section" style="display: none;">
        <div class="card card-outline card-danger">
            <div class="card-header">
                <h5 class="card-title">Vendor's Default Payment Preference</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <p><strong>Payment Preference:</strong></p>
                        <p id="vendor_payment_preference_display">-</p>
                    </div>
                    <div class="col-md-4" id="vendor_pdc_days_display_section" style="display: none;">
                        <p><strong>Number of Days (PDC):</strong></p>
                        <p id="vendor_pdc_days_display">-</p>
                    </div>
                    <div class="col-md-4" id="vendor_pdc_option_display_section" style="display: none;">
                        <p><strong>Payment Processing Option:</strong></p>
                        <p id="vendor_pdc_option_display">-</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Custom Payment Preference Fields -->
    <div class="col-sm-12" id="custom_preference_section" style="display: none;">
        <div class="row mt-3">
            <div class="col-md-4 col-sm-6">
                <div class="form-group">
                    {!! Form::label('lpo_payment_preference', __('models/lpoouts.fields.lpo_payment_preference').':') !!}
                    {!! Form::select('lpo_payment_preference', ['cod' => 'Cash on Delivery', 'pdc' => 'PDC (Post Dated Check)'], null, ['class' => ($errors->has('lpo_payment_preference')) ? 'form-control is-invalid' : 'form-control', 'id' => 'lpo_payment_preference']) !!}
                    @if ($errors->has('lpo_payment_preference'))
                        <span class="invalid-feedback">
                            <strong>{{ $errors->first('lpo_payment_preference') }}</strong>
                        </span>
                    @endif
                </div>
            </div>

            <div class="col-md-4 col-sm-6" id="custom_pdc_days_section" style="display: none;">
                <div class="form-group">
                    {!! Form::label('lpo_pdc_number_of_days', __('models/lpoouts.fields.lpo_pdc_number_of_days').':') !!}
                    {!! Form::number('lpo_pdc_number_of_days', null, ['class' => ($errors->has('lpo_pdc_number_of_days')) ? 'form-control is-invalid' : 'form-control', 'placeholder' => 'Number of days']) !!}
                    @if ($errors->has('lpo_pdc_number_of_days'))
                        <span class="invalid-feedback">
                            <strong>{{ $errors->first('lpo_pdc_number_of_days') }}</strong>
                        </span>
                    @endif
                </div>
            </div>

            <div class="col-md-4 col-sm-6" id="custom_pdc_option_section" style="display: none;">
                <div class="form-group">
                    {!! Form::label('lpo_pdc_payment_option', __('models/lpoouts.fields.lpo_pdc_payment_option').':') !!}
                    {!! Form::select('lpo_pdc_payment_option', ['on_delivery_amount' => 'On Delivery', 'on_payment_release_amount' => 'On PO Release'], null, ['class' => ($errors->has('lpo_pdc_payment_option')) ? 'form-control is-invalid' : 'form-control', 'id' => 'lpo_pdc_payment_option']) !!}
                    @if ($errors->has('lpo_pdc_payment_option'))
                        <span class="invalid-feedback">
                            <strong>{{ $errors->first('lpo_pdc_payment_option') }}</strong>
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @php
        $pricingMode = old('pricing_mode', isset($lpoout) ? ($lpoout->pricing_mode ?? 'unit') : 'unit');
        $hasItemCode = (bool) old('has_item_code', isset($lpoout) ? $lpoout->has_item_code : false);
    @endphp
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
            <small class="form-text text-muted">In <strong>Lump-Sum</strong> mode the per-row price columns are hidden here and left out of the LPO print and the vendor email.</small>
        </div>
        <div class="form-group">
            <label class="checkbox-inline font-weight-bold">
                {!! Form::hidden('has_item_code', 0) !!}
                {!! Form::checkbox('has_item_code', 1, $hasItemCode, ['id' => 'has_item_code']) !!}
                <span class="ml-1">Capture Item Code</span>
            </label>
            <small class="form-text text-muted">Adds an <strong>ITEM CODE</strong> column before the description, on screen and in the LPO print and vendor email.</small>
        </div>
    </div>

    <div class="col-sm-12 mt-4">
        <h5><strong>Items</strong></h5>
        <table class="table table-bordered" id="itemsTable">
            <thead class="bg-light">
                <tr>
                    <th>NO</th>
                    <th data-col="item_code">ITEM CODE</th>
                    <th>ITEM DESCRIPTION</th>
                    <th>UNIT</th>
                    <th>QTY</th>
                    <th data-col="cost">UNIT PRICE (AED)</th>
                    <th data-col="total">TOTAL (AED)</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
        <button type="button" class="btn btn-outline-primary" id="addRow">+ Add Row</button>
    </div>

    <div class="col-sm-12 mt-3">
        <table class="table table-borderless">
            <tr>
                <th style="width:70%">TOTAL AMOUNT (IN AED)</th>
                <td>
                    <input type="number" min="0" step="0.01" class="form-control" id="total_amount_display" value="{{ isset($lpoout) ? $lpoout->amount : '' }}" readonly>
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

    <div class="row" id="terms_section">
        <div class="col-md-12 col-sm-12">
            <div class="form-group">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <label for="terms">Terms & Conditions:</label>
                    <button type="button" id="load_vendor_terms_btn" class="btn btn-sm btn-outline-info" style="display:none;">
                        <i class="fa fa-refresh"></i> Load Vendor Terms
                    </button>
                </div>
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
                ', ['class' => 'form-control summernote', 'id' => 'terms']) !!}
                <small class="form-text text-muted">Default T&C will be used. Click "Load Vendor Terms" to use vendor-specific T&C if available.</small>
            </div>
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
<script src="{{ asset('plugins/bs-custom-file-input/bs-custom-file-input.min.js')}}"></script>
<script src="{{ asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
<script src="{{ asset('plugins/summernote/summernote-bs4.min.js') }}"></script>

<script>
    $(document).ready(function () {
        // Initialize file input
        bsCustomFileInput.init();

        // Initialize date pickers
        $('#date').daterangepicker({
            singleDatePicker: true,
            timePicker: false,
            locale: {
                format: 'YYYY-MM-DD'
            }
        });

        $('#cheque_date').daterangepicker({
            singleDatePicker: true,
            timePicker: false,
            locale: { format: 'YYYY-MM-DD' }
        });

        // Initialize Select2
        $('#lpo_out_type_id').select2({
            theme: 'bootstrap4'
        }).on('change', function(e) {
            var data = $("#lpo_out_type_id option:selected").text().trim();
            if(data == 'Project'){
                $('#div_project').attr('hidden',false);
                $('#project_id').val('').trigger('change');
            }else{
                $('#div_project').attr('hidden',true);
            }
        });

        $('#project_id').select2({
            theme: 'bootstrap4',
            placeholder: 'Select an option',
            allowClear: true
        });

        $('#vendor_id').select2({
            theme: 'bootstrap4',
            placeholder: 'Select Vendor',
            allowClear: true
        }).on('change', function() {
            // Show/hide load vendor terms button when vendor is selected
            if ($(this).val()) {
                $('#load_vendor_terms_btn').show();
            } else {
                $('#load_vendor_terms_btn').hide();
            }
        });

        // --- Auto-Load Vendor Terms & Conditions ---
        function loadVendorTermsLPO(showAlert = false) {
            var vendorId = $('#vendor_id').val();
            if (!vendorId) {
                if (showAlert) {
                    alert('Please select a vendor first');
                }
                return;
            }

            $.ajax({
                url: '/vendors/' + vendorId + '/terms-and-conditions',
                type: 'GET',
                success: function(data) {
                    if (data.terms_and_conditions && data.terms_and_conditions.trim()) {
                        $('#terms').summernote('code', data.terms_and_conditions);
                        if (showAlert) {
                            alert('Vendor terms loaded successfully');
                        }
                    } else if (showAlert) {
                        alert('This vendor does not have custom terms & conditions. Using default.');
                    }
                },
                error: function() {
                    if (showAlert) {
                        alert('Error loading vendor terms');
                    }
                }
            });
        }

        // When vendor changes, auto-load their terms
        $('#vendor_id').on('change', function() {
            loadVendorTermsLPO(false);
        });

        // Manual reload button click
        $('#load_vendor_terms_btn').on('click', function() {
            loadVendorTermsLPO(true);
        });

        // --- Show/Hide Cheque Date ---
        function toggleChequeDate() {
            if ($('#payment_type').val() === 'Cheque') {
                $('#cheque_date_div').show();
            } else {
                $('#cheque_date_div').hide();
                $('#cheque_date').val('');
            }
        }

        // Initialize on page load
        toggleChequeDate();

        $('#payment_type').on('change', function() {
            toggleChequeDate();
        });

        // --- Build a new row ---
        function buildRowHtml() {
            return `
                <tr>
                    <td class="row-number"></td>
                    <td data-col="item_code"><input type="text" data-field="item_code" class="form-control" /></td>
                    <td><input type="text" data-field="description" class="form-control" /></td>
                    <td><input type="text" data-field="unit" class="form-control" /></td>
                    <td><input type="number" min="0" step="any" data-field="qty" class="form-control qty" /></td>
                    <td data-col="cost"><input type="number" min="0" step="any" data-field="unit_price" class="form-control unit_price" /></td>
                    <td data-col="total"><input type="number" min="0" step="any" data-field="total" class="form-control total" /></td>
                    <td><button type="button" class="btn btn-danger btn-sm removeRow">X</button></td>
                </tr>
            `;
        }

        // --- Update row numbering and field names ---
        function updateRowIndexes() {
            $('#itemsTable tbody tr').each(function(i) {
                const idx = i + 1;
                $(this).find('.row-number').text(idx);
                $(this).find('input').each(function() {
                    const field = $(this).data('field');
                    if (field) {
                        $(this).attr('name', `items[${idx}][${field}]`);
                    }
                });
            });
        }

        // --- Add Row ---
        $('#addRow').click(function() {
            $('#itemsTable tbody').append(buildRowHtml());
            updateRowIndexes();
            applyPricingMode();
            applyItemCode();
        });

        // --- Optional columns: hidden when they carry no meaning ---
        function isLumpMode() {
            return $('input[name="pricing_mode"]:checked').val() === 'lump';
        }

        function applyPricingMode() {
            const lump = isLumpMode();

            $('#itemsTable [data-col="cost"], #itemsTable [data-col="total"]').toggle(!lump);
            $('#itemsTable tbody tr').each(function() {
                $(this).find('input[data-field="unit_price"]').prop('disabled', lump);
                $(this).find('input[data-field="total"]').prop('disabled', lump);
            });

            // In lump mode the batch total is typed straight into TOTAL AMOUNT
            $('#total_amount_display').prop('readonly', !lump);
            $('.lump-hint').toggle(lump);

            calculateTotals();
        }

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

        // --- Show/Hide VAT section based on checkbox ---
        function toggleVatSection() {
            if ($('input[name="vat"]').is(':checked')) {
                $('#vatSection').show();
                calculateTotals();
            } else {
                $('#vatSection').hide();
                $('#vat_amount_display').val('');
                $('#grand_total_display').val('');
            }
        }

        // Initialize VAT section on load
        toggleVatSection();

        $('input[name="vat"]').on('change', function() {
            toggleVatSection();
        });

        // --- Typed batch total (lump mode) recalculates VAT and the hidden amount ---
        $('#total_amount_display').on('input', function() {
            if (isLumpMode()) {
                calculateTotals();
            }
        });

        // --- Recalculate subtotal, VAT, and grand total ---
        function calculateTotals() {
            let subtotal = 0;

            if (isLumpMode()) {
                // Batch total is entered directly, not derived from the rows
                subtotal = parseFloat($('#total_amount_display').val()) || 0;
            } else {
                // Sum all item totals
                $('#itemsTable tbody tr').each(function() {
                    const rowTotal = parseFloat($(this).find('input[data-field="total"]').val()) || 0;
                    subtotal += rowTotal;
                });

                // Update display field
                $('#total_amount_display').val(subtotal.toFixed(2));
            }

            // Update hidden field for amount (this goes to backend)
            $('#amount_hidden').val(subtotal.toFixed(2));

            // Calculate VAT if checkbox is checked
            if ($('input[name="vat"]').is(':checked')) {
                const vat = subtotal * 0.05;
                const grandTotal = subtotal + vat;
                
                $('#vat_amount_display').val(vat.toFixed(2));
                $('#grand_total_display').val(grandTotal.toFixed(2));
            }
        }

        // --- POPULATE ITEMS ON EDIT ---
        @if(isset($lpoout) && $lpoout->items)
            var existingItems = @json($lpoout->items);
            
            if (existingItems && existingItems.length > 0) {
                existingItems.forEach(function(item) {
                    $('#itemsTable tbody').append(buildRowHtml());
                    
                    var lastRow = $('#itemsTable tbody tr:last');
                    lastRow.find('input[data-field="item_code"]').val(item.item_code || '');
                    lastRow.find('input[data-field="description"]').val(item.description || '');
                    lastRow.find('input[data-field="unit"]').val(item.unit || '');
                    lastRow.find('input[data-field="qty"]').val(item.qty || '');
                    lastRow.find('input[data-field="unit_price"]').val(item.unit_price || '');
                    lastRow.find('input[data-field="total"]').val(item.total || '');
                });
                
                updateRowIndexes();
                calculateTotals();
            }
        @endif

        // Initialize numbering
        updateRowIndexes();
        calculateTotals(); // Calculate totals on page load
        applyPricingMode();
        applyItemCode();
    });


    // TRN is our own company TRN and never changes with the vendor, so only the
    // payment preference is fetched when a vendor is selected.
    $('#vendor_id').on('change', function () {
        var vendorId = $(this).val();

        if (vendorId) {
            // Fetch vendor payment preference
            $.ajax({
                url: '/vendor/' + vendorId + '/payment-preference',
                type: 'GET',
                success: function (data) {
                    displayVendorPaymentPreference(data);
                }
            });
        }
    });

    // Display vendor payment preference
    function displayVendorPaymentPreference(data) {
        const preference = data.payment_preference;
        const days = data.pdc_number_of_days;
        const option = data.pdc_payment_option;

        // Display preference
        const prefText = preference === 'cod' ? 'Cash on Delivery' : 'PDC (Post Dated Check)';
        $('#vendor_payment_preference_display').text(prefText);

        // Show/hide PDC fields based on preference
        if (preference === 'pdc') {
            $('#vendor_pdc_days_display_section').show();
            $('#vendor_pdc_option_display_section').show();
            $('#vendor_pdc_days_display').text(days || '-');
            
            const optionText = option === 'on_delivery_amount' ? 'On Delivery' : 'On PO Release';
            $('#vendor_pdc_option_display').text(optionText);
        } else {
            $('#vendor_pdc_days_display_section').hide();
            $('#vendor_pdc_option_display_section').hide();
        }
    }

    // Handle payment preference option change
    function togglePaymentPreferenceOption() {
        const option = $('input[name="lpo_payment_preference_option"]:checked').val();
        
        if (option === 'default') {
            $('#default_preference_section').show();
            $('#custom_preference_section').hide();
            // Clear custom fields
            $('#lpo_payment_preference').val('');
            $('#lpo_pdc_number_of_days').val('');
            $('#lpo_pdc_payment_option').val('');
        } else {
            $('#default_preference_section').hide();
            $('#custom_preference_section').show();
        }
    }

    // Initialize on page load
    togglePaymentPreferenceOption();

    // Listen for changes
    $('input[name="lpo_payment_preference_option"]').on('change', function() {
        togglePaymentPreferenceOption();
    });

    // Handle custom payment preference change
    function toggleCustomPaymentFields() {
        const preference = $('#lpo_payment_preference').val();
        
        if (preference === 'pdc') {
            $('#custom_pdc_days_section').show();
            $('#custom_pdc_option_section').show();
            $('#lpo_pdc_number_of_days').prop('required', true);
            $('#lpo_pdc_payment_option').prop('required', true);
        } else {
            $('#custom_pdc_days_section').hide();
            $('#custom_pdc_option_section').hide();
            $('#lpo_pdc_number_of_days').prop('required', false).val('');
            $('#lpo_pdc_payment_option').prop('required', false).val('');
        }
    }

    // Initialize custom payment fields on load
    toggleCustomPaymentFields();

    // Listen for custom payment preference changes
    $('#lpo_payment_preference').on('change', function() {
        toggleCustomPaymentFields();
    });

    // Populate form on edit mode
    @if(isset($lpoout))
        const lpoPaymentPrefOption = '{{ $lpoout->lpo_payment_preference ? "custom" : "default" }}';
        $('input[name="lpo_payment_preference_option"][value="' + lpoPaymentPrefOption + '"]').prop('checked', true);
        
        if (lpoPaymentPrefOption === 'custom') {
            $('#lpo_payment_preference').val('{{ $lpoout->lpo_payment_preference }}');
            $('#lpo_pdc_number_of_days').val('{{ $lpoout->lpo_pdc_number_of_days }}');
            $('#lpo_pdc_payment_option').val('{{ $lpoout->lpo_pdc_payment_option }}');
        }
        
        togglePaymentPreferenceOption();
        toggleCustomPaymentFields();
    @endif
</script>

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

<div class="row">
    <div class="col-sm-12">
        <!-- Submit Field -->
        <div class="form-group">
            {!! Form::submit(__('crud.save'), ['class' => 'btn btn-danger btn-flat btn-lg']) !!}
            <a href="{{ route('lpoouts.index') }}" class="btn btn-outline-danger btn-flat btn-lg text-maroon">@lang('crud.cancel')</a>
        </div>
    </div>
</div>