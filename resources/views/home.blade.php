@php
    $number=1;
@endphp
@extends('layouts.master')

@section('content')
<!-- Content Header (Page header) -->
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
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-lg-2 col-6">
                <!-- small box -->
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{$companies}}</h3>

                        <p>Companies</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-bag"></i>
                    </div>
                    <a href="{{ route('companies.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-2 col-6">
                <!-- small box -->
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{$projects}}</h3>
                        <p>Projects</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-stats-bars"></i>
                    </div>
                    <a href="{{ route('projects.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-2 col-6">
                <!-- small box -->
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{$users}}</h3>

                        <p>User Registrations</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-person-add"></i>
                    </div>
                    <a href="{{ route('users.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-2 col-6">
                <!-- small box -->
                <div class="small-box bg-primary">
                    <div class="inner">
                        <h3>{{$quotations}}</h3>

                        <p>Quotations</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-pie-graph"></i>
                    </div>
                    <a href="{{ route('quotations.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-2 col-6">
                <!-- small box -->
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{$requests}}</h3>

                        <p>Pending Requests</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-pie-graph"></i>
                    </div>
                    <a href="{{ route('invoices.request_invoices',['status' => 'pending']) }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-2 col-6">
                <!-- small box -->
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{$invoices}}</h3>

                        <p>Pending Invoices</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-pie-graph"></i>
                    </div>
                    <a href="{{ route('invoices.index',['status' => 'pending']) }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>
        <!-- /. row -->
        <div class="row">
            <div class="col-md-6" style="height: 300px;overflow: scroll;">
                <h3><strong>Receipt Vouchers</strong></h3>
                <table id="example2" class="bg-white table-bordered table-striped table-sm" role="grid" aria-describedby="example1_info" style="width:100%;">
                    <thead>
                        <tr role="row">
                            <th class="sorting sorting_asc" tabindex="0" aria-controls="example1" rowspan="1" colspan="1">S.No</th>
                            <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" >Date</th>
                            <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" >Company</th>
                            <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" >Invoice #</th>
                            <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" >Total</th>
                            <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" >Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($receipts as $key => $receipt)
                            <tr class="odd">
                                <td class="dtr-control sorting_1" tabindex="0">{{$key +1}}</td>
                                <td>{{$receipt->date_time}}</td>
                                <td>
                                    <a href="{{ route('companies.show', $receipt->transactionable->quotation->company->id ) }}" class='btn btn-ghost-success'>
                                    {{ $receipt->transactionable->quotation->company->name }}
                                    </a>
                                </td>
                                <td>{{$receipt->transactionable ? $receipt->transactionable->invoice_no : '' }}</td>
                                <td>{{$receipt->total}}</td>
                                <td>@include('components.datatables_status', [
                                    'msg' => ($receipt->status) ? 'complete' : 'pending',
                                    'type' => ($receipt->status) ? 'success' : 'danger',
                                    ])
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <br>
                <a href="{{route('receipts.index')}}" class="btn btn-block bg-custom btn-sm">View More</a>
            </div>
            <div class="col-md-6" style="height: 300px;overflow: scroll;">
                <h3><strong>Cheques Notifications</strong></h3>
                <table id="example2" class="bg-white table-bordered table-striped table-sm" role="grid" aria-describedby="example1_info" style="width:100%;">
                    <thead>
                        <tr role="row">
                            <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" >Company</th>
                            <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" >Type</th>
                            <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" >Bank/Cheque Clearance Date</th>
                            <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" >Bank/Cheque No</th>
                            <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" >Total</th>
                            <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" >Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cheques as $key => $cheque)
                            <tr class="odd">
                                <td>
                                    @if(isset($cheque->transactionable->quotation->company))
                                        <a href="{{ route('companies.show', $cheque->transactionable->quotation->company->id ) }}" class='btn btn-ghost-success'>
                                        {{ $cheque->transactionable->quotation->company->name }}
                                        </a>
                                    @endif
                                    @if(isset($cheque->transactionable->lpoout->vendor))
                                        <a href="{{ route('vendors.show', $cheque->transactionable->lpoout->vendor->id ) }}" class='btn btn-ghost-success'>
                                        {{ $cheque->transactionable->lpoout->vendor->name }}
                                        </a>
                                    @endif
                                </td>
                                <td>{{ isset($cheque->transactionable->quotation) ? "Receipt" : "Payment"}}</td>
                                <td>{{$cheque->clearance_date}}</td>
                                <td>{{$cheque->payment_no}}</td>
                                <td>{{$cheque->total}}</td>
                                <td>@include('components.datatables_status', [
                                    'msg' => ($cheque->status) ? 'complete' : 'pending',
                                    'type' => ($cheque->status) ? 'success' : 'danger',
                                    ])
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <br>
                <a href="{{route('cheques.index')}}" class="btn btn-block btn-md">View More</a>
            </div>
        </div>
        <!-- /.expiry table -->
        <hr>
        <div class="row">
            <div class="col-md-6" style="height: 300px;overflow: scroll;">
                <h3><strong>Documents </strong></h3>
                <table id="example2" class="bg-white table-bordered table-striped table-sm" role="grid" aria-describedby="example1_info" style="width:100%;">
                    <thead>
                        <tr role="row">
                            <th class="sorting sorting_asc" tabindex="0" aria-controls="example1" rowspan="1" colspan="1">S.No</th>
                            <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" id="asd">Name</th>
                            <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" >Type</th>
                            <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" >Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($documents as $key => $document)
                            <tr class="odd">
                                <td class="dtr-control sorting_1" tabindex="0">{{$key +1}}</td>
                                <td>{{$document->name}}</td>
                                <td>{{ $document->type}}</td>
                                <td>{{$document->date}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <br>
                <a href="{{route('document.index')}}" class="btn btn-block bg-custom btn-sm">View More</a>
            </div>
            <div class="col-md-6" style="height: 300px;overflow: scroll;">
                <h3><strong>Staff Profiles</strong></h3>
                <table id="example2" class="bg-white table-bordered table-striped table-sm" role="grid" aria-describedby="example1_info" style="width:100%;">
                    <thead>
                        <tr role="row">
                            <th class="sorting sorting_asc" tabindex="0" aria-controls="example1" rowspan="1" colspan="1">No</th>
                            <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" >{{__('models/stafprofile.fields.name')}}</th>
                            <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" >Type</th>
                            <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" >Expiry date</th>
                            <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" >Update</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($staff_profiles as $key => $staff_profile)
                            @if ($staff_profile->passport_expiry != null && $staff_profile->passport_expiry < $exp_date)
                                <x-staf_expiry_row :id=" $staff_profile->id" :name="$staff_profile->name" type="Passport" :date="$staff_profile->passport_expiry" :number="$number"/>
                                @php($number++)
                            @endif
                            @if ($staff_profile->visa_expiry != null && $staff_profile->visa_expiry < $exp_date)
                                <x-staf_expiry_row :id=" $staff_profile->id" :name="$staff_profile->name" type="Visa" :date="$staff_profile->visa_expiry" :number="$number"/>
                                @php($number++)
                            @endif
                            @if ($staff_profile->emirates_id_expiry != null && $staff_profile->emirates_id_expiry < $exp_date)
                                <x-staf_expiry_row :id=" $staff_profile->id" :name="$staff_profile->name" type="Emirates Id Expiry" :date="$staff_profile->emirates_id_expiry" :number="$number"/>
                                @php($number++)
                            @endif
                            @if ($staff_profile->labor_card_expiry != null && $staff_profile->labor_card_expiry < $exp_date)
                                <x-staf_expiry_row :id=" $staff_profile->id" :name="$staff_profile->name" type="Labor Card Expiry" :date="$staff_profile->labor_card_expiry" :number="$number"/>
                                @php($number++)
                            @endif
                            @if ($staff_profile->driver_permit_expiry != null && $staff_profile->driver_permit_expiry < $exp_date)
                                <x-staf_expiry_row :id=" $staff_profile->id" :name="$staff_profile->name" type="Driver Permit Expiry" :date="$staff_profile->driver_permit_expiry" :number="$number"/>
                                @php($number++)
                            @endif
                        @endforeach
                    </tbody>
                </table>
                <br>
                <a href="{{route('staf.index')}}" class="btn btn-block btn-md">View More</a>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8" style="height: 300px;overflow: scroll;">
                <h3><strong>Overdue Invoices </strong></h3>
                <table id="example2" class="bg-white table-bordered table-striped table-sm" role="grid" aria-describedby="example1_info" style="width:100%;">
                    <thead>
                        <tr role="row">
                            <th class="sorting sorting_asc" tabindex="0" aria-controls="example1" rowspan="1" colspan="1">Doc.No</th>
                            <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" id="asd">Client</th>
                            <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" id="asd">Start Date</th>
                            <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" >Due Date</th>
                            <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" >Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($overdue_invoices as $key => $invoice)
                            <tr class="odd">
                                <td class="dtr-control sorting_1" tabindex="0">{{$invoice->invoice_no}}</td>
                                <td>
                                    @if(isset($invoice->quotation) && isset($invoice->quotation->company))
                                        {{$invoice->quotation->company->name}}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>{{$invoice->start_date}}</td>
                                <td>{{ $invoice->end_date}}</td>
                                <td>{{$invoice->total_amount}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>
@endsection
