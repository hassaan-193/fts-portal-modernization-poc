@section('css')
@parent
    @include('layouts.datatables_css')
    @include('reports.page_style')
@endsection

{!! $dataTable->table(['width' => '100%', 'class' => 'table table-hover table-bordered table-striped table-sm text-nowrap']) !!}

@section('scripts')
@parent
    @include('layouts.datatables_js')
    {!! $dataTable->scripts() !!}
    @include('invoice_requests.page_script')
@endsection
