@section('css')
    @include('layouts.datatables_css')
@endsection

<table width="100%" class="table table-hover table-bordered table-striped table-sm text-nowrap" id="presentsTable">
    <thead>
        <tr>
            <th>#</th>
            <th>Date</th>
            <th>Day</th>
            <th>Labor</th>
            <th>Foreman</th>
            <th>Site Name</th>
            <th>Overtime (Hours)</th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>

@section('scripts')
@parent
    @include('layouts.datatables_js')
    <script>
    $(document).ready(function() {
        var tableData = {!! json_encode($tableData['data'] ?? []) !!} || [];
        console.log('Table data received:', tableData.length, 'records');
        
        var table = $('#presentsTable').DataTable({
            data: tableData,
            columns: [
                {data: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'formatted_date', orderable: false, searchable: false},
                {data: 'day_name', searchable: false, orderable: false},
                {data: 'labor_name', searchable: true},
                {data: 'foreman_name', searchable: true},
                {data: 'site_name', searchable: true},
                {data: 'formatted_overtime', orderable: false, searchable: false}
            ],
            dom: '<"row" <"col-md-3"B> <"col-md-7"<"table-filter">> <"col-md-2"f> >rt<"row" <"col-md-6"li> <"col-md-6"p> >',
            stateSave: false,
            order: [[1, 'desc']],
            processing: false,
            serverSide: false,
            buttons: [
                {
                    extend: 'excel',
                    className: 'btn btn-default btn-sm no-corner',
                    text: '<i class="fa fa-download"></i> Export'
                },
                {
                    extend: 'reload',
                    className: 'btn btn-default btn-sm no-corner',
                    text: '<i class="fa fa-refresh"></i> Reload'
                }
            ]
        });
        
        console.log('DataTable initialized with', table.data().length, 'rows');

        // Apply filters
        $('#filterBtn').on('click', function(e) {
            e.preventDefault();
            var dateFrom = $('#dateFrom').val();
            var dateTo = $('#dateTo').val();

            $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                if (settings.nTable.id !== 'presentsTable') return true;

                var rowDate = data[1]; // Date column

                if (dateFrom && !isDateInRange(rowDate, dateFrom, true)) return false;
                if (dateTo && !isDateInRange(rowDate, dateTo, false)) return false;

                return true;
            });

            table.draw();
            $.fn.dataTable.ext.search.pop();
        });

        // Reset filters
        $('#resetBtn').on('click', function(e) {
            e.preventDefault();
            $('#dateFrom').val('');
            $('#dateTo').val('');
            $.fn.dataTable.ext.search.length = 0;
            table.draw();
        });

        // Export Report with Filters
        $('#exportReportBtn').on('click', function(e) {
            e.preventDefault();
            
            var dateFrom = $('#reportDateFrom').val();
            var dateTo = $('#reportDateTo').val();
            var laborId = $('#reportLaborId').val();
            
            if (!dateFrom && !dateTo && !laborId) {
                alert('Please select at least a date range or labor name to generate the report');
                return;
            }
            
            var params = new URLSearchParams();
            if (dateFrom) params.append('date_from', dateFrom);
            if (dateTo) params.append('date_to', dateTo);
            if (laborId) params.append('labor_id', laborId);
            
            window.location.href = '{{ route("attendance.presents.export") }}?' + params.toString();
        });

        function isDateInRange(dateStr, filterDate, isFrom) {
            try {
                var parts = dateStr.trim().split(/[\s,]+/);
                var monthStr = parts[0];
                var day = parts[1];
                var year = parts[2];

                var months = {
                    'Jan': '01', 'Feb': '02', 'Mar': '03', 'Apr': '04',
                    'May': '05', 'Jun': '06', 'Jul': '07', 'Aug': '08',
                    'Sep': '09', 'Oct': '10', 'Nov': '11', 'Dec': '12'
                };

                var month = months[monthStr];
                var rowDate = new Date(year, parseInt(month) - 1, day);
                var filter = new Date(filterDate);

                if (isFrom) {
                    return rowDate >= filter;
                } else {
                    return rowDate <= filter;
                }
            } catch (e) {
                return true;
            }
        }
    });
    </script>
@endsection
