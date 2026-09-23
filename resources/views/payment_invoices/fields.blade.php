<!-- Invoice For Request -->
<input type="hidden" name="invoice_request_id" value=" @if(isset($paymentInvoices)) {{ $paymentInvoices->invoice_request_id }}  @elseif ( isset($request)) {{ $request }}  @else  null  @endif" />

<!-- Invoice No Field -->
<div class="row">
    <div class="col-md-3 col-sm-6">
    <!-- Date Field -->
        <div class="form-group">
            {!! Form::label('invoice_no', __('models/payment_invoices.fields.invoice_no').':') !!}
            {!! Form::text('invoice_no', null, ['class' => ($errors->has('invoice_no')) ? 'form-control is-invalid' : 'form-control']) !!}
            @if ($errors->has('invoice_no'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('invoice_no') }}</strong>
                </span>
            @endif
        </div>
    </div>
    <!-- Type Field -->
    <div class="form-group col-sm-2">
        {!! Form::label('type', __('models/payment_invoices.fields.type').':') !!}
        {!! Form::select('type', ['LpoOut' => 'Lpoout','General' => 'General'], isset($lpoout) ? 'LpoOut' : (isset($paymentInvoices) ? $paymentInvoices->type : null), ['class' => ($errors->has('type')) ? 'form-control is-invalid' : 'form-control' ,'id'=>'type']) !!}
        @if ($errors->has('type'))
            <span class="invalid-feedback">
                <strong>{{ $errors->first('type') }}</strong>
            </span>
        @endif
    </div>
    <!-- Lpoout Id Field -->
    <div class="col-md-3 col-sm-6" id="lpo-div">
        <div class="form-group">
            {!! Form::label('lpoout_id', __('models/payment_invoices.fields.lpoout_id').':') !!}
            {!! Form::select('lpoout_id', $lpooutItems, isset($lpoout) ? $lpoout->id : (isset($paymentInvoices) ? $paymentInvoices->lpoout_id : null), ['class' => ($errors->has('lpoout_id')) ? 'form-control is-invalid' : 'form-control' ,'id'=>'lpoout_id']) !!}
            @if ($errors->has('lpoout_id'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('lpoout_id') }}</strong>
                </span>
            @endif
        </div>
    </div>
    <!-- Vendor Id Field -->
    <div class="col-md-3 col-sm-6" id="vendor-div">
        <div class="form-group">
            {!! Form::label('vendor_id', __('models/payment_invoices.fields.vendor_id').':') !!}
            {!! Form::select('vendor_id', $vendorItems, null, ['class' => ($errors->has('vendor_id')) ? 'form-control is-invalid' : 'form-control' ,'id'=>'vendor_id']) !!}
            @if ($errors->has('vendor_id'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('vendor_id') }}</strong>
                </span>
            @endif
        </div>
    </div>
    <!-- Project Id Field -->
    <div class="col-md-3 col-sm-6" id="project-div">
        <div class="form-group">
            {!! Form::label('project_id', __('models/payment_invoices.fields.project_id').':') !!}
            {!! Form::select('project_id', $projectItems, null, ['class' => ($errors->has('project_id')) ? 'form-control is-invalid' : 'form-control' ,'id'=>'project_id']) !!}
            @if ($errors->has('project_id'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('project_id') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <!-- Start Date Field -->
    <div class="col-md-2 col-sm-6">
        <div class="form-group">
            {!! Form::label('start_date', __('models/payment_invoices.fields.start_date').':') !!}
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">
                    <i class="far fa-calendar-alt"></i>
                    </span>
                </div>
                {!! Form::text('start_date', null, ['class' => ($errors->has('start_date')) ? 'form-control is-invalid' : 'form-control' ,'id'=>'start_date']) !!}
                @if ($errors->has('start_date'))
                    <span class="invalid-feedback">
                        <strong>{{ $errors->first('start_date') }}</strong>
                    </span>
                @endif
            </div>
        </div>
    </div>
    <!-- End Date Field -->
    <div class="col-md-2 col-sm-6">
        <div class="form-group">
            {!! Form::label('end_date', __('models/payment_invoices.fields.end_date').':') !!}
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">
                    <i class="far fa-calendar-alt"></i>
                    </span>
                </div>
                {!! Form::text('end_date', null, ['class' => ($errors->has('end_date')) ? 'form-control is-invalid' : 'form-control' ,'id'=>'end_date']) !!}
                @if ($errors->has('end_date'))
                    <span class="invalid-feedback">
                        <strong>{{ $errors->first('end_date') }}</strong>
                    </span>
                @endif
            </div>
        </div>
    </div>

    <!-- Amount Field -->
    <div class="col-md-3 col-sm-6">
        <div class="form-group">
            {!! Form::label('amount', __('models/payment_invoices.fields.amount').':') !!}
            {!! Form::text('amount', null, ['class' => ($errors->has('amount')) ? 'form-control is-invalid' : 'form-control']) !!}
            @if ($errors->has('amount'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('amount') }}</strong>
                </span>
            @endif
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

</div>


<!-- Note Field -->
<div class="form-group col-sm-12 col-lg-12">
    {!! Form::label('note', __('models/payment_invoices.fields.note').':') !!}
    {!! Form::textarea('note', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit(__('crud.save'), ['class' => 'btn btn-danger btn-flat btn-lg']) !!}
    <a href="{{ route('paymentInvoices.index') }}" class="btn btn-outline-danger btn-flat btn-lg text-maroon">@lang('crud.cancel')</a>
</div>

@section('scripts')
@parent
<script src="{{ asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
<script src="{{ asset('plugins/select2/js/select2.full.min.js')}}"></script>
<script>
    $(document).ready(function() {
        $('#type').trigger('change');
    });

    $('#lpoout_id').select2({
        theme: 'bootstrap4',
        placeholder: 'Select a lpoout'
    });
    $('#vendor_id').select2({
        theme: 'bootstrap4',
        placeholder: 'Select a vendor'
    });
    $('#project_id').select2({
        theme: 'bootstrap4',
        placeholder: 'Select a project'
    });
    $('#start_date').daterangepicker({
            singleDatePicker: true,
            timePicker: false,
            locale: {
                format: 'YYYY-MM-DD'
            }
        })
    $('#end_date').daterangepicker({
            singleDatePicker: true,
            timePicker: false,
            locale: {
                format: 'YYYY-MM-DD'
            }
        })
    $('#type').on('change', function(e) {

        if($(this).val() == 'General'){
            $('#lpo-div').attr('hidden',true);
            $('#vendor-div').attr('hidden',false);
            $('#project-div').attr('hidden',false);
        }else{
            $('#lpo-div').attr('hidden',false)
            $('#vendor-div').attr('hidden',true);
            $('#project-div').attr('hidden',true);
        }
    })
</script>
@endsection
