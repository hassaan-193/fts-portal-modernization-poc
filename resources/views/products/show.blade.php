@extends('layouts.master')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">@lang('crud.detail') @lang('models/products.singular')</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{!! route('products.index') !!}">@lang('models/products.singular')</a></li>
                        <li class="breadcrumb-item active">@lang('crud.detail')</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        @include('flash::message')
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="card card-primary card-outline card-maroon">
                    <div class="card-body">
                        <p class="text-muted text-center"> <b>@lang('models/products.singular') Details</b></p>
                        <ul class="list-group list-group-unbordered mb-3">
                            @include(strtolower(__('models/products.plural')).'.show_fields')
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
