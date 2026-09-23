@section('css')
    @include('layouts.datatables_css')
@endsection

<div class="table-responsive">
    {!! $dataTable->table(['width' => '100%', 'class' => 'table table-hover table-bordered table-striped table-sm text-nowrap']) !!}
</div>

@section('scripts')
@parent
    @include('layouts.datatables_js')
    {!! $dataTable->scripts() !!}
    <script>
    $(document).ready(function() {
        // Export Report with Filters
        $('#exportReportBtn').on('click', function(e) {
            e.preventDefault();
            
            var dateFrom = $('#reportDateFrom').val();
            var dateTo = $('#reportDateTo').val();
            var laborId = $('#reportLaborId').val();
            var status = $('#reportStatus').val();
            var informed = $('#reportInformed').val();
            
            if (!dateFrom && !dateTo && !laborId && !status && !informed) {
                alert('Please select at least one filter criteria (date range, labor, status, or informed status)');
                return;
            }
            
            var params = new URLSearchParams();
            if (dateFrom) params.append('date_from', dateFrom);
            if (dateTo) params.append('date_to', dateTo);
            if (laborId) params.append('labor_id', laborId);
            if (status) params.append('status', status);
            if (informed) params.append('informed', informed);
            
            window.location.href = '{{ route("pending.attendance.export") }}?' + params.toString();
        });
    });
    </script>
@endsection
