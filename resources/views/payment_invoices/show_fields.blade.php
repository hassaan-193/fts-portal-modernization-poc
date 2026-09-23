<!-- Invoice No Field -->
<li class="callout callout-danger list-group-item mb-3 shadow">
    <b>@lang('models/payment_invoices.fields.invoice_no')</b> <br><a class="text-left">{{ $paymentInvoice->invoice_no }}</a>
</li>

@if($paymentInvoice->lpoout_id)
    <!-- Lpoout Id Field -->
    <li class="callout callout-danger list-group-item mb-3 shadow">
        <b>@lang('models/payment_invoices.fields.lpoout_id')</b> <br><a class="text-left text-primary" href="{{ route('lpoouts.show',$paymentInvoice->lpoout_id) }}">{{ $paymentInvoice->lpoout->name }}</a>
    </li>
@endif

@if($paymentInvoice->vendor_id)
    <!-- Lpoout Id Field -->
    <li class="callout callout-danger list-group-item mb-3 shadow">
        <b>@lang('models/payment_invoices.fields.vendor_id')</b> <br><a class="text-left text-primary" href="{{ route('vendors.show',$paymentInvoice->vendor_id) }}">{{ $paymentInvoice->vendor->name }}</a>
    </li>
@endif

@if($paymentInvoice->project_id)
    <!-- Lpoout Id Field -->
    <li class="callout callout-danger list-group-item mb-3 shadow">
        <b>@lang('models/payment_invoices.fields.project_id')</b> <br><a class="text-left text-primary" href="{{ route('projects.show',$paymentInvoice->project_id) }}">{{ $paymentInvoice->project->quotation->name }}</a>
    </li>
@endif

<!-- Amount Field -->
<li class="callout callout-danger list-group-item mb-3 shadow">
    <b>@lang('models/payment_invoices.fields.amount')</b> <br><a class="text-left">{{ $paymentInvoice->amount }}</a>
</li>

<!-- Start Date Field -->
<li class="callout callout-danger list-group-item mb-3 shadow">
    <b>@lang('models/payment_invoices.fields.start_date')</b> <br><a class="text-left">{{ $paymentInvoice->start_date }}</a>
</li>


<!-- End Date Field -->
<li class="callout callout-danger list-group-item mb-3 shadow">
    <b>@lang('models/payment_invoices.fields.end_date')</b> <br><a class="text-left">{{ $paymentInvoice->end_date }}</a>
</li>


<!-- Note Field -->
<li class="callout callout-danger list-group-item mb-3 shadow">
    <b>@lang('models/payment_invoices.fields.note')</b> <br><a class="text-left">{{ $paymentInvoice->note }}</a>
</li>


