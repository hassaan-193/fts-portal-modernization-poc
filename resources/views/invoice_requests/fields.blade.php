<!-- Quotation Id LPO Out Id Field -->
<div class="row">
    <div class="col-md-6 col-sm-6" id="quotation" >
        {!! Form::label('quotation_id', __('models/lpoins.singular').':') !!}
        {!! Form::select('quotation_id', $lpoinItems, isset($lpoin->quotation) ? $lpoin->quotation->id : (isset($invoiceRequest) && $invoiceRequest->requestable_type == 'App\Models\Quotation' ?  $invoiceRequest->requestable_id : NULL), ['class' => ($errors->has('quotation_id')) ? 'form-control is-invalid' : 'form-control' ,'id'=>'quotation_id']) !!}
        @if ($errors->has('quotation_id'))
            <span class="invalid-feedback">
                <strong>{{ $errors->first('quotation_id') }}</strong>
            </span>
        @endif
    </div>
    <!-- Delivery Date Field -->
    <div class="col-md-3 col-sm-6">
        <div class="form-group">
            {!! Form::label('delivery_date', __('models/invoices.fields.delivery_date').':') !!}
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">
                    <i class="far fa-calendar-alt"></i>
                    </span>
                </div>
                {!! Form::text('delivery_date', null, ['class' => ($errors->has('delivery_date')) ? 'form-control is-invalid' : 'form-control' ,'id'=>'delivery_date']) !!}
                @if ($errors->has('delivery_date'))
                    <span class="invalid-feedback">
                        <strong>{{ $errors->first('delivery_date') }}</strong>
                    </span>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Info Cards Section -->
<div class="row" id="lpoin-info-cards" style="display: none;">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3 id="card-cv-value">0</h3>
                <p>Contract Value (CV)</p>
            </div>
            <div class="icon">
                <i class="fas fa-file-contract"></i>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3 id="card-remaining-value">0</h3>
                <p>Remaining Amount</p>
            </div>
            <div class="icon">
                <i class="fas fa-money-bill-wave"></i>
            </div>
        </div>
    </div>
    
    <div class="col-lg-2 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3 id="card-total-invoices">0</h3>
                <p>Total Invoices</p>
            </div>
            <div class="icon">
                <i class="fas fa-file-invoice"></i>
            </div>
        </div>
    </div>
    
    <div class="col-lg-2 col-6">
        <div class="small-box bg-primary">
            <div class="inner">
                <h3 id="card-completed-invoices">0</h3>
                <p>Completed</p>
            </div>
            <div class="icon">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
    </div>
    
    <div class="col-lg-2 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3 id="card-pending-invoices">0</h3>
                <p>Pending Invoices</p>
            </div>
            <div class="icon">
                <i class="fas fa-clock"></i>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Note Field -->
    <div class="col-md-12 col-sm-12">
        <div class="form-group ">
            {!! Form::label('note', __('models/invoice_requests.fields.note').':') !!}
            {!! Form::textarea('note', null, ['class' => ($errors->has('note')) ? 'form-control is-invalid' : 'form-control'] ) !!}
            @if ($errors->has('note'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('note') }}</strong>
                </span>
            @endif
        </div>
    </div>
</div>

<input type="hidden" name="temp_sum_dif" value="0">

@section('scripts')
@parent
    <script src="{{ asset('plugins/select2/js/select2.full.min.js')}}"></script>
    <script src="{{ asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
    <script>
        $(document).ready(function () {
            $('#quotation_id').trigger('change');
        });
        
        $('#quotation_id').select2({
            theme: 'bootstrap4',
            placeholder: "Select a Lpoin",
            allowClear: true
        });
        
        $('#lpoout_id').select2({
            theme: 'bootstrap4'
        });
        
        $('#delivery_date').daterangepicker({
            singleDatePicker: true,
            timePicker: false,
            locale: {
                format: 'YYYY-MM-DD'
            }
        });
        
        // Handle Lpoin selection change
        $('#quotation_id').on('change', function() {
            const quotationId = $(this).val();
            
            if (!quotationId) {
                $('#lpoin-info-cards').hide();
                return;
            }
            
            // Fetch quotation details
            $.ajax({
                url: '/invoice-requests/quotation-details/' + quotationId,
                type: 'GET',
                success: function(response) {
                    // Update card values
                    $('#card-cv-value').text(response.cv_amount || 0);
                    $('#card-remaining-value').text(response.remaining_amount || 0);
                    $('#card-total-invoices').text(response.total_invoices || 0);
                    $('#card-completed-invoices').text(response.completed_invoices || 0);
                    $('#card-pending-invoices').text(response.pending_invoices || 0);
                    
                    // Show cards
                    $('#lpoin-info-cards').fadeIn();
                },
                error: function(xhr) {
                    console.error('Error fetching quotation details:', xhr);
                    $('#lpoin-info-cards').hide();
                }
            });
        });
    </script>
@endsection

<!-- Products Field -->
@include('invoice_requests.product_detail' ,[
    'products' => isset($invoiceRequest->request_products) ? $invoiceRequest->request_products : null
])
{{-- Invoice History --}}
@include('invoice_requests.invoice_history')
{{-- Request History --}}
@include('invoice_requests.request_history')
{{-- Payment History --}}
@include('invoice_requests.payment_history')

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit(__('crud.save'), ['class' => 'btn btn-danger btn-flat btn-lg']) !!}
    <a href="{{ route('invoiceRequests.index') }}" class="btn btn-outline-danger btn-flat btn-lg text-maroon">@lang('crud.cancel')</a>
</div>