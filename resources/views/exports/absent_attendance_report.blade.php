<!-- EMPLOYEE ABSENCES SUMMARY BY DATE RANGE -->
<table>
    <thead>
        <tr style="background-color: #4CAF50; font-weight: bold; color: white;">
            <th style="border: 2px solid #000; padding: 10px;">Employee Name</th>
            <th style="border: 2px solid #000; padding: 10px; text-align: center;">Total Absences in Period</th>
            <th style="border: 2px solid #000; padding: 10px; text-align: center;">Approved &amp; Informed</th>
            <th style="border: 2px solid #000; padding: 10px; text-align: center;">Approved &amp; Uninformed</th>
        </tr>
    </thead>
    <tbody>
        @php
            // Group data by labor_id to get summary per person
            $groupedByLabor = $data->groupBy('labor_id');
        @endphp
        @forelse($groupedByLabor as $laborId => $absences)
            @php
                $laborName = $absences->first()->labor->name ?? 'Unknown';
                $totalCount = $absences->count();
                $informedCount = $absences->filter(function($item) {
                    $approval = $item->approvals()->latest()->first();
                    return $approval && $approval->informed === 'informed';
                })->count();
                $uninformedCount = $absences->filter(function($item) {
                    $approval = $item->approvals()->latest()->first();
                    return $approval && $approval->informed === 'uninformed';
                })->count();
            @endphp
            <tr>
                <td style="border: 1px solid #ddd; padding: 10px;">{{ $laborName }}</td>
                <td style="border: 1px solid #ddd; padding: 10px; text-align: center; font-weight: bold; font-size: 14px;">{{ $totalCount }}</td>
                <td style="border: 1px solid #ddd; padding: 10px; text-align: center; background-color: #e8f5e9;">{{ $informedCount }}</td>
                <td style="border: 1px solid #ddd; padding: 10px; text-align: center; background-color: #ffebee;">{{ $uninformedCount }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="4" style="border: 1px solid #ddd; padding: 10px; text-align: center;">No approved absences found</td>
            </tr>
        @endforelse
    </tbody>
</table>

<br><br>

<br><br>

<table>
    <tr style="background-color: #2196F3; font-weight: bold; color: white;">
        <td colspan="2" style="border: 2px solid #000; padding: 10px;">Summary Statistics</td>
    </tr>
    <tr>
        <td style="border: 1px solid #ddd; padding: 8px;">Total Absence Records:</td>
        <td style="border: 1px solid #ddd; padding: 8px; text-align: right; font-weight: bold;">{{ $data->count() }}</td>
    </tr>
    @if($data->count() > 0)
        <tr>
            <td style="border: 1px solid #ddd; padding: 8px;">Total Unique Employees:</td>
            <td style="border: 1px solid #ddd; padding: 8px; text-align: right; font-weight: bold;">
                {{ $data->groupBy('labor_id')->count() }}
            </td>
        </tr>
        <tr>
            <td style="border: 1px solid #ddd; padding: 8px;">Informed Cases:</td>
            <td style="border: 1px solid #ddd; padding: 8px; text-align: right; font-weight: bold; background-color: #e8f5e9;">
                {{ $data->filter(function($item) { 
                    $approval = $item->approvals()->latest()->first();
                    return $approval && $approval->informed === 'informed';
                })->count() }}
            </td>
        </tr>
        <tr>
            <td style="border: 1px solid #ddd; padding: 8px;">Uninformed Cases:</td>
            <td style="border: 1px solid #ddd; padding: 8px; text-align: right; font-weight: bold; background-color: #ffebee;">
                {{ $data->filter(function($item) { 
                    $approval = $item->approvals()->latest()->first();
                    return $approval && $approval->informed === 'uninformed';
                })->count() }}
            </td>
        </tr>
    @endif
</table>

<!-- Filters Info -->
@if(!empty($filters))
    <table>
        <tr>
            <td style="font-weight: bold;">Report Filters Applied:</td>
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
        @if(!empty($filters['status']))
            <tr>
                <td>Status: {{ ucfirst($filters['status']) }}</td>
            </tr>
        @endif
        @if(!empty($filters['informed']))
            <tr>
                <td>Informed Status: {{ ucfirst($filters['informed']) }}</td>
            </tr>
        @endif
        <tr>
            <td>Generated: {{ \Carbon\Carbon::now()->format('M d, Y H:i:s') }}</td>
        </tr>
    </table>
@endif
