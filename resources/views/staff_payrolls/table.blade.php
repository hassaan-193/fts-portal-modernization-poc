@section('css')
@parent
    @include('layouts.datatables_css')
    @include('staff_payrolls.page_style')
@endsection

{!! $dataTable->table(['width' => '100%', 'class' => 'table table-hover table-bordered table-striped table-sm']) !!}

@section('scripts')
@parent
    @include('layouts.datatables_js')
    {!! $dataTable->scripts() !!}
    @include('staff_payrolls.page_script')
@endsection
