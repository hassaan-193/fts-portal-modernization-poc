@extends('layouts.master')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark text-weight-bold">@lang('models/lpoins.plural')</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                    <li class="breadcrumb-item active">@lang('models/lpoins.plural')</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    @include('flash::message')
    <div class="bg-white card-primary card-maroon">
        <div class="card-header">
            <h3 class="card-title">@lang('models/lpoins.plural') Detail</h3>
        </div>

        <div class="row mb-3">
            <div class="col-md-3">
                <label for="date_from">@lang('From Date')</label>
                <input type="date" id="date_from" class="form-control" placeholder="From Date">
            </div>

            <div class="col-md-3">
                <label for="date_to">@lang('To Date')</label>
                <input type="date" id="date_to" class="form-control" placeholder="To Date">
            </div>

            <div class="col-md-3 align-self-end">
                <button id="filterBtn" class="btn btn-primary">Filter</button>
            </div>
        </div>

        @include(strtolower(__('models/lpoins.plural')).'.table')
    </div>
</div>
@endsection
