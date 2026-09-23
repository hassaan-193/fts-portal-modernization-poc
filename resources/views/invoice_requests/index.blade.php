@extends('layouts.master')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark text-weight-bold">@lang('models/invoice_requests.plural')</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item active">@lang('models/invoice_requests.plural')</li>
                    </ol>
                </div>
            </div>
        </div>
  </div>
  <div class="content">
    @include('flash::message')
    <div class="bg-white card-primary card-maroon">
        <div class="card-header">
            <h3 class="card-title">@lang('models/invoice_requests.plural') Detail</h3>
        </div>
        <div class="card-body table-responsive" >
            <input type="hidden" name="status" value="{{ isset($type) && !empty($type) ? $type : '' }}"/>

            @include(strtolower(__('models/invoice_requests.plural')).'.table')
        </div>
    </div>
</div>
@endsection


