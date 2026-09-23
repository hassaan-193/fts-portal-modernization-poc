@section('css')
    @include('layouts.datatables_css')
@endsection

{!! $dataTable->table(['width' => '100%', 'class' => 'table table-hover table-bordered table-striped table-sm text-nowrap']) !!}

@section('scripts')
    @include('layouts.datatables_js')
    {!! $dataTable->scripts() !!}

<script>
    $(document).ready(function() {
    const table = window.LaravelDataTables["dataTableBuilder"];

    $('#filterBtn').on('click', function() {
        const remainingFilter = $('#invoice_count_filter').val();
        const statusFilter = $('#status_filter').val();

        table.ajax.url(`{{ route('amc.invoices.index') }}?remaining_filter=${remainingFilter}&status_filter=${statusFilter}`).load();
    });
    });
</script>
@endsection
