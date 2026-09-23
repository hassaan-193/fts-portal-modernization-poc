@extends('layouts.master')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">
                    Vendor Payable #{{ $payable->id }}
                    @if($payable->status === 1)
                        <span class="badge badge-success ml-2">Paid</span>
                    @elseif($payable->status === -1)
                        <span class="badge badge-secondary ml-2">Cancelled</span>
                    @elseif($payable->source_date && $payable->source_date->isPast())
                        <span class="badge badge-danger ml-2">Overdue</span>
                    @else
                        <span class="badge badge-warning ml-2">Pending</span>
                    @endif
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('vendor-payables.index') }}">Vendor Payables</a></li>
                    <li class="breadcrumb-item active">#{{ $payable->id }}</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    @include('flash::message')

    @if($payable->status === -1)
    <div class="alert alert-secondary">
        <i class="fa fa-ban mr-1"></i> This payable has been <strong>cancelled</strong> and is excluded from the payment queue.
    </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card card-primary card-maroon">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Payable Details</h3>
                    <span class="badge badge-info">{{ $payable->is_historical ? 'Vendor Payable' : 'LPO-Generated' }}</span>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-sm">
                        <tr>
                            <th style="width:200px">Vendor</th>
                            <td>
                                {{ optional($payable->vendor)->name ?? $payable->historical_party ?? '—' }}
                                @if(!$payable->vendor_id && $payable->historical_party)
                                    <span class="badge badge-warning ml-1" title="Vendor not linked — edit to link">Unlinked</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Project</th>
                            <td>{{ optional($payable->project)->subject ?? $payable->historical_project ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th>Amount</th>
                            <td><strong>AED {{ number_format($payable->total_amount, 2) }}</strong></td>
                        </tr>
                        <tr>
                            <th>Due Date</th>
                            <td>
                                {{ $payable->source_date ? $payable->source_date->format('d M Y') : '—' }}
                                @if($payable->status === 0 && $payable->source_date && $payable->source_date->isPast())
                                    <span class="badge badge-danger ml-1">Overdue by {{ $payable->source_date->diffForHumans() }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Reference No</th>
                            <td>{{ $payable->invoice_no ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th>Source Reference</th>
                            <td>{{ $payable->source_reference ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th>Notes</th>
                            <td>{{ $payable->note ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th>Payment Status</th>
                            <td>
                                @if($payable->status === 1)
                                    <span class="text-success"><i class="fa fa-check-circle"></i> Paid</span>
                                    @if($payable->transaction)
                                        — via {{ optional($payable->transaction->transaction_payment_type)->name ?? 'payment' }}
                                        on {{ \Carbon\Carbon::parse($payable->transaction->date_time)->format('d M Y') }}
                                    @endif
                                @elseif($payable->status === -1)
                                    <span class="text-secondary"><i class="fa fa-ban"></i> Cancelled</span>
                                @else
                                    <span class="text-warning"><i class="fa fa-clock-o"></i> Pending — will appear in payment queue under this vendor</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Recorded By</th>
                            <td>{{ optional($payable->createdBy)->name ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th>Recorded At</th>
                            <td>{{ $payable->created_at->format('d M Y H:i') }}</td>
                        </tr>
                        @if($payable->document_path)
                        <tr>
                            <th>Supporting Document</th>
                            <td>
                                <a href="{{ Storage::url($payable->document_path) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                    <i class="fa fa-file-o"></i> View Document
                                </a>
                            </td>
                        </tr>
                        @endif
                    </table>

                    <div class="mt-3 d-flex align-items-center flex-wrap" style="gap:8px">
                        <a href="{{ route('vendor-payables.index') }}" class="btn btn-outline-secondary">
                            <i class="fa fa-arrow-left"></i> Back
                        </a>

                        @if($payable->status === 0)
                        <a href="{{ route('vendor-payables.edit', $payable->id) }}" class="btn btn-warning">
                            <i class="fa fa-edit"></i> Edit
                        </a>
                        <form action="{{ route('vendor-payables.cancel', $payable->id) }}" method="POST" class="d-inline"
                            onsubmit="return confirm('Cancel this payable? It will be removed from the payment queue.')">
                            @csrf @method('PATCH')
                            <button class="btn btn-secondary"><i class="fa fa-ban"></i> Cancel Payable</button>
                        </form>
                        <form action="{{ route('vendor-payables.destroy', $payable->id) }}" method="POST" class="d-inline"
                            onsubmit="return confirm('Permanently delete this payable?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-outline-danger"><i class="fa fa-trash"></i> Delete</button>
                        </form>
                        @elseif($payable->status === -1)
                        <form action="{{ route('vendor-payables.destroy', $payable->id) }}" method="POST" class="d-inline"
                            onsubmit="return confirm('Permanently delete this cancelled payable?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-outline-danger"><i class="fa fa-trash"></i> Delete</button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
