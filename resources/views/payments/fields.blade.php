<!-- Date Time Field -->
<div class="form-group col-sm-3">
    {!! Form::label('date_time', __('models/payments.fields.date_time').':') !!}
    <div class="input-group">
        <div class="input-group-prepend">
            <span class="input-group-text">
            <i class="far fa-calendar-alt"></i>
            </span>
        </div>
        {!! Form::text('date_time', null, ['class' => ($errors->has('date_time')) ? 'form-control is-invalid' : 'form-control' ,'id'=>'date_time']) !!}
        @if ($errors->has('date_time'))
            <span class="invalid-feedback">
                <strong>{{ $errors->first('date_time') }}</strong>
            </span>
        @endif
    </div>
</div>

<!-- Type Field -->
<div class="form-group col-sm-6">
    <label>Type</label><br>

    <div class="form-check form-check-inline">
        {!! Form::radio('type', 'vendor', true, ['id' => 'type_vendor', 'class' => 'form-check-input' . ($errors->has('type') ? ' is-invalid' : '')]) !!}
        <label class="form-check-label" for="type_vendor">Vendor Payment</label>
    </div>

    <div class="form-check form-check-inline">
        {!! Form::radio('type', 'general', null, ['id' => 'type_general', 'class' => 'form-check-input' . ($errors->has('type') ? ' is-invalid' : '')]) !!}
        <label class="form-check-label" for="type_general">Expense</label>
    </div>

    @if($errors->has('type'))
        <div class="invalid-feedback d-block">
            {{ $errors->first('type') }}
        </div>
    @endif
</div>

<div id="vendor_area" >
    <!-- Vendor Id Field -->
    {!! Form::label('vendor_id', __('models/payments.fields.vendor_id').':') !!}
    <div class="input-group input-group-sm">
        {!! Form::select('vendor_id', $vendorItems, null, ['class' => ($errors->has('vendor_id')) ? 'form-control is-invalid' : 'form-control' ,'id'=>'vendor_id' ,'disabled' => isset($payment) ? true : false]) !!}
        @if ($errors->has('vendor_id'))
            <span class="invalid-feedback">
                <strong>{{ $errors->first('vendor_id') }}</strong>
            </span>
        @endif
        <span class="input-group-append">
            @if(!isset($payment) )
                <button type="button" id="search" class="btn btn-info btn-flat">Go!</button>
            @endif
        </span>
    </div>

    <!-- Products Field -->
    @include('payments.payments_partials.invoice_detail' ,[
        'invoice' => isset($payment) ? $payment : null
    ])
</div>
<div class="form-group col-sm-6 expense_account_div" hidden>
    {!! Form::label('expense_account', __('models/payments.fields.expense_account').':') !!}
    {!! Form::select('expense_account', $accountItems, null, ['class' => ($errors->has('expense_account')) ? 'form-control is-invalid' : 'form-control' ,'id'=>'expense_account']) !!}
    @if ($errors->has('expense_account'))
        <span class="invalid-feedback">
            <strong>{{ $errors->first('expense_account') }}</strong>
        </span>
    @endif
</div>

<!-- Total Amount Field -->
<div class="form-group col-md-6">
    {!! Form::label('total_amount', __('models/payments.fields.total_amount').':') !!}
    {!! Form::text('total_amount', null, ['class' => ($errors->has('total_amount')) ? 'form-control is-invalid' : 'form-control', 'readonly' => true]) !!}
    @if ($errors->has('total_amount'))
        <span class="invalid-feedback">
            <strong>{{ $errors->first('total_amount') }}</strong>
        </span>
    @endif
</div>

<!-- VAT Field -->
<div class="form-group col-md-3 expense_account_div" hidden>
    {!! Form::label('vat', 'Include VAT:') !!}
    <div class="form-check mt-2">
        {!! Form::hidden('vat', 0) !!}
        <label class="form-check-label">
            {!! Form::checkbox('vat', '1', null, ['class' => 'form-check-input']) !!}
            Yes
        </label>
    </div>
</div>

<!-- Payment Type Field -->
<div class="form-group col-sm-6">
    {!! Form::label('payment_type', __('models/payments.fields.payment_type').':') !!}
    {!! Form::select('payment_type', $lookupItems, null, ['class' => ($errors->has('payment_type')) ? 'form-control is-invalid' : 'form-control' ,'id'=>'payment_type']) !!}
    @if ($errors->has('payment_type'))
        <span class="invalid-feedback">
            <strong>{{ $errors->first('payment_type') }}</strong>
        </span>
    @endif
</div>

<!-- Payment No Field -->
<div class="form-group col-sm-6" hidden id="div_payment_no">
    {!! Form::label('payment_no', __('models/payments.fields.payment_no').':') !!}
    {!! Form::text('payment_no', null, ['class' => ($errors->has('payment_no')) ? 'form-control is-invalid' : 'form-control']) !!}
    @if ($errors->has('payment_no'))
        <span class="invalid-feedback">
            <strong>{{ $errors->first('payment_no') }}</strong>
        </span>
    @endif
</div>

<!-- Clearance Date Field -->
<div class="form-group col-sm-6" hidden id="div_clearance_date">
    {!! Form::label('clearance_date', __('models/payments.fields.clearance_date').':') !!}
    <div class="input-group">
        <div class="input-group-prepend">
            <span class="input-group-text">
            <i class="far fa-calendar-alt"></i>
            </span>
        </div>
        {!! Form::text('clearance_date', null, ['class' => ($errors->has('clearance_date')) ? 'form-control is-invalid' : 'form-control' ,'id'=>'clearance_date', 'autocomplete' => "off"]) !!}
        @if ($errors->has('clearance_date'))
            <span class="invalid-feedback">
                <strong>{{ $errors->first('clearance_date') }}</strong>
            </span>
        @endif
    </div>
</div>

<!-- Bank Name Field -->
<div class="form-group col-sm-6" hidden id="div_bank_name">
    {!! Form::label('bank_name', __('models/payments.fields.bank_name').':') !!}
    {!! Form::text('bank_name', null, ['class' => ($errors->has('bank_name')) ? 'form-control is-invalid' : 'form-control']) !!}
    @if ($errors->has('bank_name'))
        <span class="invalid-feedback">
            <strong>{{ $errors->first('bank_name') }}</strong>
        </span>
    @endif
</div>

<!-- Account Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('account_id', __('models/payments.fields.account_id').':') !!}
    {!! Form::select('account_id', $accountItems, null, ['class' => ($errors->has('account_id')) ? 'form-control is-invalid' : 'form-control' ,'id'=>'account_id']) !!}
    @if ($errors->has('account_id'))
        <span class="invalid-feedback">
            <strong>{{ $errors->first('account_id') }}</strong>
        </span>
    @endif
</div>

<!-- Note Field -->
<div class="form-group col-sm-12 col-lg-12">
    {!! Form::label('note', __('models/payments.fields.note').':') !!}
    {!! Form::textarea('note', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit(__('crud.save'), ['class' => 'btn btn-danger btn-flat btn-lg']) !!}
    <a href="{{ route('payments.index') }}" class="btn btn-outline-danger btn-flat btn-lg text-maroon">@lang('crud.cancel')</a>
</div>

@section('scripts')
@parent
    <script src="{{ asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
    <script src="{{ asset('plugins/select2/js/select2.full.min.js')}}"></script>
    <script type="text/javascript">
        //date picker
        $('#date_time').daterangepicker({
            singleDatePicker: true,
            timePicker: true,
            locale: {
                format: 'YYYY-MM-DD HH:mm:ss'
            }
        })

        $('[name="type"]').on('change', function(e) {
            const selectedType = $('[name="type"]:checked').val();
            
            if (selectedType === 'general') {
                $('#vendor_area').attr('hidden',true)
                $('#total_amount').attr('readonly',false);
                $('.expense_account_div').attr('hidden',false)
            }else{
                $('#vendor_area').attr('hidden',false);
                $('#total_amount').attr('readonly',true);
                $('.expense_account_div').attr('hidden',true)
            }
        });
        // company
        $('#vendor_id').select2({
            theme: 'bootstrap4',
            placeholder: 'Select Vendor'
        })

        // payment type
        $('#payment_type').select2({
            theme: 'bootstrap4',
            placeholder: {
                id: '',
                text: 'None Selected'
            },
        }).on('change', function(e) {
            var data = $("#payment_type option:selected").text().trim();

            if(data == 'Cash'){
                $('#div_payment_no').attr('hidden',true)
                $('#div_clearance_date').attr('hidden',true)
                $('#div_bank_name').attr('hidden',true)

                // clear fields
                $('#payment_no').val('')
                $('#clearance_date').val('')
                $('#bank_name').val('')

            }else if(data == 'Cheque'){
                $('#div_payment_no').attr('hidden',false)
                $('#div_clearance_date').attr('hidden',false)
                $('#div_bank_name').attr('hidden',true)

                $('#bank_name').val('')
            }else{
                $('#div_payment_no').attr('hidden',false)
                $('#div_clearance_date').attr('hidden',true)
                $('#div_bank_name').attr('hidden',false)

                $('#clearance_date').val('')
            }
        });

        // clearance date
        $('#clearance_date').daterangepicker({
            singleDatePicker: true,
            timePicker: false,
            locale: {
                format: 'YYYY-MM-DD'
            }
        })
        // account
        $('#account_id').select2({
            theme: 'bootstrap4',
            placeholder: 'Select an option',
            allowClear: true
        });

        $('#expense_account').select2({
            theme: 'bootstrap4',
            placeholder: 'Select an account',
            allowClear: true
        })
        //trigger chnage for payment type
        $(function() {
            $('#payment_type').trigger("change");
            $('[name="type"]').trigger("change");
        });
    </script>

@endsection
