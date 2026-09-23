@extends('layouts.master')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark text-weight-bold">
                        {{ $row['type_label'] }} Request #{{ $row['id'] }}
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('request_approvals.index') }}">Request
                                Approvals</a></li>
                        <li class="breadcrumb-item active">#{{ $row['id'] }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        @include('flash::message')

        <div class="row">
            <div class="col-md-8">
                <div class="bg-white card-primary card-maroon">
                    <div class="card-header">
                        <h3 class="card-title">Request Details</h3>
                        <div class="card-tools">
                            {!! \App\Services\RequestApprovalService::statusBadge($row['status']) !!}
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-sm mb-0">
                            <tbody>
                                <tr>
                                    <th style="width: 200px;">Side</th>
                                    <td>{{ $row['type_label'] }}</td>
                                </tr>
                                <tr>
                                    <th>Requester</th>
                                    <td>{{ $row['requester'] }}</td>
                                </tr>
                                <tr>
                                    <th>Request Type</th>
                                    <td>{{ $row['request_type'] }}</td>
                                </tr>
                                <tr>
                                    <th>Start Date</th>
                                    <td>{{ $row['start_date'] ?: '—' }}</td>
                                </tr>
                                <tr>
                                    <th>End Date</th>
                                    <td>{{ $row['end_date'] ?: '—' }}</td>
                                </tr>
                                <tr>
                                    <th>Advance Money</th>
                                    <td>{{ $row['advance_money'] ?: '—' }}</td>
                                </tr>
                                <tr>
                                    <th>Submitted</th>
                                    <td>{{ $row['created_at'] }}</td>
                                </tr>
                                <tr>
                                    <th>Note</th>
                                    <td style="white-space: pre-wrap;">{{ $row['note'] ?: '—' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="bg-white card-primary card-maroon">
                    <div class="card-header">
                        <h3 class="card-title">Approvals</h3>
                    </div>
                    <div class="card-body">
                        @forelse ($row['progress'] as $slot)
                            @php
                                if ($slot['decision'] === 1) {
                                    $cls = 'success'; $txt = 'Approved';
                                } elseif ($slot['decision'] === 2) {
                                    $cls = 'danger'; $txt = 'Disapproved';
                                } else {
                                    $cls = 'warning'; $txt = 'Pending';
                                }
                            @endphp
                            <div class="mb-3">
                                <strong>{{ $slot['label'] }}</strong>
                                <span class="badge badge-{{ $cls }} float-right">{{ $txt }}</span>
                                @if ($slot['decided_at'])
                                    <div class="small text-muted">{{ $slot['decided_at'] }}</div>
                                @endif
                                @if (!empty($slot['note']))
                                    <div class="small" style="white-space: pre-wrap;">“{{ $slot['note'] }}”</div>
                                @endif
                            </div>
                        @empty
                            <p class="text-muted mb-0">Nobody currently holds the approval permission for this side.</p>
                        @endforelse
                    </div>

                    @if ($row['can_decide'])
                        <div class="card-footer">
                            <form action="{{ route('request_approvals.approve', [$row['type'], $row['id']]) }}"
                                method="POST" class="mb-2">
                                @csrf
                                <input type="text" name="note" class="form-control form-control-sm mb-2"
                                    placeholder="Note (optional)">
                                <button class="btn btn-primary btn-flat"
                                    {{ $row['own_decision'] === 1 ? 'disabled' : '' }}>Approve</button>
                            </form>
                            <form action="{{ route('request_approvals.disapprove', [$row['type'], $row['id']]) }}"
                                method="POST">
                                @csrf
                                <button class="btn btn-danger btn-flat"
                                    {{ $row['own_decision'] === 2 ? 'disabled' : '' }}>Disapprove</button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <a href="{{ route('request_approvals.index') }}" class="btn btn-outline-danger btn-flat">Back</a>
    </div>
@endsection
