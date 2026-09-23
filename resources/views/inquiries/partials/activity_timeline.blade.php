<div class="card bg-white">
    <div class="card-header">
        <h3 class="card-title"><i class="fa fa-history mr-1"></i> Activity Timeline</h3>
    </div>
    <div class="card-body p-0" style="max-height:600px; overflow-y:auto;">
        @forelse($activities as $activity)
        <div class="d-flex align-items-start px-3 py-2 border-bottom">
            <div class="mr-2 mt-1">
                @php
                    $iconMap = [
                        'inquiry_created'          => ['icon' => 'plus-circle',     'color' => 'text-success'],
                        'status_changed'            => ['icon' => 'exchange-alt',    'color' => 'text-primary'],
                        'assigned'                  => ['icon' => 'user-check',      'color' => 'text-info'],
                        'department_review_submitted'=> ['icon' => 'clipboard-check','color' => 'text-primary'],
                        'site_visit_scheduled'      => ['icon' => 'calendar-check',  'color' => 'text-warning'],
                        'engineer_report_submitted' => ['icon' => 'hard-hat',        'color' => 'text-dark'],
                        'sent_to_sales'             => ['icon' => 'paper-plane',     'color' => 'text-warning'],
                        'quotation_created'         => ['icon' => 'file-invoice',    'color' => 'text-success'],
                        'follow_up_added'           => ['icon' => 'comments',        'color' => 'text-info'],
                        'file_uploaded'             => ['icon' => 'paperclip',       'color' => 'text-secondary'],
                        'note_added'                => ['icon' => 'sticky-note',     'color' => 'text-muted'],
                    ];
                    $icon  = $iconMap[$activity->action]['icon']  ?? 'circle';
                    $color = $iconMap[$activity->action]['color'] ?? 'text-muted';
                @endphp
                <i class="fas fa-{{ $icon }} {{ $color }}"></i>
            </div>
            <div class="flex-grow-1">
                <p class="mb-0 text-sm">{{ $activity->description }}</p>
                <small class="text-muted">
                    {{ optional($activity->user)->name ?? 'System' }} &middot;
                    {{ $activity->created_at->diffForHumans() }}
                </small>
            </div>
        </div>
        @empty
        <div class="p-3 text-muted text-center">No activity yet.</div>
        @endforelse
    </div>
</div>
