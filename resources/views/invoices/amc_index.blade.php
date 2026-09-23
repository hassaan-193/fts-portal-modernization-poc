@extends('layouts.master')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark text-weight-bold">@lang('models/invoices.plural')</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item active">@lang('models/invoices.plural')</li>
                    </ol>
                </div>
            </div>
        </div>
  </div>
  <div class="content">
    @include('flash::message')
    <div class="bg-white card-primary card-maroon">
        <div class="card-header">
            <h3 class="card-title">@lang('models/invoices.plural') Detail</h3>
        </div>
        <div class="card-body table-responsive" >
          {{-- 🧾 Summary Cards --}}
        <div class="row mb-4">
          <div class="col-md-3">
            <div class="small-box bg-primary text-white text-center p-3 rounded">
              <div class="inner">
                <h3>{{ $stats->total_invoices ?? 0 }}</h3>
                <p>Total AMC Invoices</p>
              </div>
              <div class="icon"><i class="fas fa-file-invoice"></i></div>
            </div>
          </div>

          <div class="col-md-3">
            <div class="small-box bg-warning text-white text-center p-3 rounded">
              <div class="inner">
                <h3>{{ $stats->pending_invoices ?? 0 }}</h3>
                <p>Pending AMC Invoices</p>
              </div>
              <div class="icon"><i class="fas fa-hourglass-half"></i></div>
            </div>
          </div>

          <div class="col-md-3">
            <div class="small-box bg-success text-white text-center p-3 rounded">
              <div class="inner">
                <h3>{{ $stats->complete_invoices ?? 0 }}</h3>
                <p>Completed AMC Invoices</p>
              </div>
              <div class="icon"><i class="fas fa-check-circle"></i></div>
            </div>
          </div>


        </div>

            <div class="row">
                <div class="col-12">
                    <div class="row mb-3">
                      <div class="col-md-4">
                        <label><strong>Invoice Count Filter</strong></label>
                        <select id="invoice_count_filter" class="form-control">
                          <option value="">-- All --</option>
                          <option value="2_or_more">Two or More Invoices Left</option>
                        </select>
                      </div>

                      <div class="col-md-4">
                        <label><strong>Status Filter</strong></label>
                        <select id="status_filter" class="form-control">
                          <option value="">-- All --</option>
                          <option value="pending">Pending</option>
                          <option value="complete">Complete</option>
                        </select>
                      </div>

                      <div class="col-md-4 d-flex align-items-end">
                        <button id="filterBtn" class="btn btn-primary w-100">
                          <i class="fas fa-filter"></i> Apply Filters
                        </button>
                      </div>
                    </div>

                </div>
            </div>
            <br/>
            <input type="hidden" name="status" value="{{ isset($type) && !empty($type) ? $type : '' }}"/>
            @include(strtolower(__('models/invoices.plural')).'.table')
        </div>
    </div>
</div>
@endsection


