@extends('layouts.master')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">@lang('crud.detail') @lang('models/extensions.singular')</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{!! route('projects.show',$extension->project_id) !!}">@lang('models/projects.singular')</a></li>
                        <li class="breadcrumb-item active">@lang('crud.detail')</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        @include('flash::message')
        {{-- Include Extension attributes --}}
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <!-- Extension Invoices -->
                    <x-CardTable title="Extension_Invoices" :project="$extension->project_id" />
                </div>
                <div class="col-md-6">
                    <!-- Extension Lpoins -->
                    <x-CardTable title="Extension_Lpoins" :project="$extension->project_id" />
                </div>
                <div class="col-md-6">
                    <!-- Extension Receipt -->
                    <x-CardTable title="Extension_Receipts" :project="$extension->project_id" />
                </div>
            </div>
        </div>
    </div>
@endsection
