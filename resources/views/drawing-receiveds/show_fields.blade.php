<div class="row">
    <div class="col-md-4 col-sm-6">
        <!-- LPOIN Field -->
        <li class="callout callout-danger list-group-item mb-3 shadow">
            <b>@lang('models/drawing_receiveds.fields.lpoin_id')</b> <br>
            <a class="text-left text-primary" href="{{ route('lpoins.show', $drawingReceived->lpoin_id) }}">
                {{ $drawingReceived->lpoin->ref_no ?? 'N/A' }}
            </a>
        </li>
        <!-- Type of Work Field -->
        <li class="callout callout-danger list-group-item mb-3 shadow">
            <b>@lang('models/drawing_receiveds.fields.type_of_work')</b> <br>
            <a class="text-left">{{ $drawingReceived->type_of_work }}</a>
        </li>
        <!-- Start Date Field -->
        <li class="callout callout-danger list-group-item mb-3 shadow">
            <b>@lang('models/drawing_receiveds.fields.start_date')</b> <br>
            <a class="text-left">{{ $drawingReceived->start_date }}</a>
        </li>
    </div>
    <div class="col-md-4 col-sm-6">
        <!-- Responsible Engineer Field -->
        <li class="callout callout-danger list-group-item mb-3 shadow">
            <b>@lang('models/drawing_receiveds.fields.responsible_engineer_id')</b> <br>
            <a class="text-left">{{ $drawingReceived->responsibleEngineer->name ?? 'N/A' }}</a>
        </li>
        <!-- Status Field -->
        <li class="callout callout-danger list-group-item mb-3 shadow">
            <b>@lang('models/drawing_receiveds.fields.status')</b> <br>
            <span class="badge badge-info">{{ $drawingReceived->status }}</span>
        </li>
        <!-- Review Comments Date Field -->
        <li class="callout callout-danger list-group-item mb-3 shadow">
            <b>@lang('models/drawing_receiveds.fields.review_comments_date')</b> <br>
            <a class="text-left">{{ $drawingReceived->review_comments_date ?? 'N/A' }}</a>
        </li>
    </div>
    <div class="col-md-4 col-sm-6">
        <!-- Approval Date Field -->
        <li class="callout callout-danger list-group-item mb-3 shadow">
            <b>@lang('models/drawing_receiveds.fields.approval_date')</b> <br>
            <a class="text-left">{{ $drawingReceived->approval_date ?? 'N/A' }}</a>
        </li>
        <!-- Created At Field -->
        <li class="callout callout-danger list-group-item mb-3 shadow">
            <b>@lang('models/drawing_receiveds.fields.created_at')</b> <br>
            <a class="text-left">{{ $drawingReceived->created_at->format('Y-m-d H:i') }}</a>
        </li>
        <!-- Updated At Field -->
        <li class="callout callout-danger list-group-item mb-3 shadow">
            <b>@lang('models/drawing_receiveds.fields.updated_at')</b> <br>
            <a class="text-left">{{ $drawingReceived->updated_at->format('Y-m-d H:i') }}</a>
        </li>
    </div>
</div>

<!-- Notes Section -->
<div class="row mt-3">
    <div class="col-md-12">
        <li class="callout callout-info list-group-item mb-3 shadow">
            <b>@lang('models/drawing_receiveds.fields.notes')</b> <br>
            <a class="text-left">{!! nl2br(e($drawingReceived->notes ?? 'N/A')) !!}</a>
        </li>
    </div>
</div>
