<div class='btn-group'>
    <a href="{{ route('inquiries.show', $inquiry->id) }}" class='btn btn-success btn-sm' title="View">
        <i class="fa fa-eye"></i>
    </a>
    @if(in_array($inquiry->status, ['Quotation Created', 'Under Follow-up', 'Won', 'Lost']))
    <a href="{{ route('inquiries.show', $inquiry->id) }}" class='btn btn-warning btn-sm' title="Add Follow-up">
        <i class="fa fa-comment"></i>
    </a>
    @endif
</div>
