<!-- Invoice For Request -->
<input type="hidden" name="invoice_request_id" value="{{ isset($invoice) ? $invoice->invoice_request_id : $invoiceRequest->id }}" />

<div class="row">
    <!-- Invoice No Field -->
     <div class="col-md-3 col-sm-6">
        <div class="form-group">
            {!! Form::label('invoice_no', __('models/invoices.fields.invoice_no').':') !!}
            {!! Form::text('invoice_no', null, ['class' => ($errors->has('invoice_no')) ? 'form-control is-invalid' : 'form-control']) !!}
            @if ($errors->has('invoice_no'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('invoice_no') }}</strong>
                </span>
            @endif
        </div>
    </div>
    <!-- Invoice Type Id Field -->
     <div class="col-md-3 col-sm-6">
        <div class="form-group">
            {!! Form::label('invoice_type_id', __('models/invoices.fields.invoice_type_id').':') !!}
            {!! Form::select('invoice_type_id', $invoice_typeItems, null, ['class' => ($errors->has('invoice_type_id')) ? 'form-control is-invalid' : 'form-control' ,'id'=>'invoice_type_id']) !!}
            @if ($errors->has('invoice_type_id'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('invoice_type_id') }}</strong>
                </span>
            @endif
        </div>
    </div>
    <!-- Start Date Field -->
     <div class="col-md-3 col-sm-6">
        <div class="form-group">
            {!! Form::label('start_date', __('models/invoices.fields.start_date').':') !!}
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
    <div class="col-md-3 col-sm-6">
        <div class="form-group">
            {!! Form::label('end_date', __('models/invoices.fields.end_date').':') !!}
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
    <!-- Delivery Date Field -->
    <div class="col-md-2 col-sm-6">
        <div class="form-group">
            {!! Form::label('delivery_date', __('models/invoices.fields.delivery_date').':') !!}
            {!! Form::text('delivery_date', isset($invoice) ? $invoice->delivery_date : $invoiceRequest->delivery_date , ['class' => 'form-control', 'readonly'=> 'readonly' ]) !!}
        </div>
    </div>
    <!-- Letter Head Field -->
    {{-- <div class="col-md-3 col-sm-6">
        <div class="form-group">
            <div class="custom-file mt-4">
                {!! Form::file('file', ['class' => 'custom-file-input','id'=>'file']) !!}
                {!! Form::label('file', 'Letter Head Image', ['class' => 'custom-file-label']) !!}
            </div>
        </div>
    </div> --}}
     <!-- Currency Field -->
     <div class="col-md-1 col-sm-6">
        <div class="form-group">
            {!! Form::label('currency', __('models/invoices.fields.currency').':') !!}
            {!! Form::select('currency',config('enum.currency'), 'AED', ['class' => ($errors->has('currency')) ? 'form-control is-invalid' : 'form-control' ,'id'=>'currency']) !!}
            @if ($errors->has('currency'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('currency') }}</strong>
                </span>
            @endif
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="form-group pt-1">
             <div class="custom-file mt-4">
                 {!! Form::label('vat', __('models/invoices.fields.vat').':') !!}
                 <label class="checkbox-inline">
                     {!! Form::hidden('vat', 0) !!}
                     {!! Form::checkbox('vat', '1', (old('vat') === '1') ? true : ((isset($invoice) && !$invoice->vat) ? false : true)) !!}
                </label>
             </div>
         </div>
     </div>
</div>

<div class="row">
    <!-- Quotation Id Field -->
    <div class="col-md-3 col-sm-6">
        <div class="form-group">
            {!! Form::label('quotation_id', __('models/invoices.fields.quotation_id').':') !!}
            {!! Form::text('quotation', isset($invoice) ? $invoice->quotation->name : $invoiceRequest->requestable->name , ['class' => 'form-control', 'readonly'=> 'readonly' ]) !!}
            <input type="hidden" name="quotation_id" value="{{ isset($invoice) ? $invoice->quotation_id : $invoiceRequest->requestable_id }}" />
        </div>
    </div>
    <!-- Lpoin Id Field -->
    <div class="col-md-3 col-sm-6">
        <div class="form-group">
            {!! Form::label('lpoin_id', __('models/invoices.fields.lpoin_id').':') !!}
            {!! Form::text('quotation', isset($invoice) ? $invoice->quotation->lpoins->ref_no : $invoiceRequest->requestable->lpoins->ref_no , ['class' => 'form-control', 'readonly'=> 'readonly' ]) !!}
        </div>
    </div>
    <!-- Payment Terms Field -->
     <div class="col-md-6 col-sm-6">
        <div class="form-group">
            {!! Form::label('payment_terms', __('models/invoices.fields.payment_terms').':') !!}
            {!! Form::text('payment_terms', isset($invoice) ? $invoice->payment_terms : $invoiceRequest->requestable->lpoins->payment_terms , ['class' => 'form-control', 'readonly'=> 'readonly' ]) !!}
        </div>
    </div>
</div>

<div class="row">
    <!-- Amount In Word Field -->
    <div class="col-sm-6">
        <div class="form-group">
            {!! Form::label('amount_in_word', __('models/invoices.fields.amount_in_word').':') !!}
            {!! Form::text('amount_in_word', null, ['class' => ($errors->has('amount_in_word')) ? 'form-control is-invalid' : 'form-control']) !!}
            @if ($errors->has('amount_in_word'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('amount_in_word') }}</strong>
                </span>
            @endif
        </div>
    </div>
    <!-- Invoice Bank Field -->
    <div class="col-sm-6">
        <div class="form-group">
            {!! Form::label('invoice_bank_id', __('models/invoices.fields.invoice_bank_id').':') !!}
            {!! Form::select('invoice_bank_id', $invoiceBanks, null, ['class' => ($errors->has('invoice_bank_id')) ? 'form-control is-invalid' : 'form-control' ,'id'=>'invoice_bank_id']) !!}
            @if ($errors->has('invoice_bank_id'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('invoice_bank_id') }}</strong>
                </span>
            @endif
        </div>
    </div>
    @section('scripts')
        @parent
            <script src="{{ asset('plugins/select2/js/select2.full.min.js')}}"></script>
            <script>
                $('#invoice_bank_id').select2({
                    theme: 'bootstrap4'
                })
            </script>
    @endsection
</div>

<div class="row">
    <!-- Note1 Field -->
    <div class="col-md-6 col-sm-12">
        <div class="form-group">
            {!! Form::label('note1', __('models/invoices.fields.note1').':') !!}
            {!! Form::textarea('note1',
            '<ul>
                <li>Payment should be issued immediately upon receipt of this invoice.</li>
                <li>1% of the amount OR AED500 (whichever is higher) will be charged as a fine on the returned cheque.</li>
            </ul>
            <strong>This document has been approved electronically, hence no signature or stamp is required.</strong>'
            , ['class' => 'form-control summer-note']) !!}
        </div>
    </div>
    @section('scripts')
        @parent
        <script src=" {{ asset('plugins/summernote/summernote-bs4.min.js')}}"></script>
        <script>
            $(function () {
                    $('.summer-note').summernote({
                        height: 250
                    })
                })
        </script>
    @endsection

    <!-- Note2 Field -->
    <div class="col-md-6 col-sm-12">
        <div class="form-group">
            {!! Form::label('note2', __('models/invoices.fields.note2').':') !!}
            {!! Form::textarea('note2',
            '<ul><li>Bank transfer or payment in cash/cheque to be delivered to FTS RAK branch office, payable to:</li></ul>',
            ['class' => 'form-control summer-note']) !!}
        </div>
    </div>
</div>

<!-- Products Field -->
@include('invoices.invoices_partials.product_detail' ,[
    'products' => isset($invoiceRequest->request_products) ? $invoiceRequest->request_products : (isset($invoice) ? $invoice->invoice_product_details : null)
])

<!-- Services Field -->
@include('invoices.invoices_partials.service_detail',[
    'invoice' => isset($invoice) ? $invoice : null
])

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit(__('crud.save'), ['class' => 'btn btn-danger btn-flat btn-lg']) !!}
    <a href="{{ route('invoices.index') }}" class="btn btn-outline-danger btn-flat btn-lg text-maroon">@lang('crud.cancel')</a>
</div>

@section('scripts')
@parent
    <script src="{{ asset('plugins/select2/js/select2.full.min.js')}}"></script>
    <script src="{{ asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
    <script>
        $('#invoice_type_id').select2({
            theme: 'bootstrap4'
        })
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
    </script>
@endsection
