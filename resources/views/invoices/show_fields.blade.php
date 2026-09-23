<div class="row">
    <div class="col-md-4 col-sm-6">
        <!-- Invoice No Field -->
        <li class="callout callout-danger list-group-item mb-3 shadow">
            <b>@lang('models/invoices.fields.invoice_no')</b> <a class="float-right">{{ $invoice->invoice_no }}</a>
        </li>
        <!-- Start Date Field -->
        <li class="callout callout-danger list-group-item mb-3 shadow">
            <b>@lang('models/invoices.fields.start_date')</b> <a class="float-right">{{ $invoice->start_date }}</a>
        </li>
        <!-- Payment Terms Field -->
        <li class="callout callout-danger list-group-item mb-3 shadow">
            <b>@lang('models/invoices.fields.payment_terms')</b> <a class="float-right">{{ $invoice->payment_terms }}</a>
        </li>
        <!-- Note1 Field -->
        <li class="callout callout-danger list-group-item mb-3 shadow">
            <b>@lang('models/invoices.fields.note1')</b> <a class="float-right">{{ $invoice->note1 }}</a>
        </li>
    </div>
    <div class="col-md-4 col-sm-6">

        <!-- Invoice Type Id Field -->
        <li class="callout callout-danger list-group-item mb-3 shadow">
            <b>@lang('models/invoices.fields.invoice_type_id')</b> <a class="float-right">{{ $invoice->invoice_type_id }}</a>
        </li>
        <!-- End Date Field -->
        <li class="callout callout-danger list-group-item mb-3 shadow">
            <b>@lang('models/invoices.fields.end_date')</b> <a class="float-right">{{ $invoice->end_date }}</a>
        </li>
        <!-- Tir No Field -->
        <li class="callout callout-danger list-group-item mb-3 shadow">
            <b>@lang('models/invoices.fields.tir_no')</b> <a class="float-right">{{ $invoice->tir_no }}</a>
        </li>
        <!-- Note2 Field -->
        <li class="callout callout-danger list-group-item mb-3 shadow">
            <b>@lang('models/invoices.fields.note2')</b> <a class="float-right">{{ $invoice->note2 }}</a>
        </li>
    </div>    
    <div class="col-md-4 col-sm-6">

        <!-- Quotation Id Field -->
        <li class="callout callout-danger list-group-item mb-3 shadow">
            <b>@lang('models/invoices.fields.quotation_id')</b> <a class="float-right">{{ $invoice->quotation_id }}</a>
        </li>
        <!-- Delivery Date Field -->
        <li class="callout callout-danger list-group-item mb-3 shadow">
            <b>@lang('models/invoices.fields.delivery_date')</b> <a class="float-right">{{ $invoice->delivery_date }}</a>
        </li>
        <!-- Amount In Word Field -->
        <li class="callout callout-danger list-group-item mb-3 shadow">
            <b>@lang('models/invoices.fields.amount_in_word')</b> <a class="float-right">{{ $invoice->amount_in_word }}</a>
        </li>
    </div>
</div>
