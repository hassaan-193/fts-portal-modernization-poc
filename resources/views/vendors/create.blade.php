@extends('layouts.master')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark text-weight-bold">@lang('crud.add_new') @lang('models/vendors.singular')</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{!! route('vendors.index') !!}">@lang('models/vendors.singular')</a></li>
                    <li class="breadcrumb-item active">@lang('crud.add_new')</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<div class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="bg-white card-primary card-maroon">
                <div class="card-header">
                    <h3 class="card-title">@lang('crud.add_new') @lang('models/vendors.singular')</h3>
                </div>
                <div class="card-body">
                    {!! Form::open(['route' => 'vendors.store', 'files' => true]) !!}
                        @include(strtolower(__('models/vendors.plural')).'.fields')
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

