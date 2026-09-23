@extends('layouts.master')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark text-weight-bold">@lang('models/petty_cashes.title')</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item active">@lang('models/petty_cashes.title')</li>
                    </ol>
                </div>
            </div>
        </div>
  </div>
  <div class="content">
    @include('flash::message')
    <div class="bg-white card-primary card-maroon">
        <div class="card-header">
            <h3 class="card-title">@lang('models/petty_cashes.title') Detail</h3>
        </div>
        <div class="row">
            <div class="col-12 col-md-6">
                <div class="row">
                <div class="col-12 col-sm-6 col-md-4">
                    <div class="info-box">
                    <span class="info-box-icon bg-info elevation-1">
                        <i class="fas fa-project-diagram"></i>
                    </span>

                    <div class="info-box-content">
                        <span class="info-box-text">Total Amount</span>
                        <span class="info-box-number">
                            {{ $total_amount }}
                        </span>
                    </div>
                    <!-- /.info-box-content -->
                    </div>
                    <!-- /.info-box -->
                </div>
                <!-- /.col -->
                <div class="col-12 col-sm-6 col-md-4">
                    <div class="info-box mb-3">
                    <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-thumbs-up"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Total Advance</span>
                        <span class="info-box-number">
                            {{$total_advance}}
                        </span>
                    </div>
                    <!-- /.info-box-content -->
                    </div>
                    <!-- /.info-box -->
                </div>
                <!-- /.col -->
                </div>
            </div>
        </div>
        <div class="card-body table-responsive" >
            @include(strtolower(__('models/petty_cashes.plural')).'.table')
        </div>
    </div>
</div>
@endsection


