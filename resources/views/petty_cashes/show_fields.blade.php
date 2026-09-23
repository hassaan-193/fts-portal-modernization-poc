<!-- Date Time Field -->
<li class="callout callout-danger list-group-item mb-3 shadow">
    <b>@lang('models/petty_cashes.fields.date_time')</b> <a class="float-right">{{ $pettyCash->date_time }}</a>
</li>

<!-- Account Id Field -->
<li class="callout callout-danger list-group-item mb-3 shadow">
    <b>@lang('models/petty_cashes.fields.account_id')</b> <a class="float-right">{{ $pettyCash->account->name }}</a>
</li>

<!-- User Id Field -->
<li class="callout callout-danger list-group-item mb-3 shadow">
    <b>@lang('models/petty_cashes.fields.user_id')</b> <a class="float-right text-primary" href="{{ route('employees.show', $pettyCash->user_id) }} ">{{ $pettyCash->user->name }}</a>
</li>


<!-- Type Field -->
<li class="callout callout-danger list-group-item mb-3 shadow">
    <b>@lang('models/petty_cashes.fields.type')</b> <a class="float-right">{{ $pettyCash->payment_type->name }}</a>
</li>

<!-- Amount Field -->
<li class="callout callout-danger list-group-item mb-3 shadow">
    <b>@lang('models/petty_cashes.fields.amount')</b> <a class="float-right">{{ $pettyCash->amount }}</a>
</li>


<!-- Vat Field -->
<li class="callout callout-danger list-group-item mb-3 shadow">
    <b>@lang('models/petty_cashes.fields.vat')</b> <a class="float-right">{{ $pettyCash->vat }}</a>
</li>

<!-- Total Amount Field -->
<li class="callout callout-danger list-group-item mb-3 shadow">
    <b>@lang('models/petty_cashes.fields.total_amount')</b> <a class="float-right">{{ $pettyCash->total_amount }}</a>
</li>

<!-- Description Field -->
<li class="callout callout-danger list-group-item mb-3 shadow">
    <b>@lang('models/petty_cashes.fields.description')</b> <a class="float-right">{{ $pettyCash->description }}</a>
</li>


