@php
    use App\Models\Invoice;
    
    $letterheadType = $letterheadType ?? 'fts';
    
    // Map letterhead type to configuration
    $letterheadConfig = [
        'fts' => [
            'title' => 'FIRE TECHNICAL SERVICES',
            'trn' => '100317831400003',
            'headerImage' => 'dist/img/fts_latter_head.jpeg',
            'footerImage' => 'dist/img/fts_letter_footer.jpg',
            'stampImage' => null,
        ],
        'experts' => [
            'title' => 'EIC',
            'trn' => '104116226200003',
            'headerImage' => 'dist/img/experts_letter_head.jpeg',
            'footerImage' => 'dist/img/experts_letter_footer.jpeg',
            'stampImage' => 'dist/img/eic_stamp.png',
        ],
        'ftsits' => [
            'title' => 'FTS ITS',
            'trn' => '100317831400003',
            'headerImage' => 'dist/img/ftsits_letter_head.jpeg',
            'footerImage' => 'dist/img/ftsits_letter_footer.jpeg',
            'stampImage' => null,
        ],
    ];
    
    $config = $letterheadConfig[$letterheadType] ?? $letterheadConfig['fts'];
@endphp

<style>
    body {
        font-family: Arial, sans-serif;
        color: #333;
        font-size: 12px;
        line-height: 1.3;
        margin: 0;
        padding: 0;
    }
    
    .invoice {
        padding: 0;
        margin: 0;
    }
    
    img {
        max-width: 100%;
        height: auto;
    }
    
    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 10px;
    }
    
    td, th {
        padding: 6px;
        border: 1px solid #999;
        text-align: left;
    }
    
    th {
        background-color: #f0f0f0;
        font-weight: bold;
    }
    
    .left-custom {
        width: 35%;
        background-color: #f0f0f0;
        font-weight: bold;
    }
    
    .right-custom {
        width: 65%;
    }
    
    h2 {
        text-align: center;
        text-transform: uppercase;
        font-weight: bold;
        margin: 20px 0;
        font-size: 16px;
    }
    
    h6 {
        font-size: 12px;
        font-weight: bold;
        margin: 10px 0 5px 0;
    }
    
    .row {
        display: table;
        width: 100%;
        margin-bottom: 10px;
    }
    
    .col-33 {
        display: table-cell;
        width: 33.33%;
        padding: 5px;
        vertical-align: top;
    }
    
    .col-50 {
        display: table-cell;
        width: 50%;
        padding: 5px;
        vertical-align: top;
    }
    
    .col-67 {
        display: table-cell;
        width: 66.66%;
        padding: 5px;
        vertical-align: top;
    }
    
    .header-img {
        width: 100%;
        margin-bottom: 10px;
    }
    
    .footer-img {
        width: 100%;
        margin-top: 20px;
    }
    
    .signature-img {
        max-width: 150px;
        max-height: 80px;
    }
    
    .stamp-img {
        max-width: 120px;
        max-height: 120px;
    }
    
    dt {
        font-weight: bold;
        margin: 10px 0 5px 0;
    }
    
    dd {
        margin: 0 0 10px 0;
        font-size: 11px;
    }
</style>

<div class="invoice">
    <!-- Header Image -->
    <div style="text-align: center; margin-bottom: 10px;">
        <img src="{{ asset($config['headerImage']) }}" class="header-img" alt="Letterhead" />
    </div>

    <!-- Invoice Title -->
    <h2>@lang('models/invoices.invoice_title.' . $invoice->invoice_type_id)</h2>

    <!-- Invoice Details - 3 Columns -->
    <div class="row" style="margin-bottom: 15px;">
        <div class="col-33">
            <table>
                <tr>
                    <td class="left-custom"><strong>TRN:</strong></td>
                    <td class="right-custom">{{ $config['trn'] }}</td>
                </tr>
                <tr>
                    <td class="left-custom">@lang('models/invoices.fields.invoice_no')</td>
                    <td class="right-custom">{{ $invoice->invoice_no }}</td>
                </tr>
                <tr>
                    <td class="left-custom">@lang('models/invoices.fields.start_date')</td>
                    <td class="right-custom">{{ $invoice->start_date }}</td>
                </tr>
                <tr>
                    <td class="left-custom">@lang('models/invoices.fields.delivery_date')</td>
                    <td class="right-custom">{{ $invoice->delivery_date }}</td>
                </tr>
            </table>
        </div>
        <div class="col-33">
            <table>
                <tr>
                    <td class="left-custom">@lang('models/invoices.fields.quotation_id')</td>
                    <td class="right-custom">{{ $invoice->quotation->ref_no }}</td>
                </tr>
                <tr>
                    <td class="left-custom">@lang('models/invoices.fields.lpoin_id')</td>
                    <td class="right-custom">{{ $invoice->quotation->lpoins->ref_no }}</td>
                </tr>
                <tr>
                    <td class="left-custom">@lang('models/invoices.fields.end_date')</td>
                    <td class="right-custom">{{ $invoice->end_date }}</td>
                </tr>
            </table>
        </div>
        <div class="col-33">
            <table>
                <tr>
                    <td class="left-custom">@lang('models/quotations.fields.amount')</td>
                    <td class="right-custom">{{ $invoice->currency }}: {{ number_format($invoice->quotation->amount, 2) }}</td>
                </tr>
                <tr>
                    <td class="left-custom">@lang('models/invoices.fields.invoice_value')</td>
                    <td class="right-custom">{{ $invoice->currency }}: {{ number_format($invoice->total_amount, 2) }}</td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Bill To -->
    <div style="margin-bottom: 15px;">
        <h6>BILL TO</h6>
        <table>
            <tr>
                <td class="left-custom">@lang('models/companies.singular')</td>
                <td class="right-custom">{{ $invoice->quotation->company->name }}</td>
            </tr>
            <tr>
                <td class="left-custom">Address</td>
                <td class="right-custom">{{ $invoice->quotation->company->billing_address }}</td>
            </tr>
            <tr>
                <td class="left-custom">Contact Person</td>
                <td class="right-custom">{{ $invoice->quotation->company->billing_contact_person }}</td>
            </tr>
            <tr>
                <td class="left-custom">Tel. No</td>
                <td class="right-custom">{{ $invoice->quotation->company->contact_no }}</td>
            </tr>
            <tr>
                <td class="left-custom">Mobile No</td>
                <td class="right-custom">{{ $invoice->quotation->company->billing_pob }}</td>
            </tr>
            <tr>
                <td class="left-custom">Email</td>
                <td class="right-custom">{{ $invoice->quotation->company->billing_email }}</td>
            </tr>
            <tr>
                <td class="left-custom">TRN:</td>
                <td class="right-custom">{{ $invoice->quotation->company->vat_no }}</td>
            </tr>
        </table>
    </div>

    <!-- Project & Line Items -->
    <table style="margin-bottom: 15px;">
        <thead>
            <tr>
                <th colspan="6" style="text-align: left; padding: 8px;">
                    <strong>Project:</strong> {{ $invoice->quotation->subject }}
                </th>
            </tr>
            <tr>
                <th>SR No.</th>
                <th>@lang('models/invoiceProductDetails.fields.product')</th>
                <th>@lang('models/invoiceProductDetails.fields.unit')</th>
                <th>@lang('models/invoiceProductDetails.fields.qty')</th>
                <th>@lang('models/invoiceProductDetails.fields.rate')</th>
                <th>@lang('models/invoiceProductDetails.fields.amount')</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->invoice_product_details as $key => $item)
                <tr>
                    <td>{{ ++$key }}</td>
                    <td>{{ $item->product }}</td>
                    <td>{{ $item->unit }}</td>
                    <td>{{ $item->qty }}</td>
                    <td>{{ number_format($item->rate, 2) }}</td>
                    <td>{{ number_format($item->amount, 2) }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="2"></td>
                <td><strong>VAT (5%)</strong></td>
                <td colspan="1">{{ number_format($invoice->invoice_product_details->sum('vat'), 2) }}</td>
                <td><strong>Subtotal</strong></td>
                <td>{{ number_format($invoice->invoice_product_details->sum('total_amount') - $invoice->invoice_product_details->sum('vat'), 2) }}</td>
            </tr>
            @foreach($invoice->invoice_service_details as $item)
                <tr>
                    <td colspan="4"></td>
                    <td><strong>{{ $item->description }}</strong></td>
                    <td>{{ number_format($item->amount, 2) }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="4"></td>
                <td><strong>Total Amount</strong><br>{{ $invoice->amount_in_word }}</td>
                <td>{{ number_format($invoice->total_amount, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Terms & Conditions and Payment Instructions -->
    <div class="row" style="margin-bottom: 20px;">
        <div class="col-50">
            <h6>Terms & Conditions:</h6>
            <div style="font-size: 11px;">
                {!! $invoice->note1 !!}
            </div>
        </div>
        <div class="col-50">
            <h6>Payment Instruction:</h6>
            <div style="font-size: 11px; margin-bottom: 10px;">
                {!! $invoice->note2 !!}
            </div>
            <table>
                <tr>
                    <td class="left-custom">Beneficiary Account Name:</td>
                    <td class="right-custom">{{ $invoice->invoice_bank->beneficary_account_name }}</td>
                </tr>
                <tr>
                    <td class="left-custom">Bank Name:</td>
                    <td class="right-custom">{{ $invoice->invoice_bank->bank_name }}</td>
                </tr>
                <tr>
                    <td class="left-custom">Bank Branch:</td>
                    <td class="right-custom">{{ $invoice->invoice_bank->bank_branch }}</td>
                </tr>
                <tr>
                    <td class="left-custom">Account No:</td>
                    <td class="right-custom">{{ $invoice->invoice_bank->account_no }}</td>
                </tr>
                <tr>
                    <td class="left-custom">Account Currency:</td>
                    <td class="right-custom">{{ $invoice->invoice_bank->account_currency }}</td>
                </tr>
                <tr>
                    <td class="left-custom">IBAN No:</td>
                    <td class="right-custom">{{ $invoice->invoice_bank->iban_no }}</td>
                </tr>
                <tr>
                    <td class="left-custom">Swift Code:</td>
                    <td class="right-custom">{{ $invoice->invoice_bank->swift_code }}</td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Signatures -->
    <div class="row" style="margin-bottom: 20px;">
        <div class="col-50">
            <h6>{{ $config['title'] }}</h6>
            <table>
                <tr>
                    <td class="left-custom" style="width: 40%;">Engineering Department:</td>
                    <td class="right-custom"></td>
                </tr>
                <tr>
                    <td class="left-custom">Name:</td>
                    <td class="right-custom"></td>
                </tr>
                <tr>
                    <td class="left-custom">Signature & Stamp:</td>
                    <td class="right-custom" style="height: 80px; text-align: center; vertical-align: middle;">
                        @forelse($invoice->request->user->getMedia() as $media)
                            <img src="{{ $media->getFullUrl() }}" class="signature-img" alt="Signature" />
                        @empty
                        @endforelse
                        @if($config['stampImage'])
                            <img src="{{ asset($config['stampImage']) }}" class="stamp-img" alt="Stamp" />
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="left-custom">Verified by Accounts:</td>
                    <td class="right-custom">mag</td>
                </tr>
                <tr>
                    <td class="left-custom">Email:</td>
                    <td class="right-custom">accounts.rak@example.com</td>
                </tr>
            </table>
        </div>
        <div class="col-50">
            <h6>APPROVED & RECEIVED BY</h6>
            <table>
                <tr>
                    <td class="left-custom">Company Name:</td>
                    <td class="right-custom">{{ $invoice->quotation->company->name }}</td>
                </tr>
                <tr>
                    <td class="left-custom">Name:</td>
                    <td class="right-custom"></td>
                </tr>
                <tr>
                    <td class="left-custom">Date:</td>
                    <td class="right-custom"></td>
                </tr>
                <tr>
                    <td class="left-custom">Signature & Stamp:</td>
                    <td class="right-custom" style="height: 80px;"></td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Footer Image -->
    <div style="text-align: center;">
        <img src="{{ asset($config['footerImage']) }}" class="footer-img" alt="Footer" />
    </div>
</div>
