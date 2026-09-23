@extends('layouts.master')

@section('css')
    @parent
    @include('layouts.datatables_css')
    @include('reports.page_style')
@endsection

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark text-weight-bold">Request Approvals</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item active">Request Approvals</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        @include('flash::message')

        @foreach ($types as $type)
            @unless (\App\Services\RequestApprovalService::isRosterValid($type))
                <div class="alert alert-warning">
                    <i class="fa fa-exclamation-triangle"></i>
                    Fewer than {{ \App\Services\RequestApprovalService::MIN_APPROVERS }} users hold the
                    <code>{{ \App\Services\RequestApprovalService::permission($type) }}</code> permission, so
                    <strong>{{ \App\Services\RequestApprovalService::typeLabel($type) }}</strong> requests can be
                    disapproved but cannot reach <strong>Approved</strong>. Assign the
                    "{{ \App\Services\RequestApprovalService::typeLabel($type) }} Request Approver" role to both
                    approvers from <a href="{{ url('/users') }}">Users</a>.
                </div>
            @endunless
        @endforeach

        <div class="bg-white card-primary card-maroon">
            <div class="card-header">
                <h3 class="card-title">
                    {{ $filter === 'pending' ? 'Awaiting Approval' : 'All Requests' }}
                </h3>
                <div class="card-tools">
                    <a href="{{ route('request_approvals.index', ['filter' => 'pending']) }}"
                        class="btn btn-flat btn-sm {{ $filter === 'pending' ? 'btn-danger' : 'btn-outline-danger' }}">Under
                        Review</a>
                    <a href="{{ route('request_approvals.index', ['filter' => 'all']) }}"
                        class="btn btn-flat btn-sm {{ $filter === 'all' ? 'btn-danger' : 'btn-outline-danger' }}">All</a>
                </div>
            </div>

            <div class="card-body table-responsive">
                <input type="hidden" name="filter" value="{{ $filter }}">

                <div class="mb-2 small text-muted">
                    @foreach ($types as $type)
                        <strong>{{ \App\Services\RequestApprovalService::typeLabel($type) }}</strong> requires:
                        {{ \App\Services\RequestApprovalService::roster($type)->pluck('name')->implode(', ') ?: 'nobody assigned' }}
                        @if (!$loop->last)
                            &nbsp;&middot;&nbsp;
                        @endif
                    @endforeach
                </div>

                {!! $dataTable->table([
                    'width' => '100%',
                    'class' => 'table table-hover table-bordered table-striped table-sm text-nowrap',
                ]) !!}
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @parent
    @include('layouts.datatables_js')
    {!! $dataTable->scripts() !!}
@endsection
