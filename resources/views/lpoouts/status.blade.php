@extends('layouts.master')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark font-weight-bold">Manage LPO-Out Status</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item active">LPO-Out Status</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">

        @include('flash::message')

        <!-- FILTERS -->
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">Filter LPO-Out Records</h3>
            </div>

            <div class="card-body">
                <form method="GET" action="{{ route('lpoouts.manageStatus') }}">

                    <div class="form-row">

                        <div class="col-md-4 mb-3">
                            <label>Select Vendor</label>
                            <select name="vendor_id" id="vendor_id" class="form-control form-control-sm select2">
                                <option value="">All Vendors</option>
                                @foreach($vendors as $vendor)
                                    <option value="{{ $vendor->id }}" 
                                        {{ request('vendor_id') == $vendor->id ? 'selected' : '' }}>
                                        {{ $vendor->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label>Select Status</label>
                            <select name="status" class="form-control form-control-sm">
                                <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All</option>
                                <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                                <option value="Approved" {{ request('status') == 'Approved' ? 'selected' : '' }}>Approved</option>
                                <option value="Not Approved" {{ request('status') == 'Not Approved' ? 'selected' : '' }}>Not Approved</option>
                            </select>
                        </div>

                        <div class="col-md-2 mb-3">
                            <label>Date From</label>
                            <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control form-control-sm">
                        </div>

                        <div class="col-md-2 mb-3">
                            <label>Date To</label>
                            <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control form-control-sm">
                        </div>

                        <div class="col-md-1 d-flex align-items-end mb-3">
                            <button type="submit" class="btn btn-sm btn-primary w-100">Apply</button>
                        </div>

                    </div>

                </form>
            </div>
        </div>

        <!-- TABLE -->
        <div class="card card-maroon">
            <div class="card-header">
                <h3 class="card-title">LPO-Out Records</h3>
            </div>

            <div class="card-body table-responsive">
                <table id="lpoTable" class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th>Invoice No</th>
                            <th>Name</th>
                            <th>Vendor</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Approve</th>
                            <th>Disapprove</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lpoouts as $lpo)
                            <tr>
                                <td>{{ $lpo->lpo_invoice_no }}</td>
                                <td>{{ $lpo->name }}</td>
                                <td>{{ $lpo->vendor->name ?? 'N/A' }}</td>
                                <td>{{ $lpo->date }}</td>
                                <td>{{ number_format($lpo->total_amount, 2) }}</td>

                                <td>
                                    @php
                                        $badge = $lpo->status === 'Approved' ? 'badge-success' :
                                                 ($lpo->status === 'Not Approved' ? 'badge-danger' : 'badge-secondary');
                                    @endphp

                                    <span class="badge {{ $badge }}">{{ $lpo->status }}</span>
                                </td>

                                <td>
                                    <form action="{{ route('lpoouts.approve', $lpo->id) }}" method="POST">
                                        @csrf
                                        <button class="btn btn-sm btn-success"
                                            {{ $lpo->status == 'Approved' ? 'disabled' : '' }}>
                                            Approve
                                        </button>
                                    </form>
                                </td>

                                <td>
                                    <form action="{{ route('lpoouts.disapprove', $lpo->id) }}" method="POST">
                                        @csrf
                                        <button class="btn btn-sm btn-danger"
                                            {{ $lpo->status == 'Not Approved' ? 'disabled' : '' }}>
                                            Disapprove
                                        </button>
                                    </form>
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
        </div>

    </div>
@endsection

@push('styles')
 <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">

@endpush

@section('scripts')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script><script>
$(function () {
    $('#lpoTable').DataTable({
        "pagingType": "simple_numbers", // nicer pagination style
        "lengthMenu": [10, 25, 50, 100], // control page length options
        "order": [], // disable initial sorting if needed
        "language": {
            "search": "_INPUT_",
            "searchPlaceholder": "Search records",
            "paginate": {
                "previous": "&laquo;",
                "next": "&raquo;"
            }
        }
    });

    $('#vendor_id').select2({
        theme: 'bootstrap4',
        width: '100%',
    });
});

</script>
@endsection
