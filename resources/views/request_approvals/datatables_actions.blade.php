<div class='btn-group'>
    <a href="{{ route('request_approvals.show', [$type, $id]) }}" class='btn btn-success' title="View">
        <i class="fa fa-eye"></i>
    </a>

    @if ($can_decide)
        <form action="{{ route('request_approvals.approve', [$type, $id]) }}" method="POST" class="d-inline">
            @csrf
            <button class="btn btn-primary" title="Approve" {{ $own_decision === 1 ? 'disabled' : '' }}>
                Approve
            </button>
        </form>

        <form action="{{ route('request_approvals.disapprove', [$type, $id]) }}" method="POST" class="d-inline">
            @csrf
            <button class="btn btn-danger" title="Disapprove" {{ $own_decision === 2 ? 'disabled' : '' }}>
                Disapprove
            </button>
        </form>
    @endif
</div>
