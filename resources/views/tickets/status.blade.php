@extends('layouts.master')

@section('css')
    @include('layouts.datatables_css')
@endsection

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark text-weight-bold">labor attendance</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item active">labor attendance</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        @include('flash::message')
        <div class="bg-white card-primary card-maroon">
            <div class="card-header">
                <h3 class="card-title">@lang('models/projects.plural') Detail</h3>
            </div>
            <div class="card-body">
            {!! $dataTable->table(['width' => '100%', 'class' => 'table table-hover table-bordered table-striped table-sm text-nowrap']) !!}
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    @include('layouts.datatables_js')
    {!! $dataTable->scripts() !!}
@endsection
