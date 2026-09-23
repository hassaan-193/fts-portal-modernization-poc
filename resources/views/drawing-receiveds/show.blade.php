@extends('layouts.master')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark text-weight-bold">@lang('crud.detail') @lang('models/drawing_receiveds.singular')</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('drawing-receiveds.index') }}">@lang('models/drawing_receiveds.plural')</a></li>
                        <li class="breadcrumb-item active">@lang('crud.detail')</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        @include('flash::message')
        <div class="row">
            <div class="col-md-12">
                <div class="bg-white card-primary card-maroon">
                    <div class="card-header">
                        <h3 class="card-title">@lang('models/drawing_receiveds.singular') Details</h3>
                        <div class="card-tools pull-right">
                            <a href="{{ route('drawing-receiveds.edit', $drawingReceived->id) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-edit"></i> @lang('crud.edit')
                            </a>
                            <a href="{{ route('drawing-receiveds.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-unbordered mb-3">
                            @include('drawing-receiveds.show_fields')
                        </ul>
                    </div>
                    <div class="card-body">
                        @include('components.model_files', ['model' => $drawingReceived])
                    </div>
                    
                    <!-- Contributions Summary Section -->
                    @if($drawingReceived->contributions->count() > 0)
                    <div class="card-body">
                        <h5 class="mb-3">
                            <i class="fas fa-history"></i> Contribution Summary
                            <a href="{{ route('drawing-receiveds.contributions', $drawingReceived->id) }}" class="btn btn-sm btn-info float-right">
                                View All Contributions
                            </a>
                        </h5>
                        
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Contributed By</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Files</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($drawingReceived->contributions->take(5) as $contribution)
                                        <tr>
                                            <td>{{ $contribution->created_at->format('Y-m-d H:i') }}</td>
                                            <td>{{ $contribution->contributedBy->name ?? 'Unknown' }}</td>
                                            <td><span class="badge badge-info">{{ $contribution->contribution_type }}</span></td>
                                            <td><span class="badge badge-success">{{ $contribution->status }}</span></td>
                                            <td>
                                                @if($contribution->getMedia()->count() > 0)
                                                    <span class="badge badge-secondary">{{ $contribution->getMedia()->count() }} file(s)</span>
                                                @else
                                                    <small class="text-muted">-</small>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @else
                    <div class="card-body">
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle"></i> No contributions recorded yet. 
                            @if(auth()->user()->id == $drawingReceived->responsible_engineer_id)
                                <a href="{{ route('drawing-receiveds.contribute-form', $drawingReceived->id) }}">Submit your contribution</a>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection