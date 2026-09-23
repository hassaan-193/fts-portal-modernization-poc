@extends('layouts.master')

<style type="text/css">
    .border-b{
        border-bottom:1px solid #000;
    }
    .border-bo{
        border:1px solid #000;
    }
    .table th, .table td {
        padding: 0.75rem;
        vertical-align: top;
        border-top: none !important;
    }
    .k-p{
        padding: 15px;
    }
</style>

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark text-weight-bold">@lang('crud.detail') @lang('models/receipts.singular')</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{!! route('receipts.index') !!}">@lang('models/receipts.singular')</a></li>
                        <li class="breadcrumb-item active">@lang('crud.detail')</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="invoice p-3 mb-3">
                        <div class="row no-print">
                            <div class="col-12" style="position: absolute;width: 100%;right: 15px;">
                                <button type="button" onclick="printWithExpert()" class="btn btn-warning float-right m-1"><i class="fas fa-print"></i> EXPERTS Print</button>
                                <button type="button" onclick="printWithFts()" class="btn btn-danger float-right m-1"><i class="fas fa-print"></i> FTS Print</button>
                            </div>
                          </div>
                          <div class="col-xs-12 text-center">
                            <img class="fts_img images" src="{{asset('dist/img/fts_latter_head.jpeg')}}" style="width:100%;margin:auto;" />
                            <img class="expert_img images" src="{{asset('dist/img/experts_letter_head.jpeg')}}" style="width:100%; margin:auto; display:none;" />
                        </div>
                        <div class="row justify-content-start mt-4">
                            <div class="col-md-4">
                                <table class="table expandable-table" border="0">
                                    <tr>
                                        <td><strong>TRN:</strong></td>
                                        <td class="trn_no">100317831400003</td>
                                    </tr>
                                    <tr>
                                        <td>Email</td>
                                        <td>info@example.com</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-4">
                                <h2 class="text-center text-weight-bold" style="text-transform: uppercase;">Receipt Voucher</h2>
                            </div>
                        </div>
                        <div class="row mt-4 k-p">
                            <div class="col-md-4">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label>Voucher No.</label>
                                    </div>
                                    <div class="col-md-6 border-b">
                                        {{$receipt->id}}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label>Date.</label>
                                    </div>
                                    <div class="col-md-6 border-b">
                                        {{$receipt->date_time}}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label>Payment Type.</label>
                                    </div>
                                    <div class="col-md-6 border-b">
                                        {{$receipt->transaction_payment_type->name}}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row k-p">
                            <div class="col-md-12 ">
                                <div class="row">
                                    <div class="col-md-2">
                                        <label>Invoices:</label>
                                    </div>
                                    <div class="col-md-10 border-b">
                                        @foreach ($invoices as $invoice)
                                         {{ $invoice->transactionable->invoice_no ." , " }}
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row k-p">
                            @if($receipt->account)
                                <div class="col-md-12 ">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label>Account:</label>
                                        </div>
                                        <div class="col-md-10 border-b">
                                            {{$receipt->account->type}}
                                        </div>
                                    </div>
                                </div>
                            @endif
                            @if($receipt->bank_name)
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label>Bank Name:</label>
                                        </div>
                                        <div class="col-md-8 border-b">
                                            {{$receipt->bank_name}}
                                        </div>
                                    </div>
                                </div>
                            @endif
                            @if($receipt->payment_no)
                                <div class="col-md-12 mt-4">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label>Bank/Cheque No:</label>
                                        </div>
                                        <div class="col-md-10 border-b">
                                            {{$receipt->payment_no}}
                                        </div>
                                    </div>
                                </div>
                            @endif
                            @if($receipt->clearance_date)
                                <div class="col-md-12 mt-4">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label>Bank/Cheque Clearance Date:</label>
                                        </div>
                                        <div class="col-md-10 border-b">
                                            {{$receipt->clearance_date}}
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <div class="col-md-12 mt-3">
                                <div class="row">
                                    <div class="col-md-2">
                                        <label>Note:</label>
                                    </div>
                                    <div class="col-md-10 border-b">
                                        {{$receipt->note}}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row k-p">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-2">
                                        <label>Paid By.</label>
                                    </div>
                                    <div class="col-md-10 border-b">
                                        {{ $receipt->transactionable ? $receipt->transactionable->quotation->company->name : ''}}
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-2">
                                        <label>The Sum Of Dirhams.</label>
                                    </div>
                                    <div class="col-md-10 border-b">
                                        {{number_format($invoices->sum('total'),2)}}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row k-p">
                            <div class="col-xs-12 text-center">
                                <img class="fts_img images" src="{{asset('dist/img/fts_letter_footer.jpg')}}" style="width:100%;margin:auto;" />
                                <img class="expert_img images" src="{{asset('dist/img/experts_letter_footer.jpeg')}}" style="width:100%; margin:auto; display:none;" />
                          </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
<script type="text/javascript">
    function printWithFts() {
        // change title
        $('#title').text("FIRE TECHNICAL SERVICES");
        $('.trn_no').text('100317831400003');

        toggleImages('fts_img');
        window.print();
    }
    function printWithExpert() {
        // change title
        $('#title').text("EIC");
        $('.trn_no').text('104116226200003');

        toggleImages('expert_img');
        window.print();
    }
    function toggleImages(className) {
        for (let el of document.querySelectorAll('.images')) el.style.display = 'none';
        for (let el of document.querySelectorAll(`.${className}`)) el.style.display = 'block';
    }
</script>
