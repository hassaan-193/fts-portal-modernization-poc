<!-- Date Time Field -->
<li class="callout callout-danger list-group-item mb-3 shadow">
    <b>@lang('models/payments.fields.date_time')</b> <a class="float-right">{{ $payment->date_time }}</a>
</li>

<!-- Payment Type Field -->
<li class="callout callout-danger list-group-item mb-3 shadow">
    <b>@lang('models/payments.fields.payment_type')</b> <a class="float-right">{{ $payment->transaction_payment_type->name }}</a>
</li>

@if($payment->transaction_payment_type->name != 'Cash')
    <!-- Payment No Field -->
    <li class="callout callout-danger list-group-item mb-3 shadow">
        <b>@lang('models/payments.fields.payment_no')</b> <a class="float-right">{{ $payment->payment_no }}</a>
    </li>
@endif

@if($payment->transaction_payment_type->name == 'Cheque')
    <!-- Clearance Date Field -->
    <li class="callout callout-danger list-group-item mb-3 shadow">
        <b>@lang('models/payments.fields.clearance_date')</b> <a class="float-right">{{ $payment->clearance_date }}</a>
    </li>
@endif

@if($payment->transaction_payment_type->name == 'Credit Card')
    <!-- Bank Name Field -->
    <li class="callout callout-danger list-group-item mb-3 shadow">
        <b>@lang('models/payments.fields.bank_name')</b> <a class="float-right">{{ $payment->bank_name }}</a>
    </li>
@endif

<!-- Amount Field -->
<li class="callout callout-danger list-group-item mb-3 shadow">
    <b>@lang('models/payments.fields.total')</b> <a class="float-right">{{ number_format($payment->total,2) }}</a>
</li>


<!-- Account Id Field -->
<li class="callout callout-danger list-group-item mb-3 shadow">
    <b>@lang('models/payments.fields.account_id')</b> <a class="float-right">{{ $payment->account->name }}</a>
</li>

<!-- Note Field -->
<li class="callout callout-danger list-group-item mb-3 shadow">
    <b>@lang('models/payments.fields.note')</b> <a class="float-right">{{ $payment->note }}</a>
</li>

<!-- Status Field -->
<li class="callout callout-danger list-group-item mb-3 shadow">
    <b>@lang('models/payments.fields.status')</b>
    <a class="float-right">
        @include('components.datatables_status', [
            'msg' => ($payment->status) ? 'complete' : 'pending',
            'type' => ($payment->status) ? 'success' : 'danger',
        ])
    </a>
</li>

