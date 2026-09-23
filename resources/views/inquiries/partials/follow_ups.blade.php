<div class="card bg-white mb-3">
    <div class="card-header">
        <h3 class="card-title"><i class="fa fa-comments mr-1"></i> Sales Follow-ups</h3>
        <div class="card-tools">
            <span class="badge badge-primary">{{ $followUps->count() }} entries</span>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm table-hover mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>Date</th>
                        <th>Notes</th>
                        <th>Client Feedback</th>
                        <th>Status</th>
                        <th>By</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($followUps as $fu)
                    @php
                        $fuColor = ['Won' => 'success', 'Lost' => 'danger', 'Under Follow-up' => 'warning'][$fu->status] ?? 'secondary';
                    @endphp
                    <tr>
                        <td class="text-nowrap">{{ $fu->follow_up_date->format('d M Y') }}</td>
                        <td>{{ $fu->follow_up_notes }}</td>
                        <td>{{ $fu->client_feedback ?? '—' }}</td>
                        <td><span class="badge badge-{{ $fuColor }}">{{ $fu->status }}</span></td>
                        <td class="text-nowrap">{{ optional($fu->creator)->name ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
