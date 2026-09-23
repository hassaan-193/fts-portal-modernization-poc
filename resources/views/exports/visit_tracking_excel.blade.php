<table>
    <thead>
        <tr style="background-color: #4B0082; color: white; font-weight:  bold;">
            <th>ID</th>
            <th>Company</th>
            <th>Category</th>
            <th>Start Date</th>
            <th>Expiry Date</th>
            <th>Total Visits</th>
            <th>Pending Visits</th>
            <th>Next Visit Date</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($projects as $project)
            @php
                $company = 'N/A';
                if ($project->quotation && isset($project->quotation->company) && $project->quotation->company) {
                    $company = $project->quotation->company->name;
                }
                
                $nextVisit = $project->visitSchedules
                    ->where('status', '!=', 'done')
                    ->sortBy('visit_date')
                    ->first();
                $nextVisitDate = $nextVisit ? $nextVisit->visit_date :  'N/A';
                
                $expiryDate = \Carbon\Carbon::parse($project->date)->addYear()->format('Y-m-d');
                
                $pendingVisits = $project->visitSchedules
                    ->whereIn('status', ['pending', 'upcoming'])
                    ->count();
            @endphp
            <tr>
                <td>{{ $project->id }}</td>
                <td>{{ $company }}</td>
                <td>{{ strtoupper($project->category) }}</td>
                <td>{{ \Carbon\Carbon::parse($project->date)->format('Y-m-d') }}</td>
                <td>{{ $expiryDate }}</td>
                <td>{{ $project->visits ??  0 }}</td>
                <td>{{ $pendingVisits }}</td>
                <td>{{ $nextVisitDate }}</td>
                <td>{{ $project->status ?? 'Active' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>