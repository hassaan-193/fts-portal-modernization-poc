@extends('layouts.master')

@section('css')
    @include('layouts.datatables_css')
@endsection

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark text-weight-bold">AMC @lang('models/projects.plural')</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item active">AMC @lang('models/projects.plural')</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        @include('flash::message')
        <div class="bg-white card-primary card-maroon">
            <div class="card-header">
                <h3 class="card-title">@lang('models/projects.plural') Detail</h3>
            </div>
            <div class="card-body">
            <div class="mb-3">
                <button class="btn btn-sm btn-outline-secondary expiry-filter active" data-filter="">All</button>
                <button class="btn btn-sm btn-outline-info expiry-filter" data-filter="this_month">Expiring This Month</button>
                <button class="btn btn-sm btn-outline-warning expiry-filter" data-filter="next_month">Expiring Next Month</button>
                <button class="btn btn-sm btn-outline-success expiry-filter" data-filter="visit_this_month">Visits This Month</button>
                <button class="btn btn-sm btn-outline-primary expiry-filter" data-filter="visit_next_month">Visits Next Month</button>
                <button class="btn btn-sm btn-outline-danger float-right" id="export-pdf">
                    <i class="fas fa-file-pdf"></i> Export PDF
                </button>
                <button class="btn btn-sm btn-outline-success float-right mr-2" id="export-excel">
                    <i class="fas fa-file-excel"></i> Export Excel
                </button>
            </div>
            {!! $dataTable->table(['width' => '100%', 'class' => 'table table-hover table-bordered table-striped table-sm text-nowrap']) !!}
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    @include('layouts.datatables_js')
    {!! $dataTable->scripts() !!}

<script>
$(document).ready(function() {
    let table = window.LaravelDataTables["dataTableBuilder"];
    let currentFilter = '';

    $('.expiry-filter').on('click', function() {
        $('.expiry-filter')
            .removeClass('btn-primary')
            .addClass('btn-outline-secondary');
        $(this)
            .addClass('btn-primary')
            .removeClass('btn-outline-secondary');

        currentFilter = $(this).data('filter') || '';
        let newUrl = "{{ route('projects.visit-tracking') }}" + "?expiry_filter=" + currentFilter;

        console.log("Reloading table with URL:", newUrl);
        table.ajax.url(newUrl).load();
    });

    $('#export-pdf').on('click', function() {
        let exportUrl = "{{ route('projects.visit-tracking-export') }}" + "?expiry_filter=" + currentFilter;
        window.open(exportUrl, '_blank');
    });

    $('#export-excel').on('click', function() {
        let exportUrl = "{{ route('projects.visit-tracking-export-excel') }}" + "?expiry_filter=" + currentFilter;
        window.open(exportUrl, '_blank');
    });
});
</script>
@endsection