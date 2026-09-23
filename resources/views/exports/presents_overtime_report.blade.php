<table>
    <thead>
        <tr style="background-color: #f3f3f3; font-weight: bold;">
            <th style="border: 1px solid #000; padding: 8px;">Date</th>
            <th style="border: 1px solid #000; padding: 8px;">Day</th>
            <th style="border: 1px solid #000; padding: 8px;">Labor Name</th>
            <th style="border: 1px solid #000; padding: 8px;">Foreman Name</th>
            <th style="border: 1px solid #000; padding: 8px;">Site Name</th>
            <th style="border: 1px solid #000; padding: 8px;">Overtime (Hours)</th>
        </tr>
    </thead>
    <tbody>
        @php
            $totalOvertime = 0;
        @endphp
        @forelse($data as $row)
            @php
                $date = \Carbon\Carbon::createFromFormat('Y-m-d', $row->attendance_date);
                $totalOvertime += (float)$row->overtime_hours;
            @endphp
            <tr>
                <td style="border: 1px solid #ddd; padding: 8px;">{{ $date->format('M d, Y') }}</td>
                <td style="border: 1px solid #ddd; padding: 8px;">{{ $date->format('l') }}</td>
                <td style="border: 1px solid #ddd; padding: 8px;">{{ $row->labor_name }}</td>
                <td style="border: 1px solid #ddd; padding: 8px;">{{ $row->foreman_name ?? '-' }}</td>
                <td style="border: 1px solid #ddd; padding: 8px;">{{ $row->site_name ?? '-' }}</td>
                <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">{{ number_format((float)$row->overtime_hours, 2) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="6" style="border: 1px solid #ddd; padding: 8px; text-align: center;">No data found</td>
            </tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr style="background-color: #f3f3f3; font-weight: bold;">
            <td colspan="5" style="border: 1px solid #000; padding: 8px; text-align: right;">Total Overtime:</td>
            <td style="border: 1px solid #000; padding: 8px; text-align: right;">{{ number_format($totalOvertime, 2) }} hrs</td>
        </tr>
    </tfoot>
</table>

@if(!empty($filters))
    <br><br>
    <table>
        <tr>
            <td style="font-weight: bold;">Report Filters:</td>
        </tr>
        @if(!empty($filters['date_from']))
            <tr>
                <td>Date From: {{ \Carbon\Carbon::createFromFormat('Y-m-d', $filters['date_from'])->format('M d, Y') }}</td>
            </tr>
        @endif
        @if(!empty($filters['date_to']))
            <tr>
                <td>Date To: {{ \Carbon\Carbon::createFromFormat('Y-m-d', $filters['date_to'])->format('M d, Y') }}</td>
            </tr>
        @endif
        @if(!empty($filters['labor_name']))
            <tr>
                <td>Labor Name: {{ $filters['labor_name'] }}</td>
            </tr>
        @endif
        <tr>
            <td>Generated: {{ \Carbon\Carbon::now()->format('M d, Y H:i:s') }}</td>
        </tr>
    </table>
@endif
