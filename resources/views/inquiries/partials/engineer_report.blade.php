<div class="card bg-white mb-3">
    <div class="card-header">
        <h3 class="card-title"><i class="fa fa-hard-hat mr-1"></i> Engineer Report</h3>
        <div class="card-tools">
            @if($report->visit_completed)
                <span class="badge badge-success">Visit Completed</span>
            @else
                <span class="badge badge-warning">Visit Not Completed</span>
            @endif
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <p><strong>Submitted By:</strong> {{ optional($report->engineer)->name ?? '—' }}</p>
                <p><strong>Submitted At:</strong> {{ $report->created_at->format('d M Y H:i') }}</p>
                @if($report->estimated_cost)
                <p><strong>Estimated Cost:</strong> AED {{ number_format($report->estimated_cost, 2) }}</p>
                @endif
            </div>
            <div class="col-md-6">
                @if($report->site_condition_notes)
                <p><strong>Site Condition:</strong><br>{{ $report->site_condition_notes }}</p>
                @endif
            </div>
        </div>

        <div class="row mt-2">
            @if($report->scope_understanding)
            <div class="col-md-6">
                <div class="callout callout-info">
                    <strong>Scope Understanding</strong>
                    <p>{{ $report->scope_understanding }}</p>
                </div>
            </div>
            @endif
            @if($report->materials_required)
            <div class="col-md-6">
                <div class="callout callout-warning">
                    <strong>Materials Required</strong>
                    <p>{{ $report->materials_required }}</p>
                </div>
            </div>
            @endif
            @if($report->challenges_risks)
            <div class="col-md-12">
                <div class="callout callout-danger">
                    <strong>Challenges / Risks</strong>
                    <p>{{ $report->challenges_risks }}</p>
                </div>
            </div>
            @endif
        </div>

        {{-- Site visit files --}}
        @if($report->getMedia('site_visit')->isNotEmpty())
        <hr>
        <strong>Site Visit Files:</strong>
        <ul class="list-group mt-2">
            @foreach($report->getMedia('site_visit') as $file)
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <a href="{{ $file->getFullUrl() }}" target="_blank"><i class="fa fa-paperclip mr-1"></i>{{ $file->name }}</a>
                <small class="text-muted">{{ $file->human_readable_size }}</small>
            </li>
            @endforeach
        </ul>
        @endif
    </div>
</div>
