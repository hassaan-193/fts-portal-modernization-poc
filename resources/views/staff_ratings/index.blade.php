@extends('layouts.master')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark text-weight-bold">Labor Staff Monthly Ratings</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                    <li class="breadcrumb-item active">Staff Ratings</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    @include('flash::message')

    <div class="bg-white card-primary card-maroon">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Staff Ratings - {{ \Carbon\Carbon::createFromDate($year, $month, 1)->format('F Y') }}</h3>

            <form method="GET" action="{{ route('staff-ratings.index') }}" class="form-inline">
                <input type="month" name="month_year" class="form-control form-control-sm" 
                       value="{{ sprintf('%04d-%02d', $year, $month) }}" onchange="this.form.submit()">
            </form>
        </div>

        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th>S.L No.</th>
                        <th>Labor Name</th>
                        <th style="min-width: 350px;">Rating / Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($staffList as $index => $staff)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $staff->name }}</td>
                        <td>
                            @if($userRatings->has($staff->id))
                            @php $rating = $userRatings[$staff->id]; @endphp
                            <div class="text-success mb-2 small" style="font-size: 0.85rem;">
                                <strong>Your Ratings:</strong>
                                <span class="ml-2">Safety: <strong>{{ $rating->safety_compliance }}</strong></span> |
                                <span class="ml-2">Communication: <strong>{{ $rating->communication }}</strong></span> |
                                <span class="ml-2">Attendance: <strong>{{ $rating->attendance }}</strong></span> |
                                <span class="ml-2">Time: <strong>{{ $rating->time_management }}</strong></span> |
                                <span class="ml-2">Responsibility: <strong>{{ $rating->job_responsibility }}</strong></span> |
                                <span class="ml-2">Material Handling: <strong>{{ $rating->material_handling }}</strong></span> |
                                <span class="ml-2">Document Handling: <strong>{{ $rating->document_handling }}</strong></span> |
                                <span class="ml-2">Competency: <strong>{{ $rating->competency }}</strong></span>
                            </div>
                            @else
                                <form method="POST" action="{{ route('staff-ratings.store', $staff->id) }}" class="row g-2 align-items-center">
                                    @csrf
                                    <input type="hidden" name="month" value="{{ $month }}">
                                    <input type="hidden" name="year" value="{{ $year }}">
                                    @php
                                        $fields = [
                                            'safety_compliance' => 'Safety',
                                            'communication' => 'Comm.',
                                            'attendance' => 'Attend.',
                                            'time_management' => 'Time',
                                            'job_responsibility' => 'Resp.',
                                            'material_handling' => 'Mat.',
                                            'document_handling' => 'Doc.',
                                            'competency' => 'Comp.'
                                        ];
                                    @endphp
                                    @foreach($fields as $name => $placeholder)
                                    <div class="col-auto">
                                        <input type="number" name="{{ $name }}" min="0" max="10" placeholder="{{ $placeholder }}"
                                            required class="form-control form-control-sm" style="width: 125px;" />
                                    </div>
                                    @endforeach
                                    <div class="col-auto">
                                        <button type="submit" class="btn btn-sm btn-success">Save</button>
                                    </div>
                                </form>
                            @endif

                            @if($monthlyReports->has($staff->id))
                                <div class="mt-2">
                                    <strong>Final Score:</strong> {{ $monthlyReports[$staff->id]->final_score }}
                                    <br>
                                    <small class="text-muted">Based on {{ $monthlyReports[$staff->id]->total_ratings }} rating(s)</small>
                                </div>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
