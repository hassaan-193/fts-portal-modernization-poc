@extends('layouts.master')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark text-weight-bold">Contribution History - Drawing #{{ $drawingReceived->id }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('drawing-receiveds.index') }}">Drawings</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('drawing-receiveds.show', $drawingReceived->id) }}">Detail</a></li>
                    <li class="breadcrumb-item active">Contribution History</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    @include('flash::message')
    
    <div class="row">
        <!-- Drawing Summary -->
        <div class="col-md-3">
            <div class="bg-white card-primary card-maroon">
                <div class="card-header">
                    <h3 class="card-title">Drawing Summary</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>LPOIN:</strong><br>
                        <a href="{{ route('lpoins.show', $drawingReceived->lpoin_id) }}" class="text-primary">
                            {{ $drawingReceived->lpoin->ref_no ?? 'N/A' }}
                        </a>
                    </div>
                    <div class="mb-3">
                        <strong>Type:</strong><br>
                        {{ $drawingReceived->type_of_work }}
                    </div>
                    <div class="mb-3">
                        <strong>Engineer:</strong><br>
                        {{ $drawingReceived->responsibleEngineer->name ?? 'N/A' }}
                    </div>
                    <div class="mb-3">
                        <strong>Current Status:</strong><br>
                        <span class="badge badge-info badge-lg">{{ $drawingReceived->status }}</span>
                    </div>
                    <div class="mb-3">
                        <strong>Start Date:</strong><br>
                        {{ $drawingReceived->start_date }}
                    </div>
                    <div class="mb-3">
                        <strong>Total Contributors:</strong><br>
                        <span class="badge badge-secondary">
                            {{ $drawingReceived->contributions->pluck('contributed_by_id')->unique()->count() }}
                        </span>
                    </div>
                    <div class="mb-3">
                        <strong>Total Contributions:</strong><br>
                        <span class="badge badge-primary">{{ $contributions->total() }}</span>
                    </div>
                    <hr>
                    <a href="{{ route('drawing-receiveds.show', $drawingReceived->id) }}" class="btn btn-sm btn-primary btn-block">
                        <i class="fas fa-arrow-left"></i> Back to Drawing
                    </a>
                </div>
            </div>
        </div>

        <!-- Contributions Timeline -->
        <div class="col-md-9">
            <div class="bg-white card-primary card-maroon">
                <div class="card-header">
                    <h3 class="card-title">Contribution Timeline</h3>
                </div>
                <div class="card-body">
                    @if($contributions->count() > 0)
                        <div class="timeline timeline-inverse">
                            @foreach($contributions as $contribution)
                                @if($loop->first)
                                    <div class="time-label">
                                        <span class="bg-primary">{{ $contribution->created_at->format('M d, Y') }}</span>
                                    </div>
                                @elseif($contribution->created_at->format('Y-m-d') != $contributions[$loop->index - 1]->created_at->format('Y-m-d'))
                                    <div class="time-label">
                                        <span class="bg-primary">{{ $contribution->created_at->format('M d, Y') }}</span>
                                    </div>
                                @endif

                                <div class="timeline-item">
                                    <span class="timeline-head bg-blue">
                                        @if($contribution->contribution_type === 'Approved')
                                            <i class="fas fa-check-circle"></i>
                                        @elseif($contribution->contribution_type === 'Rejected')
                                            <i class="fas fa-times-circle"></i>
                                        @elseif($contribution->contribution_type === 'Comment Added')
                                            <i class="fas fa-comment"></i>
                                        @elseif($contribution->contribution_type === 'Revision Submitted')
                                            <i class="fas fa-redo"></i>
                                        @else
                                            <i class="fas fa-pencil-alt"></i>
                                        @endif
                                    </span>
                                    <div class="timeline-body">
                                        <h5 class="timeline-header">
                                            <strong>{{ $contribution->contributedBy->name ?? 'Unknown User' }}</strong>
                                            <span class="badge" 
                                                @if($contribution->contribution_type === 'Approved')
                                                    style="background-color: #28a745;"
                                                @elseif($contribution->contribution_type === 'Rejected')
                                                    style="background-color: #dc3545;"
                                                @else
                                                    style="background-color: #17a2b8;"
                                                @endif
                                            >
                                                {{ $contribution->contribution_type }}
                                            </span>
                                        </h5>
                                        <p class="timeline-title">
                                            {{ $contribution->created_at->format('Y-m-d H:i:s') }}
                                        </p>
                                        @if($contribution->description)
                                            <div class="timeline-text">
                                                {!! nl2br(e($contribution->description)) !!}
                                            </div>
                                        @endif
                                        <div class="mt-2">
                                            <strong>Status Set To:</strong>
                                            <span class="badge badge-info">{{ $contribution->status }}</span>
                                        </div>
                                        @if($contribution->getMedia()->count() > 0)
                                            <div class="mt-3">
                                                <strong class="d-block mb-2">
                                                    <i class="fas fa-paperclip"></i> Attached Files:
                                                </strong>
                                                <div class="pl-3">
                                                    @foreach($contribution->getMedia() as $media)
                                                        <div class="mb-2">
                                                            <a href="{{ $media->getUrl() }}" class="btn btn-sm btn-outline-primary" target="_blank">
                                                                <i class="fas fa-download"></i> {{ $media->file_name }}
                                                            </a>
                                                            <small class="text-muted ml-2">
                                                                ({{ $media->human_readable_size }})
                                                            </small>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if($contributions->lastPage() > 1)
                            <div class="mt-4">
                                {{ $contributions->links('pagination::bootstrap-4') }}
                            </div>
                        @endif
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> No contributions recorded yet.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@section('css')
<style>
    .timeline {
        position: relative;
        padding: 20px 0;
    }
    .timeline::before {
        content: '';
        position: absolute;
        left: 30px;
        top: 0;
        height: 100%;
        width: 4px;
        background: #dee2e6;
    }
    .timeline-item {
        margin-bottom: 20px;
        margin-left: 80px;
        position: relative;
    }
    .timeline-head {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        position: absolute;
        left: -80px;
        top: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 20px;
        border: 3px solid #fff;
    }
    .timeline-body {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 5px;
        padding: 15px;
    }
    .timeline-title {
        margin: 10px 0 5px 0;
        font-size: 12px;
        color: #999;
    }
    .timeline-header {
        margin: 0 0 10px 0;
    }
    .timeline-text {
        margin: 10px 0;
        font-size: 13px;
        line-height: 1.5;
    }
    .time-label {
        position: relative;
        background: #fff;
        text-align: center;
        margin: 20px 0;
    }
    .time-label > span {
        padding: 5px 15px;
        border-radius: 4px;
        color: white;
        font-size: 12px;
        font-weight: bold;
    }
</style>
@endsection
@endsection
