@extends('layouts.master')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark text-weight-bold">@lang('models/stafprofile.front')</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item active">@lang('models/stafprofile.front')</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        @include('flash::message')
        <div class="bg-white card-primary card-maroon">
            <div class="card-header">
                <h3 class="card-title">@lang('models/stafprofile.front') Detail</h3>
            </div>
            <div class="card-body">
                <!-- Date Filter Form -->
                <form method="GET" action="{{ url()->current() }}" class="mb-3">
                    <div class="row">
                        <div class="col-md-4">
                            <label for="expiry_date">Filter by Expiry Date:</label>
                            <div class="input-group">
                                <input type="date" 
                                       id="expiry_date" 
                                       name="expiry_date" 
                                       class="form-control" 
                                       value="{{ request('expiry_date') }}"
                                       placeholder="Select Date">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-search"></i> Filter
                                    </button>
                                    @if(request('expiry_date'))
                                        <a href="{{ url()->current() }}" class="btn btn-secondary">
                                            <i class="fa fa-times"></i> Clear
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                
                @if(request('expiry_date'))
                    <div class="alert alert-info">
                        Showing records expiring on: <strong>{{ request('expiry_date') }}</strong>
                    </div>
                @endif
                
                <div class="table-responsive">
                    @include(strtolower(__('models/stafprofile.plural')).'.table')
                </div>
            </div>
        </div>
    </div>
@endsection