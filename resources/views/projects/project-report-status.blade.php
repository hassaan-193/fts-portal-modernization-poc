@extends('layouts.master')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark font-weight-bold">Manage Project Report Status</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item active">Project Report Status</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        @include('flash::message')

        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">Filter Reports</h3>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('projects.reportStatus') }}">
                    <div class="form-row">
                        <div class="col-md-4 mb-3">
                            <label for="company_id">Select Company</label>
                            <select id="company_id" name="company_id" class="form-control form-control-sm select2">
                                <option value="">All Companies</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>
                                        {{ $company->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="project_id">Select Project</label>
                            <select id="project_id" name="project_id" class="form-control form-control-sm" {{ request('company_id') ? '' : 'disabled' }}>
                                <option value="">All Projects</option>
                            </select>
                        </div>

                        <div class="col-md-2 mb-3">
                            <label for="status">Select Status</label>
                            <select id="status" name="status" class="form-control form-control-sm">
                                <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All</option>
                                <option value="pending" {{ request('status', 'pending') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="disapproved" {{ request('status') == 'disapproved' ? 'selected' : '' }}>Disapproved</option>
                            </select>
                        </div>

                        <div class="col-md-2 d-flex align-items-end mb-3">
                            <button type="submit" class="btn btn-sm btn-primary w-100">Filter</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card card-maroon">
            <div class="card-header">
                <h3 class="card-title">Project Reports</h3>
            </div>

            <div class="card-body table-responsive">
                @if($reports && count($reports))
                    <table id="reportsTable" class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <th>Reference No</th>
                                <th>Visit Date</th>
                                <th>Report Date</th>
                                <th>Company</th>
                                <th>Status</th>
                                <th>View</th>
                                <th>Edit</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reports as $report)
                                <tr>
                                    <td>{{ $report->reference_number ?? 'N/A' }}</td>
                                    <td>
                                        @if($report->is_emergency_visit)
                                            {{ $report->emergency_visit_date ? \Carbon\Carbon::parse($report->emergency_visit_date)->format('Y-m-d') : 'N/A' }}
                                            <span class="badge badge-warning">Emergency</span>
                                        @else
                                            {{ $report->visitSchedule->visit_date ?? 'N/A' }}
                                        @endif
                                    </td>
                                    <td>{{ $report->date }}</td>
                                    <td>{{ $report->company->name ?? $report->manual_client_name ?? 'N/A' }}</td>
                                    <td>
                                        @php
                                            if ($report->status === 'approved') {
                                                $badgeClass = 'badge-success';
                                            } elseif ($report->status === 'disapproved') {
                                                $badgeClass = 'badge-danger';
                                            } else {
                                                $badgeClass = 'badge-secondary';
                                            }
                                        @endphp

                                        <span class="badge {{ $badgeClass }}">{{ ucfirst($report->status) }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('projects.viewReport', $report->id) }}" class="btn btn-sm btn-info">View</a>
                                    </td>
                                    <td>
                                        <a href="{{ route('projects.editReport', $report->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                    </td>
                                    <td>
                                        <form action="{{ route('projects.updateReportStatus', $report->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" name="status" value="approved" class="btn btn-sm btn-success">Approve</button>
                                            <button type="submit" name="status" value="disapproved" class="btn btn-sm btn-danger">Disapprove</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-muted">No reports found.</p>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
@endpush


@section('scripts')
@parent

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#reportsTable').DataTable();
            
            const companySelect = $('#company_id');
            const projectSelect = $('#project_id');
            const selectedProjectId = "{{ request('project_id') }}";

            function loadProjects(companyId) {
                projectSelect.prop('disabled', true).html('<option value="">Loading...</option>');
                fetch(`/projects/get-projects-by-company?company_id=${companyId}`)
                    .then(response => response.json())
                    .then(projects => {
                        projectSelect.html('<option value="">All Projects</option>');
                        projects.forEach(project => {
                            const selected = project.id == selectedProjectId ? 'selected' : '';
                            projectSelect.append(`<option value="${project.id}" ${selected}>${project.subject}</option>`);
                        });
                        projectSelect.prop('disabled', false);
                    })
                    .catch(() => {
                        projectSelect.html('<option value="">Failed to load projects</option>');
                        projectSelect.prop('disabled', true);
                    });
            }

            if (companySelect.val()) {
                loadProjects(companySelect.val());
            }

            companySelect.on('change', function () {
                const companyId = $(this).val();
                if (companyId) {
                    loadProjects(companyId);
                } else {
                    projectSelect.html('<option value="">All Projects</option>').prop('disabled', true);
                }
            });



        });
    </script>
    <script src="{{ asset('plugins/select2/js/select2.full.min.js')}}"></script>

    <script>
            $('#company_id').select2({
                theme: 'bootstrap4',
                placeholder: "Select Company",
                allowClear: true
            });
            $('#project_id').select2({
                theme: 'bootstrap4',
                placeholder: "Select Project",
                allowClear: true
            })
    </script>
@endsection

