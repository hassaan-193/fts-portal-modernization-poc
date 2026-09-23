@extends('layouts.master')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark text-weight-bold">
                       Leave Period Details
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('staf-dates.index') }}">Staff Dates</a></li>
                        <li class="breadcrumb-item active">View</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="card card-primary card-maroon">
            <div class="card-header">
                <h3 class="card-title">Leave Period Information</h3>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><strong>Staff Member:</strong></label>
                            <p>{{ $stafDate->staff->name ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label><strong>Leave Start Date:</strong></label>
                            <p>{{ $stafDate->start_date ? \Carbon\Carbon::parse($stafDate->start_date)->format('d M Y') : 'N/A' }}</p>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label><strong>Joining Date After Leave:</strong></label>
                            <p>{{ $stafDate->end_date ? \Carbon\Carbon::parse($stafDate->end_date)->format('d M Y') : 'N/A' }}</p>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label><strong>Total Leave Days:</strong></label>
                            <p>{{ $stafDate->days ?? 0 }} days</p>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group">
                            <label><strong>Created On:</strong></label>
                            <p>{{ $stafDate->created_at->format('d M Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <a href="{{ route('staf-dates.edit', $stafDate->id) }}" class="btn btn-primary btn-flat">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="{{ route('staf-dates.index') }}" class="btn btn-secondary btn-flat">Back</a>
            </div>
        </div>
    </div>
@endsection
