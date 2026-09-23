<!-- Date Time Field -->
<div class="form-group col-sm-6">
    {!! Form::label('date_time', __('models/receipts.fields.date_time').':') !!}
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
    {!! Form::label('type', __('models/payments.fields.type').':') !!}
    {!! Form::select('type', ['company' => 'Company'], null, ['class' => ($errors->has('type')) ? 'form-control is-invalid' : 'form-control' ,'id'=>'type']) !!}
    @if ($errors->has('type'))
        <span class="invalid-feedback">
            <strong>{{ $errors->first('type') }}</strong>
        </span>
    @endif
</div>

{{-- from account --}}
<div class="form-group col-sm-6" hidden id="div_from_account">
    {!! Form::label('from_account', __('models/receipts.fields.from_account').':') !!}
    {!! Form::select('from_account', ['1' => "Owner's Personal A/c - Engr. Jalaa",'2' => 'Cash in RAKBANK Current A/c', '3' => 'Cash in ADCB Current A/c', '4' => "Owner's Current Period Profit"], null, ['class' => ($errors->has('from_account')) ? 'form-control is-invalid' : 'form-control' ,'id'=>'from_account']) !!}
    @if ($errors->has('from_account'))
        <span class="invalid-feedback">
            <strong>{{ $errors->first('from_account') }}</strong>
        </span>
    @endif
</div>

<div id="company_area" >
    <!-- Company Id Field -->
    {!! Form::label('company_id', __('models/receipts.fields.company_id').':') !!}
    <div class="input-group input-group-sm">
        {!! Form::select('company_id', $companyItems, null, ['class' => ($errors->has('company_id')) ? 'form-control is-invalid' : 'form-control' ,'id'=>'company_id' ,'disabled' => isset($receipt) ? true : false]) !!}
        @if ($errors->has('company_id'))
            <span class="invalid-feedback">
                <strong>{{ $errors->first('company_id') }}</strong>
            </span>
        @endif
        <span class="input-group-append">
            @if(!isset($receipt) )
                <button type="button" id="search" class="btn btn-info btn-flat">Go!</button>
            @endif
        </span>
    </div>

    <!-- Products Field -->
    @include('receipts.receipts_partials.invoice_detail' ,[
        'invoice' => isset($receipt) ? $receipt : null
    ])
</div>
<!-- Total Amount Field -->
<div class="form-group col-sm-6">
    {!! Form::label('total_amount', __('models/receipts.fields.total_amount').':') !!}
    {!! Form::text('total_amount', null, ['class' => ($errors->has('total_amount')) ? 'form-control is-invalid' : 'form-control', 'readonly' => true]) !!}
    @if ($errors->has('total_amount'))
        <span class="invalid-feedback">
            <strong>{{ $errors->first('total_amount') }}</strong>
        </span>
    @endif
</div>

<!-- Payment Type Field -->
<div class="form-group col-sm-6">
    {!! Form::label('payment_type', __('models/receipts.fields.payment_type').':') !!}
    {!! Form::select('payment_type', $lookupItems, null, ['class' => ($errors->has('payment_type')) ? 'form-control is-invalid' : 'form-control' ,'id'=>'payment_type']) !!}
    @if ($errors->has('payment_type'))
        <span class="invalid-feedback">
            <strong>{{ $errors->first('payment_type') }}</strong>
        </span>
    @endif
</div>

<!-- Payment No Field -->
<div class="form-group col-sm-6" hidden id="div_payment_no">
    {!! Form::label('payment_no', __('models/receipts.fields.payment_no').':') !!}
    {!! Form::text('payment_no', null, ['class' => ($errors->has('payment_no')) ? 'form-control is-invalid' : 'form-control']) !!}
    @if ($errors->has('payment_no'))
        <span class="invalid-feedback">
            <strong>{{ $errors->first('payment_no') }}</strong>
        </span>
    @endif
</div>

<!-- Clearance Date Field -->
<div class="form-group col-sm-6" hidden id="div_clearance_date">
    {!! Form::label('clearance_date', __('models/receipts.fields.clearance_date').':') !!}
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
    {!! Form::label('bank_name', __('models/receipts.fields.bank_name').':') !!}
    {!! Form::text('bank_name', null, ['class' => ($errors->has('bank_name')) ? 'form-control is-invalid' : 'form-control']) !!}
    @if ($errors->has('bank_name'))
        <span class="invalid-feedback">
            <strong>{{ $errors->first('bank_name') }}</strong>
        </span>
    @endif
</div>

<!-- Account Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('account_id', __('models/receipts.fields.account_id').':') !!}
    {!! Form::select('account_id', $accountItems, null, ['class' => ($errors->has('account_id')) ? 'form-control is-invalid' : 'form-control' ,'id'=>'account_id']) !!}
    @if ($errors->has('account_id'))
        <span class="invalid-feedback">
            <strong>{{ $errors->first('account_id') }}</strong>
        </span>
    @endif
</div>

<!-- Note Field -->
<div class="form-group col-sm-12 col-lg-12">
    {!! Form::label('note', __('models/receipts.fields.note').':') !!}
    {!! Form::textarea('note', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit(__('crud.save'), ['class' => 'btn btn-danger btn-flat btn-lg']) !!}
    <a href="{{ route('receipts.index') }}" class="btn btn-outline-danger btn-flat btn-lg text-maroon">@lang('crud.cancel')</a>
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

        // company
        $('#company_id').select2({
            theme: 'bootstrap4'
        });

        $('#type').on('change', function(e) {
            if($(this).val() == 'general'){
                $('#div_from_account').attr('hidden',false)
                $('#company_area').attr('hidden',true)
                $('#total_amount').attr('readonly',false)
            }else{
                $('#div_from_account').attr('hidden',true)
                $('#company_area').attr('hidden',false)
                $('#total_amount').attr('readonly',true)
            }
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
        })
        //trigger chnage for payment type
        $(function() {
            $('#payment_type').trigger("change");
        });
    </script>

@endsection
