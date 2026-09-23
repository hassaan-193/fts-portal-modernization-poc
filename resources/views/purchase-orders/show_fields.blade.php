<li class="list-group-item">
    <b>Request Number</b>
    <a class="float-right">{{ $po->request_number }}</a>
</li>
<li class="list-group-item">
    <b>Request Type</b>
    <a class="float-right">{{ ucfirst($po->request_type) }}</a>
</li>
<li class="list-group-item">
    <b>Quotation</b>
    <a class="float-right">
        @if(isset($po->quotation_id) && $po->quotation_id)
            @php
                $quotation = \App\Models\Quotation::find($po->quotation_id);
            @endphp
            {{ $quotation ? $quotation->name : '-' }}
        @else
            -
        @endif
    </a>
</li>
<li class="list-group-item">
    <b>Company</b>
    <a class="float-right">{{ $po->getCompanyName() }}</a>
</li>
<li class="list-group-item">
    <b>Vendor</b>
    <a class="float-right">
        @if($po->lpout_vendor_id && $po->vendor && $po->vendor->id)
            {{ $po->vendor->name }}
        @else
            -
        @endif
    </a>
</li>
<li class="list-group-item">
    <b>TRN No (Company)</b>
    <a class="float-right">{{ config('purchase-orders.company_trn') }}</a>
</li>
<li class="list-group-item">
    <b>Kindly Attn</b>
    <a class="float-right">{{ $po->lpout_kindly_attn ?? '-' }}</a>
</li>
<li class="list-group-item">
    <b>LPOUT Date</b>
    <a class="float-right">{{ $po->lpout_date ? $po->lpout_date->format('Y-m-d') : '-' }}</a>
</li>
<li class="list-group-item">
    <b>Payment Type</b>
    <a class="float-right">{{ $po->lpout_payment_type ?? '-' }}</a>
</li>
@if($po->lpout_payment_type === 'Cheque')
<li class="list-group-item">
    <b>Cheque Date</b>
    <a class="float-right">{{ $po->lpout_cheque_date ? $po->lpout_cheque_date->format('Y-m-d') : '-' }}</a>
</li>
@endif
<li class="list-group-item">
    <b>Date</b>
    <a class="float-right">{{ $po->date->format('Y-m-d') ?? '-' }}</a>
</li>
<li class="list-group-item">
    <b>Other Information</b>
    <a class="float-right">{{ $po->other_info ?? '-' }}</a>
</li>
<li class="list-group-item">
    <b>Status</b>
    <a class="float-right"><span class="badge badge-primary">{{ $po->status }}</span></a>
</li>
<li class="list-group-item">
    <b>Items</b>
    <div class="float">
        @php
            // Lump-sum orders have no per-row cost, so those columns are dropped.
            $isLump = $po->isLumpSum();
            $showItemCode = $po->hasItemCode();
            $columnCount = 2 + ($showItemCode ? 1 : 0) + ($isLump ? 0 : 2);
        @endphp
        <table class="table table-sm table-bordered">
            <thead>
                <tr>
                    @if($showItemCode)
                        <th>Item Code</th>
                    @endif
                    <th>Material</th>
                    <th>Qty</th>
                    @unless($isLump)
                        <th>Cost Per Unit (AED)</th>
                        <th>Total Cost (AED)</th>
                    @endunless
                </tr>
            </thead>
            <tbody>
                @if($po->items)
                    @php
                        $items = is_string($po->items) ? json_decode($po->items, true) : $po->items;
                    @endphp
                    @if(is_array($items) && count($items) > 0)
                        @foreach($items as $item)
                            @if(is_array($item) && (!empty($item['material_name']) || !empty($item['quantity'])))
                                <tr>
                                    @if($showItemCode)
                                        <td>{{ $item['item_code'] ?? '-' }}</td>
                                    @endif
                                    <td >{{ $item['material_name'] ?? '-' }}</td>
                                    <td>{{ $item['quantity'] ?? '-' }}</td>
                                    @unless($isLump)
                                        <td>{{ isset($item['cost']) && $item['cost'] !== '' ? number_format($item['cost'], 2) : '-' }}</td>
                                        <td>{{ isset($item['total']) && $item['total'] !== '' ? number_format($item['total'], 2) : '-' }}</td>
                                    @endunless
                                </tr>
                            @endif
                        @endforeach
                    @else
                        <tr>
                            <td colspan="{{ $columnCount }}" class="text-center">No items</td>
                        </tr>
                    @endif
                @else
                    <tr>
                        <td colspan="{{ $columnCount }}" class="text-center">No items</td>
                    </tr>
                @endif
            </tbody>
        </table>
        @if($isLump && $po->lpout_manual_total)
            <div class="mt-2"><strong>Lump-Sum Total (AED):</strong> {{ number_format($po->lpout_manual_total, 2) }}</div>
        @endif
    </div>
</li>

<li class="list-group-item">
    <b>Attached Files</b>
    <div class="float">
        @php $files = $po->getMedia(); @endphp
        @if($files->count() > 0)
            <ul class="list-group list-group-unbordered">
                @foreach($files as $file)
                    <li class="list-group-item">
                        <i class="fa fa-file"></i>
                        <a href="{{ url('storage/' . $file->id . '/' . $file->file_name) }}" target="_blank">{{ $file->name }}</a>
                        <small class="text-muted">({{ strtoupper($file->mime_type) }})</small>
                        <a href="{{ url('storage/' . $file->id . '/' . $file->file_name) }}" download class="btn btn-sm btn-outline-primary" title="Download file">
                            <i class="fa fa-download"></i>
                        </a>
                        @can(['media'])
                            <a href="{{ route('media.delete_file', $file->id) }}" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')" title="Delete file">
                                <i class="fa fa-trash"></i>
                            </a>
                        @endcan
                    </li>
                @endforeach
            </ul>
        @else
            <p class="text-muted"><small>No files attached.</small></p>
        @endif
    </div>
</li>