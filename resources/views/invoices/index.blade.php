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
            
            <div class="card-body table-responsive">
                <div class="row mb-3">
                    <div class="col-12">
                        <a href="{{ route('invoices.request_invoices') }}" class="btn btn-default float-right">
                            <i class="fas fa-file"></i> Requested Invoices
                        </a>
                    </div>
                </div>
                
                <!-- Filter Section -->
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h5 class="mb-0"><i class="fas fa-filter"></i> Pending Invoice Filters</h5>
                            </div>
                            <div class="card-body">
                                <form method="GET" action="{{ route('invoices.index') }}" id="filterForm">
                                    <div class="row">
                                        <!-- Filter 1: Companies with 2+ Pending Invoices -->
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" 
                                                           class="custom-control-input" 
                                                           id="multiple_pending" 
                                                           name="multiple_pending" 
                                                           value="1"
                                                           {{ $multiplePendingFilter == '1' ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="multiple_pending">
                                                        <strong>Show companies with 2+ pending invoices</strong>
                                                    </label>
                                                </div>
                                                <small class="text-muted">Display all pending invoices from companies that have at least 2 pending invoices</small>
                                            </div>
                                        </div>
                                        
                                        <!-- Filter 2: Search by Company -->
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label for="company_id"><strong>Search by Company</strong></label>
                                                <select class="form-control select2" 
                                                        id="company_id" 
                                                        name="company_id">
                                                    <option value="">-- Select Company --</option>
                                                    @foreach($companies as $company)
                                                        <option value="{{ $company->id }}" 
                                                                {{ $selectedCompany == $company->id ? 'selected' : '' }}>
                                                            {{ $company->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <small class="text-muted">View all pending invoices for a specific company</small>
                                            </div>
                                        </div>
                                        
                                        <!-- Submit and Clear Buttons -->
                                        <div class="col-md-3">
                                            <label class="d-block">&nbsp;</label>
                                            <button type="submit" class="btn btn-primary btn-block mb-2">
                                                <i class="fas fa-search"></i> Apply Filters
                                            </button>
                                            <a href="{{ route('invoices.index') }}" class="btn btn-secondary btn-block">
                                                <i class="fas fa-times"></i> Clear Filters
                                            </a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Company Pending Invoice Statistics -->
                @if($companyStats && $companyStats->total_pending_invoices > 0)
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="card border-left-danger shadow-sm">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-md-12 mb-2">
                                        <h4 class="mb-0">
                                            <i class="fas fa-building text-primary"></i> 
                                            <strong>{{ $companyStats->company_name }}</strong> - Pending Invoice Summary
                                        </h4>
                                    </div>
                                </div>
                                <hr class="mt-2 mb-3">
                                <div class="row text-center">
                                    <div class="col-md-6">
                                        <div class="card bg-warning">
                                            <div class="card-body">
                                                <h5 class="card-title text-white">
                                                    <i class="fas fa-file-invoice"></i> Total Pending Invoices
                                                </h5>
                                                <h2 class="card-text text-white font-weight-bold">
                                                    {{ $companyStats->total_pending_invoices }}
                                                </h2>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card bg-danger">
                                            <div class="card-body">
                                                <h5 class="card-title text-white">
                                                    <i class="fas fa-money-bill-wave"></i> Total Pending Amount
                                                </h5>
                                                <h2 class="card-text text-white font-weight-bold">
                                                    {{ number_format($companyStats->total_pending_amount, 2) }}
                                                </h2>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @elseif($companyStats && $companyStats->total_pending_invoices == 0)
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="alert alert-success">
                            <h5><i class="fas fa-check-circle"></i> {{ $companyStats->company_name }}</h5>
                            <p class="mb-0">This company has no pending invoices. All invoices are complete!</p>
                        </div>
                    </div>
                </div>
                @endif
                
                <!-- Hidden input for status -->
                <input type="hidden" name="status" value="{{ isset($type) && !empty($type) ? $type : '' }}"/>
                
                <!-- DataTable -->
                @include(strtolower(__('models/invoices.plural')).'.table')
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .border-left-danger {
        border-left: 5px solid #dc3545 !important;
    }
    .card-body .card {
        margin-bottom: 0;
    }
</style>
@endpush

@section('scripts')
    <script src="{{ asset('plugins/select2/js/select2.full.min.js')}}"></script>
    <script>
        $(document).ready(function() {
            $('#company_id').select2({
                theme: 'bootstrap4',
                placeholder: "Select Account Type",
                allowClear: true
            });

            // When filters change, reload table via AJAX
            $('#filterForm').on('submit', function(e) {
                e.preventDefault();
                window.LaravelDataTables['dataTableBuilder'].ajax.reload();
            });

            // Clear filters
            $('.btn-secondary').on('click', function(e) {
                e.preventDefault();
                $('#company_id').val('').trigger('change');
                $('#multiple_pending').prop('checked', false);
                window.LaravelDataTables['dataTableBuilder'].ajax.reload();
            });
        });
    </script>
@endsection
