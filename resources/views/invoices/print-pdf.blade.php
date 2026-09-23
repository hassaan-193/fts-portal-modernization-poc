@php
    use App\Models\Invoice;
    
    $letterheadType = $letterheadType ?? 'fts';
    
    // Map letterhead type to TRN, title, and image paths
    $letterheadConfig = [
        'fts' => [
            'title' => 'FIRE TECHNICAL SERVICES',
            'trn' => '100317831400003',
            'headerImage' => 'dist/img/fts_latter_head.jpeg',
            'footerImage' => 'dist/img/fts_letter_footer.jpg',
        ],
        'experts' => [
            'title' => 'EIC',
            'trn' => '104116226200003',
            'headerImage' => 'dist/img/experts_letter_head.jpeg',
            'footerImage' => 'dist/img/experts_letter_footer.jpeg',
        ],
        'ftsits' => [
            'title' => 'FTS ITS',
            'trn' => '100317831400003',
            'headerImage' => 'dist/img/ftsits_letter_head.jpeg',
            'footerImage' => 'dist/img/ftsits_letter_footer.jpeg',
        ],
    ];
    
    $config = $letterheadConfig[$letterheadType] ?? $letterheadConfig['fts'];
    
    // Build absolute paths for images - DomPDF needs file:// protocol or absolute filesystem paths
    $headerImagePath = public_path($config['headerImage']);
    $footerImagePath = public_path($config['footerImage']);
@endphp

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            color: #333;
            line-height: 1.2;
        }
        
        .invoice {
            padding: 0;
        }
        
        .header-section {
            text-align: center;
            margin-bottom: 0;
        }
        
        .header-section img {
            width: 100%;
            height: auto;
            display: block;
        }
        
        h2 {
            text-align: center;
            text-transform: uppercase;
            font-weight: bold;
            margin: 20px 0;
            font-size: 18px;
        }
        
        h6 {
            font-size: 13px;
            font-weight: bold;
            margin: 10px 0 5px 0;
        }
        
        .row {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        
        .col-md-4 {
            display: table-cell;
            width: 33.33%;
            padding: 5px;
            vertical-align: top;
        }
        
        .col-md-6 {
            display: table-cell;
            width: 50%;
            padding: 5px;
            vertical-align: top;
        }
        
        .col-md-8 {
            display: table-cell;
            width: 66.66%;
            padding: 5px;
            vertical-align: top;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        
        .table td, .table th {
            padding: 6px 8px;
            font-size: 12px;
            border: 1px solid #999;
            text-align: left;
        }
        
        .table thead th {
            background-color: #f9f9f9;
            font-weight: bold;
        }
        
        .left-custom {
            width: 35%;
            font-weight: 600;
            background-color: #f9f9f9;
        }
        
        .right-custom {
            width: 65%;
        }
        
        .expandable-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .k-term {
            margin-top: 20px;
        }
        
        .footer-section {
            text-align: center;
            margin-top: 20px;
        }
        
        .footer-section img {
            width: 100%;
            height: auto;
            display: block;
        }
        
        .mt-2 {
            margin-top: 10px;
        }
        
        dl, ol, ul {
            margin: 0;
            padding: 0;
        }
        
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="invoice">
        <!-- Header Image -->
        <div class="header-section">
            @if(file_exists($headerImagePath))
                <img src="file://{{ $headerImagePath }}" alt="Letterhead" />
            @endif
        </div>

        <!-- Invoice Title -->
        <h2>{{ __('models/invoices.invoice_title.' . $invoice->invoice_type_id) }}</h2>

        <!-- Invoice Details Row -->
        <div class="row" style="margin-top: 20px;">
            <div class="col-md-4">
                <table class="table">
                    <tr>
                        <td class="left-custom"><strong>TRN:</strong></td>
                        <td class="right-custom trn_no">{{ $config['trn'] }}</td>
                    </tr>
                    <tr>
                        <td class="left-custom">{{ __('models/invoices.fields.invoice_no') }}</td>
                        <td class="right-custom">{{ $invoice->invoice_no }}</td>
                    </tr>
                    <tr>
                        <td class="left-custom">{{ __('models/invoices.fields.start_date') }}</td>
                        <td class="right-custom">{{ $invoice->start_date }}</td>
                    </tr>
                    <tr>
                        <td class="left-custom">{{ __('models/invoices.fields.delivery_date') }}</td>
                        <td class="right-custom">{{ $invoice->delivery_date }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-4">
                <table class="table">
                    <tr>
                        <td class="left-custom">{{ __('models/invoices.fields.quotation_id') }}</td>
                        <td class="right-custom">{{ $invoice->quotation->ref_no }}</td>
                    </tr>
                    <tr>
                        <td class="left-custom">{{ __('models/invoices.fields.lpoin_id') }}</td>
                        <td class="right-custom">{{ $invoice->quotation->lpoins->ref_no }}</td>
                    </tr>
                    <tr>
                        <td class="left-custom">{{ __('models/invoices.fields.end_date') }}</td>
                        <td class="right-custom">{{ $invoice->end_date }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-4">
                <table class="table">
                    <tr>
                        <td class="left-custom">{{ __('models/quotations.fields.amount') }}</td>
                        <td class="right-custom">{{ $invoice->currency }}: {{ number_format($invoice->quotation->amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="left-custom">{{ __('models/invoices.fields.invoice_value') }}</td>
                        <td class="right-custom">{{ $invoice->currency }}: {{ number_format($invoice->total_amount, 2) }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Bill To Section -->
        <div style="margin-top: 15px;">
            <h6><strong>BILL TO</strong></h6>
            <table class="table">
                <tr>
                    <td class="left-custom">{{ __('models/companies.singular') }}</td>
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

        <!-- Line Items Table -->
        <table class="table" style="margin-top: 15px;">
            <thead>
                <tr>
                    <th colspan="6" style="text-align: left;"><strong>Project:</strong> {{ $invoice->quotation->subject }}</th>
                </tr>
                <tr>
                    <th>SR No.</th>
                    <th>{{ __('models/invoiceProductDetails.fields.product') }}</th>
                    <th>{{ __('models/invoiceProductDetails.fields.unit') }}</th>
                    <th>{{ __('models/invoiceProductDetails.fields.qty') }}</th>
                    <th>{{ __('models/invoiceProductDetails.fields.rate') }}</th>
                    <th>{{ __('models/invoiceProductDetails.fields.amount') }}</th>
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
                    <td colspan="1">{{ number_format($invoice->invoice_product_details->sum('total_amount') - $invoice->invoice_product_details->sum('vat'), 2) }}</td>
                </tr>

                @foreach($invoice->invoice_service_details as $key => $item)
                    <tr>
                        <td colspan="4"></td>
                        <td><strong>{{ $item->description }}</strong></td>
                        <td colspan="1">{{ number_format($item->amount, 2) }}</td>
                    </tr>
                @endforeach

                <tr>
                    <td colspan="4"></td>
                    <td><strong>Total Amount</strong></td>
                    <td colspan="1">{{ number_format($invoice->total_amount, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Terms & Conditions and Payment Info -->
        <div class="row k-term">
            <div class="col-md-6" style="padding-right: 10px;">
                <h6><strong>Terms & Conditions:</strong></h6>
                <div style="font-size: 12px;">
                    {!! $invoice->note1 !!}
                </div>
            </div>
            <div class="col-md-6" style="padding-left: 10px;">
                <h6><strong>Payment Instruction:</strong></h6>
                <div style="font-size: 12px; margin-bottom: 10px;">
                    {!! $invoice->note2 !!}
                </div>
                <table class="table mt-2">
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

        <!-- Signatures and Approvals -->
        <div class="row" style="margin-top: 20px;">
            <div class="col-md-6" style="padding-right: 10px;">
                <h6><strong id="title">{{ $config['title'] }}</strong></h6>
                <table class="table">
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
                        <td class="right-custom" style="height: 60px;"></td>
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
            <div class="col-md-6" style="padding-left: 10px;">
                <h6><strong>APPROVED & RECEIVED BY</strong></h6>
                <table class="table">
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
                        <td class="right-custom" style="height: 60px;"></td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Footer Image -->
        <div class="footer-section" style="margin-top: 30px;">
            @if(file_exists($footerImagePath))
                <img src="file://{{ $footerImagePath }}" alt="Footer" />
            @endif
        </div>
    </div>
</body>
</html>
    </div>

    <h2 class="text-center text-weight-bold" style="text-transform: uppercase;">{{ __('models/invoices.invoice_title.' . $invoice->invoice_type_id) }}</h2>

    <div style="display: table; width: 100%; margin-top: 1.5rem;">
        <div style="display: table-cell; width: 33.33%; vertical-align: top; padding-right: 10px;">
            <table class="expandable-table" border="1">
                <tr>
                    <td class="left-custom"><strong>TRN:</strong></td>
                    <td class="right-custom">{{ $config['trn'] }}</td>
                </tr>
                <tr>
                    <td class="left-custom">{{ __('models/invoices.fields.invoice_no') }}</td>
                    <td class="right-custom">{{ $invoice->invoice_no }}</td>
                </tr>
                <tr>
                    <td class="left-custom">{{ __('models/invoices.fields.start_date') }}</td>
                    <td class="right-custom">{{ $invoice->start_date }}</td>
                </tr>
                <tr>
                    <td class="left-custom">{{ __('models/invoices.fields.delivery_date') }}</td>
                    <td class="right-custom">{{ $invoice->delivery_date }}</td>
                </tr>
            </table>
        </div>
        
        <div style="display: table-cell; width: 33.33%; vertical-align: top; padding: 0 10px;">
            <table class="expandable-table" border="1">
                <tr>
                    <td class="left-custom">{{ __('models/invoices.fields.quotation_id') }}</td>
                    <td class="right-custom">{{ $invoice->quotation->ref_no }}</td>
                </tr>
                <tr>
                    <td class="left-custom">{{ __('models/invoices.fields.lpoin_id') }}</td>
                    <td class="right-custom">{{ $invoice->quotation->lpoins->ref_no }}</td>
                </tr>
                <tr>
                    <td class="left-custom">{{ __('models/invoices.fields.end_date') }}</td>
                    <td class="right-custom">{{ $invoice->end_date }}</td>
                </tr>
            </table>
        </div>
        
        <div style="display: table-cell; width: 33.33%; vertical-align: top; padding-left: 10px;">
            <table class="expandable-table" border="1">
                <tr>
                    <td class="left-custom">{{ __('models/quotations.fields.amount') }}</td>
                    <td class="right-custom">{{ $invoice->currency }}: {{ number_format($invoice->quotation->amount, 2) }}</td>
                </tr>
                <tr>
                    <td class="left-custom">{{ __('models/invoices.fields.invoice_value') }}</td>
                    <td class="right-custom">{{ $invoice->currency }}: {{ number_format($invoice->total_amount, 2) }}</td>
                </tr>
            </table>
        </div>
    </div>

    <div style="margin-top: 1.5rem;">
        <h6><strong>BILL TO</strong></h6>
        <table class="expandable-table" border="1">
            <tr>
                <td class="left-custom">{{ __('models/companies.singular') }}</td>
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

    <table class="expandable-table" border="1" style="margin-top: 1.5rem;">
        <thead>
            <tr>
                <th colspan="6" style="text-align: left; padding: 0.5rem;">
                    <strong>Project:</strong> {{ $invoice->quotation->subject }}
                </th>
            </tr>
            <tr>
                <th>SR No.</th>
                <th>{{ __('models/invoiceProductDetails.fields.product') }}</th>
                <th>{{ __('models/invoiceProductDetails.fields.unit') }}</th>
                <th>{{ __('models/invoiceProductDetails.fields.qty') }}</th>
                <th>{{ __('models/invoiceProductDetails.fields.rate') }}</th>
                <th>{{ __('models/invoiceProductDetails.fields.amount') }}</th>
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
            
            @if($invoice->invoice_product_details->sum('vat') > 0)
                <tr>
                    <td colspan="4"></td>
                    <td><strong>VAT (5%)</strong></td>
                    <td>{{ number_format($invoice->invoice_product_details->sum('vat'), 2) }}</td>
                </tr>
            @endif

            @foreach($invoice->invoice_service_details as $item)
                <tr>
                    <td colspan="4"></td>
                    <td><strong>{{ $item->description }}</strong></td>
                    <td>{{ number_format($item->amount, 2) }}</td>
                </tr>
            @endforeach

            <tr>
                <td colspan="4"></td>
                <td><strong>Total Amount</strong></td>
                <td>{{ number_format($invoice->total_amount, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div style="margin-top: 2rem; display: table; width: 100%;">
        <div style="display: table-cell; width: 50%; padding-right: 10px;">
            <h6><strong>Terms & Conditions:</strong></h6>
            <div style="font-size: 0.875rem;">
                {!! $invoice->note1 !!}
            </div>
        </div>
        
        <div style="display: table-cell; width: 50%; padding-left: 10px;">
            <h6><strong>Payment Instruction:</strong></h6>
            <div style="font-size: 0.875rem;">
                {!! $invoice->note2 !!}
            </div>
            
            <table class="expandable-table" border="1" style="margin-top: 0.5rem;">
                <tr>
                    <td class="left-custom">Beneficiary Account Name:</td>
                    <td class="right-custom">{{ $invoice->invoice_bank->beneficary_account_name }}</td>
                </tr>
                <tr>
                    <td class="left-custom">Bank Name:</td>
                    <td class="right-custom">{{ $invoice->invoice_bank->bank_name }}</td>
                </tr>
                <tr>
                    <td class="left-custom">Account No:</td>
                    <td class="right-custom">{{ $invoice->invoice_bank->account_no }}</td>
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

    <div style="margin-top: 2rem; display: table; width: 100%;">
        <div style="display: table-cell; width: 50%; padding-right: 10px;">
            <h6><strong>{{ $config['title'] }}</strong></h6>
            <table class="expandable-table" border="1">
                <tr>
                    <td class="left-custom">Engineering Department:</td>
                    <td class="right-custom"></td>
                </tr>
                <tr>
                    <td class="left-custom">Name:</td>
                    <td class="right-custom"></td>
                </tr>
                <tr>
                    <td class="left-custom">Signature & Stamp:</td>
                    <td class="right-custom"></td>
                </tr>
                <tr>
                    <td class="left-custom">Email:</td>
                    <td class="right-custom">accounts.rak@example.com</td>
                </tr>
            </table>
        </div>
        
        <div style="display: table-cell; width: 50%; padding-left: 10px;">
            <h6><strong>APPROVED & RECEIVED BY</strong></h6>
            <table class="expandable-table" border="1">
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
                    <td class="right-custom"></td>
                </tr>
            </table>
        </div>
    </div>

    <div style="margin-top: 2rem; text-align: center;">
        <img src="{{ public_path('dist/img/' . $config['footerImage']) }}" style="width: 100%; margin: 0 auto;" alt="Footer" />
    </div>
</div>
