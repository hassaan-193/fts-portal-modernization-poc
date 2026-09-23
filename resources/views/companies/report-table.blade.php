<div class="dataTables_filter mb-5"  >
    <div class="dt-buttons btn-group">
        <a class="btn btn-default buttons-collection buttons-export btn-sm no-corner" tabindex="0" aria-controls="dataTableBuilder" href="{{route('companies.export_company_report', ['id' => $company->id,'type'=>'excel'])}}">
            <span><i class="fa fa-download"></i> Excel</span>
        </a>
        <a class="btn btn-default buttons-collection buttons-export btn-sm no-corner" tabindex="0" aria-controls="dataTableBuilder" href="{{route('companies.export_company_report', ['id' => $company->id,'type'=>'csv'])}}">
            <span><i class="fa fa-download"></i> CSV</span>
        </a>
        <a class="btn btn-default buttons-collection buttons-export btn-sm no-corner" tabindex="0" aria-controls="dataTableBuilder" href="{{route('companies.export_company_report', ['id' => $company->id,'type'=>'pdf'])}}">
            <span><i class="fa fa-download"></i> PDF</span>
        </a>
    </div>
</div>
<table class="table table-hover table-bordered table-striped table-sm text-nowrap">
    <thead>
        <th scope="col">@lang('models/reports.company.quotation')</th>
        <th scope="col">@lang('models/reports.company.lpo')</th>
        <th scope="col">@lang('models/reports.company.ref_no')</th>
        <th scope="col" class="k-text-limit k-space-limit" >@lang('models/reports.company.type')</th>
        <th scope="col" class="k-text-limit k-space-limit k-text-limit-1">@lang('models/reports.company.invoice_no')</th>
        <th scope="col" class="k-text-limit k-space-limit">@lang('models/reports.company.due_date')</th>
        <th scope="col" class="total k-text-limit k-space-limit">@lang('models/reports.company.contract_value')</th>
        <th scope="col" class="total k-text-limit k-space-limit">@lang('models/reports.company.net')</th>
        <th scope="col" class="total k-text-limit k-space-limit">@lang('models/reports.company.vat')</th>
        <th scope="col" class="total k-text-limit k-space-limit">@lang('models/reports.company.cd_govt_fee')</th>
        <th scope="col" class="total k-text-limit k-space-limit">@lang('models/reports.company.total_amount')</th>
        <th scope="col" class="total k-text-limit k-space-limit">@lang('models/reports.company.payment_received')</th>
        <th scope="col" class="k-text-limit k-space-limit">@lang('models/reports.company.payment_type')</th>
        <th scope="col" class="total k-text-limit k-space-limit">@lang('models/reports.company.payment_date')</th>
        <th scope="col" class="k-text-limit k-space-limit">@lang('models/reports.company.chq_ref')</th>
        <th scope="col" class="k-text-limit k-space-limit">@lang('models/reports.company.chq_date')</th>
        <th scope="col" class="k-text-limit k-space-limit d-print-none">@lang('models/reports.company.note')</th>
    </thead>
    <tbody>
        @php
            $total_contract = 0;
            $total_gov_fee = 0;
            $total_vat = 0;
            $total_net = 0;
            $total_amount = 0;
            $total_received = 0;
        @endphp

        @foreach ($company->quotations as $quotation )
            @if ($quotation->status)
            @php $total_contract += $quotation->amount; @endphp

            <tr scope="row">
                <td >{{$quotation->name}}</td>
                <td >{{$quotation->lpoins->ref_no}}</td>
                <td >{{$quotation->ref_no}}</td>
                <td colspan="3" style="padding:0;">
                    <table class="table table-hover table-bordered table-striped table-sm text-nowrap" style="margin-bottom: 0rem;">
                        @php
                            $total=count($quotation->invoices);
                        @endphp
                        @foreach ($quotation->invoices as $invoice)
                            @php
                                $total_vat += $invoice->vat;
                                $total_gov_fee += $invoice->invoice_service_details->sum('amount');
                                $total_net += $invoice->amount - $invoice->invoice_service_details->sum('amount');
                                $total_amount += $invoice->total_amount;
                                $total_received = $invoice->transaction ? $total_received + $invoice->transaction->total : $total_received;
                            @endphp
                            <tr>
                                <td class="k-text-limit k-space-limit">{{$invoice->invoice_type->name}}</td>
                                <td class="k-text-limit k-space-limit k-text-limit-1">{{$invoice->invoice_no}}</td>
                                <td class="k-text-limit k-space-limit">{{$invoice->end_date}}</td>
                            </tr>
                        @endforeach
                    </table>
                </td>
                <td >{{$quotation->amount}}</td>
                <td colspan="10" style="padding:0;">
                    <table class="table table-hover table-bordered table-striped table-sm text-nowrap"  style="margin-bottom: 0rem;">
                        @foreach ($quotation->invoices as $invoice)
                            <tr>
                                <td class="k-text-limit k-space-limit">{{$invoice->amount - $invoice->invoice_service_details->sum('amount')}}</td>
                                <td class="k-text-limit k-space-limit">{{$invoice->vat}}</td>
                                <td class="k-text-limit k-space-limit">{{ $invoice->invoice_service_details->sum('amount') }}</td>
                                <td class="k-text-limit k-space-limit">{{$invoice->total_amount}}</td>

                                <td class="k-text-limit k-space-limit">{{ $invoice->transaction->total ?? ''}}</td>
                                <td class="k-text-limit k-space-limit">{{ $invoice->transaction->transaction_payment_type->name ?? ''}}</td>
                                <td class="k-text-limit k-space-limit">{{ $invoice->transaction->date_time ?? ''}}</td>
                                <td class="k-text-limit k-space-limit">
                                    @if($invoice->transaction && $invoice->transaction->transaction_payment_type->name == 'Cash')
                                        {{ "" }}
                                    @elseif($invoice->transaction && $invoice->transaction->transaction_payment_type->name == 'Cheque')
                                        {{ $invoice->transaction->payment_no }}
                                    @endif
                                </td>
                                <td class="k-text-limit k-space-limit">
                                    @if($invoice->transaction && $invoice->transaction->transaction_payment_type->name == 'Cheque')
                                        {{ $invoice->transaction->clearance_date }}
                                    @endif
                                </td>
                                <td class="k-text-limit k-space-limit">{{ $invoice->transaction->note ?? '' }}</td>
                            </tr>
                        @endforeach
                    </table>
                </td>
            </tr>
            @endif
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td>{{ $total_contract }}</td>
            <td>{{ $total_net }}</td>
            <td>{{ $total_vat }}</td>
            <td>{{ $total_gov_fee }}</td>
            <td>{{ $total_amount }}</td>
            <td>{{ $total_received }}</td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr class="expandable-body" data-expandable-table="collapsed">
            <td colspan="8"></td>
            <td>
                <strong>Total Contract Value</strong>
            </td>
            <td colspan="6">
                {{ $total_contract }}
            </td>
        </tr>
        <tr class="expandable-body" data-expandable-table="collapsed">
            <td colspan="8"></td>
            <td>
                <strong>Total Unbilled Contract Balance, Net Amount</strong>
            </td>
            <td colspan="6">
                {{ $total_contract - $total_net }}
            </td>
        </tr>
        <tr class="expandable-body" data-expandable-table="collapsed">
            <td colspan="8"></td>
            <td>
                <strong>Total Billed Amount</strong>
            </td>
            <td colspan="6">
                {{ $total_amount }}
            </td>
        </tr>
        <tr class="expandable-body" data-expandable-table="collapsed">
            <td colspan="8"></td>
            <td>
                <strong>Total Payment Received</strong>
            </td>
            <td colspan="6">
                {{ $total_received }}
            </td>
        </tr>
        <tr class="expandable-body" data-expandable-table="collapsed">
            <td colspan="8"></td>
            <td>
                <strong>Total Outstanding Amount</strong>
            </td>
            <td colspan="6">
                {{ $total_amount - $total_received }}
            </td>
        </tr>
    </tfoot>
</table>
<div class="row">
    <div class="col-md-6">

    </div>
</div>
{{-- @section('scripts') --}}
{{-- @parent

<script>
    $(document).ready(function() {
        $('.total').each(function(i) {
            // calculateColumn(i);
        });
    });

    function calculateColumn(index) {
        var total = 0;
        $('table tr').each(function() {
            var value = parseInt($('td', this).eq(index).text());
            if (!isNaN(value)) {
                total += value;
            }
        });
        $('table tfoot td').eq(index).text('Total: ' + total);
    }
</script>
@endsection --}}


<style>
    .k-text-limit{
        white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 205px;
    min-width: 205px;}
    /* .k-text-limit-1{
        white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 205px;
    min-width: 205px;} */
</style>
