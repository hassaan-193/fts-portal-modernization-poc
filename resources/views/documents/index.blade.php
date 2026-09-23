@extends('layouts.master')

@section('css')
@parent
    @include('reports.page_style')
@endsection

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark text-weight-bold">@lang('models/document.front')</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item active">@lang('models/document.front')</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        @include('flash::message')
        <div class="bg-white card-primary card-maroon">
            <div class="card-header">
                <h3 class="card-title">@lang('models/document.front') Detail</h3>
            </div>
            <div class="card-body table-responsive">
               @include(strtolower(__('models/document.plural')).'.table')
            </div>
        </div>
    </div>
@endsection
@section('scripts')
@parent
<script>
    var doc_types = @json(config('enum.expirey_document_types'));
    var options='';
    Object.entries(doc_types).forEach(doc => {
        options +=`<option value="${doc[1]}">${doc[1]}</option>`;
    });
    $(window).on('load', function() {
        $("div.table-filter").html(`
            <div class="row">
                <div class="col-sm-12">
                    <span class="filter-icon"><i class="fa fa-filter"></i></span>
                    <div class="filter-group d-inline">
                        <label>Search By</label>
                        <select id="document_type" class="form-control" onchange="setSearchType()" name="document_type">
                            <option></option>
                            ${options}
                        </select>
                    </div>
                    <div class="filter-group d-inline">
                        <label></label>
                        <button type="button" onclick="clearFilter()" class="btn btn-danger" >Clear </button>
                    </div>
                </div>
            </div>`
        );
    });

    function setSearchType(){
        window.LaravelDataTables["dataTableBuilder"].draw(false);
    }
    function clearFilter(){
        document.getElementById('document_type').selectedIndex = 0;
        window.LaravelDataTables["dataTableBuilder"].draw(false);
    }
</script>
@endsection
