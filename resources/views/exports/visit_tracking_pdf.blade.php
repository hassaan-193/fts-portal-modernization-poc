@php
    use Carbon\Carbon;
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Visit Tracking Report</title>
    <style>
        * { margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; font-size: 10px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h1 { font-size: 18px; margin-bottom: 5px; }
        .header p { font-size: 9px; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #2c3e50; color: white; padding: 10px; text-align: left; font-weight: bold; border: 1px solid #333; }
        td { padding: 8px; border: 1px solid #ddd; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .status { padding: 4px 8px; border-radius: 3px; color: white; font-weight: bold; font-size: 9px; text-align: center; }
        .status-done { background-color: #27ae60; }
        .status-overdue { background-color: #e74c3c; }
        .status-upcoming { background-color: #3498db; }
        .no-records { text-align: center; color: #999; font-style: italic; }
        .currency { text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h1>AMC Visit Tracking Report</h1>
        <p>Generated on {{ now()->format('d-m-Y H:i:s') }}</p>
        @if($filter)
            <p>Filter: <strong>{{ ucfirst(str_replace('_', ' ', $filter)) }}</strong></p>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 12%;">Start Date</th>
                <th style="width: 20%;">Company</th>
                <th style="width: 15%;">Contract Value</th>
                <th style="width: 12%;">Upcoming Visit</th>
                <th style="width: 12%;">End Date</th>
                <th style="width: 12%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($projects as $project)
                @php
                    $visitSchedule = $project->visitSchedules ?? collect();
                    $upcomingVisits = $visitSchedule->filter(function ($schedule) {
                        return $schedule->status !== 'done';
                    });
                    $visitDates = $upcomingVisits->map(function ($schedule) {
                        return Carbon::parse($schedule->visit_date);
                    })->filter()->sortBy(function ($visit) {
                        return $visit->timestamp;
                    });
                    $currentDate = Carbon::now();
                    $nextUpcomingVisit = $visitDates->filter(function ($visit) use ($currentDate) {
                        return $visit->isAfter($currentDate);
                    })->first();
                    $upcomingVisitDate = $nextUpcomingVisit ? $nextUpcomingVisit->toDateString() : 'N/A';
                    $endDate = Carbon::parse($project->date)->addDays(365)->toDateString();
                    
                    $doneCount = $visitSchedule->where('status', 'done')->count();
                    $totalCount = $visitSchedule->count();
                    $overdueCount = $visitSchedule->where('status', '!=', 'done')->filter(function($s) { 
                        return Carbon::parse($s->visit_date)->isPast(); 
                    })->count();
                    
                    if ($totalCount > 0 && $doneCount === $totalCount) {
                        $statusClass = 'status-done';
                        $status = 'Done';
                    } elseif ($overdueCount > 0) {
                        $statusClass = 'status-overdue';
                        $status = 'Overdue';
                    } else {
                        $statusClass = 'status-upcoming';
                        $status = 'Upcoming';
                    }
                    
                    $company = $project->quotation && $project->quotation->company ? $project->quotation->company->name : 'N/A';
                    $contractValue = $project->quotation ? $project->quotation->amount : 0;
                @endphp
                <tr>
                    <td>{{ \Carbon\Carbon::parse($project->date)->format('d-m-Y') }}</td>
                    <td>{{ $company }}</td>
                    <td class="currency">{{ number_format($contractValue, 2) }}</td>
                    <td>{{ $upcomingVisitDate }}</td>
                    <td>{{ $endDate }}</td>
                    <td><span class="status {{ $statusClass }}">{{ $status }}</span></td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="no-records">No records found for the selected filter</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>