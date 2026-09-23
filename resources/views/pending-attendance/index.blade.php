@extends('layouts.master')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark text-weight-bold">Pending Attendance Approvals</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item active">Pending Attendance</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        @include('flash::message')
        
        <!-- Report Generation Card -->
        <div class="card card-info mb-3">
            <div class="card-header" style="background-color: #d81b60;">
                <h3 class="card-title"><i class="fas fa-file-excel"></i> Generate Absence Report</h3>
            </div>
            <div class="card-body">
                <form id="reportForm" class="report-form">
                    <div class="row">
                        <!-- Date From -->
                        <div class="col-md-2">
                            <label>Date From</label>
                            <input type="date" name="date_from" id="reportDateFrom" class="form-control form-control-sm">
                        </div>

                        <!-- Date To -->
                        <div class="col-md-2">
                            <label>Date To</label>
                            <input type="date" name="date_to" id="reportDateTo" class="form-control form-control-sm">
                        </div>

                        <!-- Labor Name -->
                        <div class="col-md-2">
                            <label>Labor Name</label>
                            <select name="labor_id" id="reportLaborId" class="form-control form-control-sm">
                                <option value="">-- All Labors --</option>
                                @foreach($laborers as $labor)
                                    <option value="{{ $labor->id }}">{{ $labor->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Status Filter -->
                        <div class="col-md-2">
                            <label>Status</label>
                            <select name="status" id="reportStatus" class="form-control form-control-sm">
                                <option value="">-- All Statuses --</option>
                                <option value="pending">Pending</option>
                                <option value="approved">Approved</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>

                        <!-- Informed Status Filter -->
                        <div class="col-md-2">
                            <label>Informed Status</label>
                            <select name="informed" id="reportInformed" class="form-control form-control-sm">
                                <option value="">-- All --</option>
                                <option value="informed">Informed</option>
                                <option value="uninformed">Uninformed</option>
                            </select>
                        </div>

                        <!-- Export Button -->
                        <div class="col-md-2">
                            <label>&nbsp;</label>
                            <button type="button" class="btn btn-success btn-sm btn-block" id="exportReportBtn">
                                <i class="fas fa-download"></i> Export to Excel
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Data Table Card -->
        <div class="bg-white card-primary card-maroon">
            <div class="card-header">
                <h3 class="card-title">Pending Attendance Detail</h3>
            </div>
            <div class="card-body table-responsive">
               @include('pending-attendance.table')
            </div>
        </div>
    </div>
@endsection