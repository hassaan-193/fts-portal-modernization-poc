@extends('layouts.master')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark text-weight-bold">@lang('crud.edit') @lang('models/companies.singular')</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{!! route('companies.index') !!}">@lang('models/companies.singular')</a></li>
                    <li class="breadcrumb-item active">@lang('crud.edit')</li>
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
                    <h3 class="card-title">@lang('crud.edit') @lang('models/companies.singular')</h3>
                </div>
                <div class="card-body">
                    {!! Form::model($company, ['route' => ['companies.update', $company->id], 'method' => 'patch', 'files' => true]) !!}
                        @include(strtolower(__('models/companies.plural')).'.fields')
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
