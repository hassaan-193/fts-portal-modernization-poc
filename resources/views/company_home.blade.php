@extends('layouts.company_layouts.master')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark text-weight-bold">Dashboard</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item active">Dashboard</li>
              </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="col-md-12">
                    <div class="card card-primary card-outline">
                        <div class="card-body">
                            <p class="text-center"> <b>Company Detail</b></p>
                            <ul class="list-group1 list-group-unbordered1 mb-3 row" style="padding-left:0px;">
                                <li class="col-md-4 list-group-item">
                                    <b>Name</b> <a class="float-right">{{ Auth::guard('company')->user()->name }}</a>
                                </li>
                                <li class="col-md-4 list-group-item">
                                    <b>Email</b> <a class="float-right">{{ Auth::guard('company')->user()->email }}</a>
                                </li>
                                <li class="col-md-4 list-group-item">
                                    <b>Contact #</b> <a class="float-right">{{ Auth::guard('company')->user()->contact_no }}</a>
                                </li>
                                <li class="col-md-4 list-group-item">
                                    <b>Contact Person</b> <a class="float-right">{{ Auth::guard('company')->user()->contact_person }}</a>
                                </li>
                                <li class="col-md-8 list-group-item">
                                    <b>Location</b> <a class="float-right">{{ Auth::guard('company')->user()->location }}</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

            </div>
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card card-primary card-outline">
                            <div class="card-body">
                                <p class="text-center"> <b>Quotations</b></p>
                                {!! $companyQuotationDataTable->html()->table(['width' => '100%', 'class' => 'table table-hover table-striped table-sm table-bordered','id' => 'companyQuotationDataTable']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card card-primary card-outline">
                            <div class="card-body">
                                <p class="text-center"> <b>Invoices</b></p>
                                {!! $companyInvoiceDataTable->html()->table(['width' => '100%', 'class' => 'table table-hover table-striped table-sm table-bordered','id' => 'companyInvoiceDataTable']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card card-primary card-outline">
                            <div class="card-body">
                                <p class="text-center"> <b>Lpoins</b></p>
                                {!! $companyLpoinDataTable->html()->table(['width' => '100%', 'class' => 'table table-hover table-striped table-sm table-bordered','id' => 'companyLpoinDataTable']) !!}
                            </div>
                        </div>
                    </div>
                    {{-- <div class="col-md-6">
                        <div class="card card-primary card-outline">
                            <div class="card-body">
                                <p class="text-center"> <b>Receipt Vouchers</b></p>

                            </div>
                        </div>
                    </div> --}}
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@section('scripts')
@parent
    @include('layouts.datatables_js')
    {!! $companyQuotationDataTable->html()->scripts() !!}
    {!! $companyInvoiceDataTable->html()->scripts() !!}
    {!! $companyLpoinDataTable->html()->scripts() !!}
@endsection
<style>
    .dataTables_wrapper {
        max-width: 600px;
        margin: 0 auto;
        overflow-x: auto;
    }
    #dtHorizontalExample th, td {
        white-space: nowrap;
    }
    div.dataTables_wrapper { min-height: 630px; }
    div.fg-toolbar.ui-toolbar.ui-corner-tl { position: inherit; }
    div.fg-toolbar.ui-toolbar.ui-corner-bl { position: absolute; bottom: 0; width: 100% }
    div.dataTables_paginate { position: relative; float: right;}

</style>
<script>
    $(document).ready(function () {
        $('#clientInvoiceDataTable_wrapper').DataTable({
            "scrollX": true
        });
    $('.dataTables_length').addClass('bs-select');
    });
</script>
