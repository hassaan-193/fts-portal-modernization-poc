<!-- Date Time Field -->
<li class="callout callout-danger list-group-item mb-3 shadow">
    <b>@lang('models/receipts.fields.date_time')</b> <a class="float-right">{{ $receipt->date_time }}</a>
</li>

<!-- Payment Type Field -->
<li class="callout callout-danger list-group-item mb-3 shadow">
    <b>@lang('models/receipts.fields.payment_type')</b> <a class="float-right">{{ $receipt->transaction_payment_type->name }}</a>
</li>

@if($receipt->transaction_payment_type->name != 'Cash')
    <!-- Payment No Field -->
    <li class="callout callout-danger list-group-item mb-3 shadow">
        <b>@lang('models/receipts.fields.payment_no')</b> <a class="float-right">{{ $receipt->payment_no }}</a>
    </li>
@endif

@if($receipt->transaction_payment_type->name == 'Cheque')
    <!-- Clearance Date Field -->
    <li class="callout callout-danger list-group-item mb-3 shadow">
        <b>@lang('models/receipts.fields.clearance_date')</b> <a class="float-right">{{ $receipt->clearance_date }}</a>
    </li>
@endif

@if($receipt->transaction_payment_type->name == 'Credit Card')
    <!-- Bank Name Field -->
    <li class="callout callout-danger list-group-item mb-3 shadow">
        <b>@lang('models/receipts.fields.bank_name')</b> <a class="float-right">{{ $receipt->bank_name }}</a>
    </li>
@endif

<!-- Amount Field -->
<li class="callout callout-danger list-group-item mb-3 shadow">
    <b>@lang('models/receipts.fields.total')</b> <a class="float-right">{{ $receipt->total }}</a>
</li>


<!-- Account Id Field -->
<li class="callout callout-danger list-group-item mb-3 shadow">
    <b>@lang('models/receipts.fields.account_id')</b> <a class="float-right">{{ $receipt->account->name }}</a>
</li>

<!-- Note Field -->
<li class="callout callout-danger list-group-item mb-3 shadow">
    <b>@lang('models/receipts.fields.note')</b> <a class="float-right">{{ $receipt->note }}</a>
</li>

<!-- Status Field -->
<li class="callout callout-danger list-group-item mb-3 shadow">
    <b>@lang('models/receipts.fields.status')</b>
    <a class="float-right">
        @include('components.datatables_status', [
            'msg' => ($receipt->status) ? 'complete' : 'pending',
            'type' => ($receipt->status) ? 'success' : 'danger',
        ])
    </a>
</li>

