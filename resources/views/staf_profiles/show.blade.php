@extends('layouts.master')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark font-weight-bold">@lang('crud.detail') @lang('models/stafprofile.front')</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{!! route('staf.index') !!}">@lang('models/stafprofile.front')</a></li>
                        <li class="breadcrumb-item active">@lang('crud.detail')</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        @include('flash::message')
        
        @include('staf_profiles.show_fields', ['model' => $profile, 'profile' => $profile])

    </div>

    <style>
        .card {
            border: none;
            border-radius: 8px;
        }
        
        .info-box {
            border: 1px solid red;
            border-radius: 8px;
            padding: 8px;
            margin-bottom: 15px;
            background-color: #ffffff;
            transition: all 0.3s ease;
        }
        
        .info-box:hover {
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .info-label {
            font-weight: 600;
            color: #333;
            font-size: 12px;
            text-transform: uppercase;
            margin-bottom: 5px;
            display: block;
        }
        
        .info-value {
            color: #666;
            font-size: 14px;
            margin-left:8%;
    
        }
        
        .section-header {
            color: #dc3545;
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 0;
            display: flex;
            align-items: center;
        }
        
        .section-header i {
            margin-right: 10px;
        }
    </style>
    
@endsection