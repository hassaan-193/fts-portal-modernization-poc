@php
    // On edit the type is fixed, so the radios are locked to the stored value and
    // only that type's detail block is ever rendered.
    $isEdit       = isset($paymentBooking);
    $currentType  = $isEdit
        ? $paymentBooking->booking_type
        : old('booking_type', \App\Models\PaymentBooking::TYPE_CHEQUE);
    $isCash       = $currentType === \App\Models\PaymentBooking::TYPE_CASH;
    $chequeRef    = $isEdit ? $paymentBooking->reference_no : ($nextChequeReference ?? '');
    $cashRef      = $isEdit ? $paymentBooking->reference_no : ($nextCashReference ?? '');
@endphp

{{-- ================= Payment type ================= --}}
<div class="row mb-3">
    <div class="col-md-6 col-sm-12 mb-2">
        <label class="w-100 mb-0" for="type_cheque">
            <div class="card mb-0 shadow-sm booking-type-card {{ $isCash ? '' : 'border-maroon' }}" id="card_cheque">
                <div class="card-body py-3 d-flex align-items-center">
                    <span class="mr-3 text-maroon"><i class="fa fa-money-check fa-2x"></i></span>
                    <div class="flex-grow-1">
                        <strong class="d-block">@lang('models/payment_bookings.fields.booking_type') — Book Cheque</strong>
                        <small class="text-muted">Record a cheque payment with its bank and release schedule.</small>
                    </div>
                    <input type="radio" name="booking_type" id="type_cheque" class="ml-2"
                        value="{{ \App\Models\PaymentBooking::TYPE_CHEQUE }}"
                        {{ $isCash ? '' : 'checked' }} {{ $isEdit ? 'disabled' : '' }}>
                </div>
            </div>
        </label>
    </div>
    <div class="col-md-6 col-sm-12 mb-2">
        <label class="w-100 mb-0" for="type_cash">
            <div class="card mb-0 shadow-sm booking-type-card {{ $isCash ? 'border-maroon' : '' }}" id="card_cash">
                <div class="card-body py-3 d-flex align-items-center">
                    <span class="mr-3 text-success"><i class="fa fa-hand-holding-usd fa-2x"></i></span>
                    <div class="flex-grow-1">
                        <strong class="d-block">@lang('models/payment_bookings.fields.booking_type') — Book Cash</strong>
                        <small class="text-muted">Record a cash payment against a cash account.</small>
                    </div>
                    <input type="radio" name="booking_type" id="type_cash" class="ml-2"
                        value="{{ \App\Models\PaymentBooking::TYPE_CASH }}"
                        {{ $isCash ? 'checked' : '' }} {{ $isEdit ? 'disabled' : '' }}>
                </div>
            </div>
        </label>
    </div>
</div>

@if($isEdit)
    {{-- Disabled radios post nothing; the stored type is what the server must see. --}}
    {!! Form::hidden('booking_type', $currentType) !!}
@endif

{{-- ================= 01 Payment information ================= --}}
<div class="card card-outline card-maroon">
    <div class="card-header">
        <h3 class="card-title">
            <span class="badge badge-maroon mr-1">01</span>
            Payment information
        </h3>
        <div class="card-tools">
            <span class="badge badge-light border" id="reference_preview">
                {{ $isCash ? $cashRef : $chequeRef }}
            </span>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6 col-sm-12">
                <!-- Payee Field -->
                <div class="form-group">
                    <label for="payee">
                        <span id="label_payee">{{ $isCash ? __('models/payment_bookings.fields.paid_to') : __('models/payment_bookings.fields.payee') }}</span>
                        <span class="text-danger">*</span>
                    </label>
                    {!! Form::text('payee', null, [
                        'class' => ($errors->has('payee')) ? 'form-control is-invalid' : 'form-control',
                        'id' => 'payee',
                        'placeholder' => 'e.g. Gulf Safety Equipment LLC',
                    ]) !!}
                    @if ($errors->has('payee'))
                        <span class="invalid-feedback d-block"><strong>{{ $errors->first('payee') }}</strong></span>
                    @endif
                </div>
            </div>

            <div class="col-md-6 col-sm-12">
                <!-- Amount Field -->
                <div class="form-group">
                    <label for="amount">@lang('models/payment_bookings.fields.amount') <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend"><span class="input-group-text">AED</span></div>
                        {!! Form::number('amount', null, [
                            'class' => ($errors->has('amount')) ? 'form-control is-invalid' : 'form-control',
                            'id' => 'amount',
                            'step' => '0.01',
                            'min' => '0.01',
                            'placeholder' => '0.00',
                        ]) !!}
                        @if ($errors->has('amount'))
                            <span class="invalid-feedback d-block"><strong>{{ $errors->first('amount') }}</strong></span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-sm-12">
                <!-- Payment Against Field -->
                <div class="form-group">
                    <label for="payment_against">@lang('models/payment_bookings.fields.payment_against') <span class="text-danger">*</span></label>
                    {!! Form::text('payment_against', null, [
                        'class' => ($errors->has('payment_against')) ? 'form-control is-invalid' : 'form-control',
                        'id' => 'payment_against',
                        'placeholder' => 'e.g. Project expense',
                    ]) !!}
                    @if ($errors->has('payment_against'))
                        <span class="invalid-feedback d-block"><strong>{{ $errors->first('payment_against') }}</strong></span>
                    @endif
                </div>
            </div>

            <div class="col-md-6 col-sm-12">
                <!-- Project / Cost Centre Field -->
                <div class="form-group">
                    <label for="project_cost_centre">@lang('models/payment_bookings.fields.project_cost_centre') <span class="text-danger">*</span></label>
                    {!! Form::text('project_cost_centre', null, [
                        'class' => ($errors->has('project_cost_centre')) ? 'form-control is-invalid' : 'form-control',
                        'id' => 'project_cost_centre',
                        'placeholder' => 'e.g. PRJ-1042 · Marina Tower AMC',
                    ]) !!}
                    @if ($errors->has('project_cost_centre'))
                        <span class="invalid-feedback d-block"><strong>{{ $errors->first('project_cost_centre') }}</strong></span>
                    @endif
                </div>
            </div>

            <div class="col-md-6 col-sm-12">
                <!-- Purpose Field -->
                <div class="form-group">
                    <label for="purpose">@lang('models/payment_bookings.fields.purpose') <span class="text-danger">*</span></label>
                    {!! Form::textarea('purpose', null, [
                        'class' => ($errors->has('purpose')) ? 'form-control is-invalid' : 'form-control',
                        'id' => 'purpose',
                        'rows' => 2,
                        'placeholder' => 'e.g. Fire alarm panel and accessories',
                    ]) !!}
                    @if ($errors->has('purpose'))
                        <span class="invalid-feedback d-block"><strong>{{ $errors->first('purpose') }}</strong></span>
                    @endif
                </div>
            </div>

            <div class="col-md-6 col-sm-12">
                <!-- Booking Date Field -->
                <div class="form-group">
                    <label for="booking_date">@lang('models/payment_bookings.fields.booking_date') <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                        </div>
                        {!! Form::text('booking_date', $isEdit && $paymentBooking->booking_date ? $paymentBooking->booking_date->format('Y-m-d') : null, [
                            'class' => ($errors->has('booking_date')) ? 'form-control is-invalid' : 'form-control',
                            'id' => 'booking_date',
                            'autocomplete' => 'off',
                        ]) !!}
                    </div>
                    @if ($errors->has('booking_date'))
                        <span class="invalid-feedback d-block"><strong>{{ $errors->first('booking_date') }}</strong></span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ================= 02 Cheque details ================= --}}
<div class="card card-outline card-maroon" id="block_cheque" {{ $isCash ? 'hidden' : '' }}>
    <div class="card-header">
        <h3 class="card-title">
            <span class="badge badge-maroon mr-1">02</span>
            Cheque details
        </h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6 col-sm-12">
                <div class="form-group">
                    <label for="cheque_number">@lang('models/payment_bookings.fields.cheque_number') <span class="text-danger">*</span></label>
                    {!! Form::text('cheque_number', null, [
                        'class' => ($errors->has('cheque_number')) ? 'form-control is-invalid' : 'form-control',
                        'id' => 'cheque_number',
                        'placeholder' => 'e.g. 009847',
                    ]) !!}
                    @if ($errors->has('cheque_number'))
                        <span class="invalid-feedback d-block"><strong>{{ $errors->first('cheque_number') }}</strong></span>
                    @endif
                </div>
            </div>

            <div class="col-md-6 col-sm-12">
                <div class="form-group">
                    <label for="bank_account">@lang('models/payment_bookings.fields.bank_account') <span class="text-danger">*</span></label>
                    {!! Form::text('bank_account', null, [
                        'class' => ($errors->has('bank_account')) ? 'form-control is-invalid' : 'form-control',
                        'id' => 'bank_account',
                        'placeholder' => 'e.g. ADCB · **** 2841',
                    ]) !!}
                    @if ($errors->has('bank_account'))
                        <span class="invalid-feedback d-block"><strong>{{ $errors->first('bank_account') }}</strong></span>
                    @endif
                </div>
            </div>

            <div class="col-md-6 col-sm-12">
                <div class="form-group">
                    <label for="cheque_date">@lang('models/payment_bookings.fields.cheque_date') <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                        </div>
                        {!! Form::text('cheque_date', $isEdit && $paymentBooking->cheque_date ? $paymentBooking->cheque_date->format('Y-m-d') : null, [
                            'class' => ($errors->has('cheque_date')) ? 'form-control is-invalid' : 'form-control',
                            'id' => 'cheque_date',
                            'autocomplete' => 'off',
                        ]) !!}
                    </div>
                    @if ($errors->has('cheque_date'))
                        <span class="invalid-feedback d-block"><strong>{{ $errors->first('cheque_date') }}</strong></span>
                    @endif
                </div>
            </div>

            <div class="col-md-6 col-sm-12">
                <div class="form-group">
                    <label for="release_date">@lang('models/payment_bookings.fields.release_date') <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                        </div>
                        {!! Form::text('release_date', $isEdit && $paymentBooking->release_date ? $paymentBooking->release_date->format('Y-m-d') : null, [
                            'class' => ($errors->has('release_date')) ? 'form-control is-invalid' : 'form-control',
                            'id' => 'release_date',
                            'autocomplete' => 'off',
                        ]) !!}
                    </div>
                    @if ($errors->has('release_date'))
                        <span class="invalid-feedback d-block"><strong>{{ $errors->first('release_date') }}</strong></span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ================= 02 Cash details ================= --}}
<div class="card card-outline card-maroon" id="block_cash" {{ $isCash ? '' : 'hidden' }}>
    <div class="card-header">
        <h3 class="card-title">
            <span class="badge badge-maroon mr-1">02</span>
            Cash details
        </h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6 col-sm-12">
                <div class="form-group">
                    <label for="cash_account">@lang('models/payment_bookings.fields.cash_account') <span class="text-danger">*</span></label>
                    {!! Form::text('cash_account', null, [
                        'class' => ($errors->has('cash_account')) ? 'form-control is-invalid' : 'form-control',
                        'id' => 'cash_account',
                        'placeholder' => 'e.g. Petty Cash · RAK Office',
                    ]) !!}
                    @if ($errors->has('cash_account'))
                        <span class="invalid-feedback d-block"><strong>{{ $errors->first('cash_account') }}</strong></span>
                    @endif
                </div>
            </div>

            <div class="col-md-6 col-sm-12">
                <div class="form-group">
                    <label for="payment_date">@lang('models/payment_bookings.fields.payment_date') <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                        </div>
                        {!! Form::text('payment_date', $isEdit && $paymentBooking->payment_date ? $paymentBooking->payment_date->format('Y-m-d') : null, [
                            'class' => ($errors->has('payment_date')) ? 'form-control is-invalid' : 'form-control',
                            'id' => 'payment_date',
                            'autocomplete' => 'off',
                        ]) !!}
                    </div>
                    @if ($errors->has('payment_date'))
                        <span class="invalid-feedback d-block"><strong>{{ $errors->first('payment_date') }}</strong></span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@section('css')
@parent
    <link rel="stylesheet" href="{{ asset('plugins/daterangepicker/daterangepicker.css')}}">
    <style>
        .booking-type-card { cursor: pointer; transition: border-color .15s ease-in-out; }
        .booking-type-card.border-maroon { border: 2px solid #a71d2a; }
        .badge-maroon { background-color: #a71d2a; color: #fff; }
    </style>
@endsection

@section('scripts')
@parent
<script src="{{ asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
<script>
$(function () {
    ['#booking_date', '#cheque_date', '#release_date', '#payment_date'].forEach(function (selector) {
        $(selector).daterangepicker({
            singleDatePicker: true,
            autoUpdateInput: false,
            locale: { format: 'YYYY-MM-DD' }
        }).on('apply.daterangepicker', function (ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD'));
        });
    });

    // References are only previewed here — the real one is generated on save, so
    // two people booking at the same time can never collide on a number.
    var references = {
        cheque: @json($chequeRef),
        cash: @json($cashRef)
    };

    var labels = {
        cheque: @json(__('models/payment_bookings.fields.payee')),
        cash: @json(__('models/payment_bookings.fields.paid_to'))
    };

    function applyType(type) {
        var isCash = type === 'cash';

        $('#block_cheque').attr('hidden', isCash);
        $('#block_cash').attr('hidden', !isCash);
        $('#card_cheque').toggleClass('border-maroon', !isCash);
        $('#card_cash').toggleClass('border-maroon', isCash);
        $('#label_payee').text(isCash ? labels.cash : labels.cheque);
        $('#reference_preview').text(isCash ? references.cash : references.cheque);

        // Only the active block's inputs are required, so the browser never blocks
        // submission on a field belonging to the other booking type.
        $('#block_cheque :input').prop('required', !isCash);
        $('#block_cash :input').prop('required', isCash);
    }

    $('input[name="booking_type"]').on('change', function () {
        applyType($(this).val());
    });

    applyType($('input[name="booking_type"]:checked').val() || 'cheque');
});
</script>
@endsection

<div class="row">
    <div class="col-sm-12">
        <div class="form-group">
            {!! Form::submit(__('crud.save'), ['class' => 'btn btn-danger btn-flat btn-lg']) !!}
            @unless(isset($paymentBooking))
                {!! Form::submit('Save as draft', ['name' => 'save_draft', 'class' => 'btn btn-outline-secondary btn-flat btn-lg ml-1']) !!}
            @endunless
            <a href="{{ route('payment-bookings.index') }}" class="btn btn-outline-danger btn-flat btn-lg text-maroon">@lang('crud.cancel')</a>
        </div>
    </div>
</div>
