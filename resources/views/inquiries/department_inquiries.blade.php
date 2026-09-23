@extends('layouts.master')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark text-weight-bold">My Department Inquiries</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Department Inquiries</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<div class="content">
    @include('flash::message')
    <div class="bg-white card-primary card-maroon">
        <div class="card-header">
            <h3 class="card-title">Inquiries Assigned to My Department</h3>
        </div>
        <div class="card-body table-responsive">
            @include('inquiries.department_table')
        </div>
    </div>
</div>
@endsection
