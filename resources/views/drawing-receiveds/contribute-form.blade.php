@extends('layouts.master')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark text-weight-bold">Contribute to Drawing #{{ $drawingReceived->id }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('drawing-receiveds.engineer-dashboard') }}">My Drawings</a></li>
                    <li class="breadcrumb-item active">Contribute</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    @include('flash::message')
    
    <div class="row">
        <!-- Drawing Details -->
        <div class="col-md-4">
            <div class="bg-white card-primary card-maroon">
                <div class="card-header">
                    <h3 class="card-title">Drawing Details</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>LPOIN:</strong><br>
                        <a href="{{ route('lpoins.show', $drawingReceived->lpoin_id) }}" class="text-primary">
                            {{ $drawingReceived->lpoin->ref_no ?? 'N/A' }}
                        </a>
                    </div>
                    <div class="mb-3">
                        <strong>Type of Work:</strong><br>
                        {{ $drawingReceived->type_of_work }}
                    </div>
                    <div class="mb-3">
                        <strong>Current Status:</strong><br>
                        <span class="badge badge-info">{{ $drawingReceived->status }}</span>
                    </div>
                    <div class="mb-3">
                        <strong>Start Date:</strong><br>
                        {{ $drawingReceived->start_date }}
                    </div>
                    <div class="mb-3">
                        <strong>Total Contributions:</strong><br>
                        <span class="badge badge-secondary">{{ $drawingReceived->contributions->count() }}</span>
                    </div>
                    <div>
                        <strong>Notes:</strong><br>
                        <small class="text-muted">{!! nl2br(e($drawingReceived->notes ?? 'No notes')) !!}</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contribution Form -->
        <div class="col-md-8">
            <div class="bg-white card-primary card-maroon">
                <div class="card-header">
                    <h3 class="card-title">Submit Contribution</h3>
                </div>
                <div class="card-body">
                    {!! Form::open(['route' => ['drawing-receiveds.store-contribution', $drawingReceived->id], 'method' => 'POST', 'files' => true]) !!}

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            {!! Form::label('contribution_type', 'Contribution Type:') !!}
                            {!! Form::select('contribution_type', $contributionTypes, null, [
                                'class' => ($errors->has('contribution_type')) ? 'form-control is-invalid' : 'form-control',
                                'required' => true,
                                'placeholder' => 'Select type'
                            ]) !!}
                            @if ($errors->has('contribution_type'))
                                <span class="invalid-feedback">
                                    <strong>{{ $errors->first('contribution_type') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-group col-md-6">
                            {!! Form::label('status', 'New Status:') !!}
                            {!! Form::select('status', $statusOptions, null, [
                                'class' => ($errors->has('status')) ? 'form-control is-invalid' : 'form-control',
                                'required' => true,
                                'placeholder' => 'Select status'
                            ]) !!}
                            @if ($errors->has('status'))
                                <span class="invalid-feedback">
                                    <strong>{{ $errors->first('status') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('description', 'Description / Comments:') !!}
                        {!! Form::textarea('description', null, [
                            'class' => ($errors->has('description')) ? 'form-control is-invalid' : 'form-control',
                            'rows' => 5,
                            'placeholder' => 'Describe your contribution, updates, or any remarks...'
                        ]) !!}
                        @if ($errors->has('description'))
                            <span class="invalid-feedback">
                                <strong>{{ $errors->first('description') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group">
                        {!! Form::label('contribution_files', 'Upload Files (Optional):') !!}
                        <div class="custom-file">
                            {!! Form::file('contribution_files[]', [
                                'class' => 'custom-file-input',
                                'multiple' => true,
                                'accept' => '.pdf,.doc,.docx,.jpg,.jpeg,.png,.dwg'
                            ]) !!}
                            {!! Form::label('contribution_files', 'Choose Files', ['class' => 'custom-file-label']) !!}
                        </div>
                        <small class="form-text text-muted">
                            Allowed: PDF, DOC, DOCX, JPG, PNG, DWG (Max 20MB per file)
                        </small>
                    </div>

                    <div class="form-group">
                        {!! Form::submit('Submit Contribution', ['class' => 'btn btn-primary']) !!}
                        <a href="{{ route('drawing-receiveds.engineer-dashboard') }}" class="btn btn-secondary">Cancel</a>
                    </div>

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>

    <!-- Contribution History Timeline -->
    @if($contributions->count() > 0)
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="bg-white card-primary card-maroon">
                    <div class="card-header">
                        <h3 class="card-title">Recent Contributions</h3>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            @foreach($contributions as $contribution)
                                <div class="time-label">
                                    <span class="bg-primary">{{ $contribution->created_at->format('M d, Y') }}</span>
                                </div>
                                <div class="timeline-item">
                                    <span class="timeline-head bg-blue">
                                        <i class="fas fa-pencil-alt"></i>
                                    </span>
                                    <div class="timeline-body">
                                        <strong>{{ $contribution->contributedBy->name ?? 'Unknown' }}</strong> - 
                                        <span class="badge badge-info">{{ $contribution->contribution_type }}</span>
                                        <br>
                                        <small class="text-muted">{{ $contribution->created_at->format('H:i:s') }}</small>
                                        <p class="mt-2">
                                            {{ $contribution->description }}
                                        </p>
                                        @if($contribution->getMedia()->count() > 0)
                                            <div class="mt-2">
                                                <strong>Attachments:</strong>
                                                <ul class="list-unstyled">
                                                    @foreach($contribution->getMedia() as $media)
                                                        <li>
                                                            <a href="{{ $media->getUrl() }}" target="_blank">
                                                                <i class="fas fa-file"></i> {{ $media->file_name }}
                                                            </a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                        <div class="mt-2">
                                            <span class="badge" style="background-color: #17a2b8;">Status: {{ $contribution->status }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($contributions->lastPage() > 1)
            <div class="row mt-3">
                <div class="col-md-12">
                    {{ $contributions->links('pagination::bootstrap-4') }}
                </div>
            </div>
        @endif
    @endif
</div>

@section('scripts')
@parent
<script src="{{ asset('plugins/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>
<script src="{{ asset('plugins/select2/js/select2.full.min.js') }}"></script>

<script type="text/javascript">
    $(document).ready(function() {
        bsCustomFileInput.init();
        $('#contribution_type, #status').select2();
    });
</script>
@endsection
@endsection
