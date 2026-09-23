@extends('layouts.master')

@section('css')
@parent
    @include('layouts.datatables_css')
    @include('reports.page_style')
@endsection

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark text-weight-bold">@lang('models/reports.trial_balance.title')</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item active">@lang('models/reports.trial_balance.title')</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        @include('flash::message')
        <div class="bg-white card-primary card-maroon">
            <div class="card-header">
                <h3 class="card-title">@lang('models/reports.trial_balance.title')</h3>
            </div>
            <div class="card-body table-responsive" >
                {!! $dataTable->table(['width' => '100%', 'class' => 'table table-hover table-bordered table-striped table-sm text-nowrap'],true ) !!}
            </div>
        </div>
    </div>
@endsection

@section('scripts')
@parent
    @include('layouts.datatables_js')
    {!! $dataTable->scripts() !!}
    <script>
        $(window).on('load', function() {
            $("div.table-filter").html(`
                <div class="row">
                    <div class="col-sm-12">
                        <span class="filter-icon"><i class="fa fa-filter"></i></span>
                        <div class="filter-group" >
                            <label>Date</label>
                            <input type="text" class="form-control" id="daterange" name="daterange">
                            <input type="hidden" id="from_date" name="from_date">
                            <input type="hidden" id="to_date" name="to_date">
                        </div>
                        <div class="filter-group">
                            <label></label>
                            <button type="button" onclick="clearFilter()" class="btn btn-danger" >Clear </button>
                        </div>
                    </div>
                </div>`
            );

            //Date range picker with time picker
            $('#daterange').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    format: 'YYYY-MM-DD'
                }
            });

            $('#daterange').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
                $('#from_date').val(picker.startDate.format('YYYY-MM-DD'));
                $('#to_date').val(picker.endDate.format('YYYY-MM-DD'));
                if($('#daterange').val() != '' )
                    window.LaravelDataTables["dataTableBuilder"].draw(false);
            });

            $('#daterange').on('cancel.daterangepicker', function(ev, picker) {
                $('#datepicker').val('');
                $('#from_date').val('');
                $('#to_date').val('');
            });
        });

        function clearFilter(){
            $('#daterange').val('');
            $('#from_date').val('');
            $('#to_date').val('');
            
            window.LaravelDataTables["dataTableBuilder"].draw(false);
        }
    </script>
    <script src="{{ asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
    <script src="{{ asset('plugins/select2/js/select2.full.min.js')}}"></script>
@endsection



