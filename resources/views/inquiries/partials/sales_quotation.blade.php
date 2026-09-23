<div class="card bg-white mb-3">
    <div class="card-header">
        <h3 class="card-title"><i class="fa fa-file-invoice-dollar mr-1"></i> Sales Quotation</h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-sm table-borderless">
                    <tr>
                        <th width="160">Quotation Amount</th>
                        <td class="font-weight-bold text-success">AED {{ number_format($quotation->quotation_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <th>Validity Date</th>
                        <td>{{ $quotation->validity_date ? $quotation->validity_date->format('d M Y') : '—' }}</td>
                    </tr>
                    <tr>
                        <th>Created By</th>
                        <td>{{ optional($quotation->creator)->name ?? '—' }}</td>
                    </tr>
                    <tr>
                        <th>Created At</th>
                        <td>{{ $quotation->created_at->format('d M Y') }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                @if($quotation->scope_of_work)
                <p><strong>Scope of Work:</strong><br>{{ $quotation->scope_of_work }}</p>
                @endif
                @if($quotation->terms_conditions)
                <p><strong>Terms & Conditions:</strong><br>{{ $quotation->terms_conditions }}</p>
                @endif
            </div>
        </div>

        {{-- Quotation files --}}
        @if($quotation->getMedia('quotation')->isNotEmpty())
        <hr>
        <strong>Quotation Documents:</strong>
        <ul class="list-group mt-2">
            @foreach($quotation->getMedia('quotation') as $file)
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <a href="{{ $file->getFullUrl() }}" target="_blank"><i class="fa fa-file-pdf mr-1 text-danger"></i>{{ $file->name }}</a>
                <small class="text-muted">{{ $file->human_readable_size }}</small>
            </li>
            @endforeach
        </ul>
        @endif
    </div>
</div>
