@extends('layouts.master')

@section('css')
@parent
    @include('layouts.datatables_css')
    @include('reports.page_style')
@endsection

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark text-weight-bold">@lang('models/reports.receipt.title')</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item active">@lang('models/reports.receipt.title')</li>
                    </ol>
                </div>
            </div>
        </div>
  </div>
  <div class="content">
    @include('flash::message')
    <div class="bg-white card-primary card-maroon">
        <div class="card-header">
            <h3 class="card-title">@lang('models/reports.receipt.title')</h3>
        </div>
        <div class="card-body table-responsive" >
            {!! $dataTable->table(['width' => '100%', 'class' => 'table table-hover table-bordered table-striped table-sm text-nowrap'],true ) !!}
        </div>
    </div>
</div>
@endsection

@section('scripts')
@parent
    @include('layouts.datatables_js')
    {!! $dataTable->scripts() !!}
    @include('reports.page_script')
@endsection



