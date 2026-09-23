<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }
        .table td, .table th {
            padding: .25rem .75rem !important;
            font-size: .875rem !important;
            border-top: 1px solid #ababab !important;
            border: 1px solid #ababab;
        }
        .table thead th {
            border: 1px solid #ababab !important;
            background-color: #f9f9f9;
        }
        dl, ol, ul {
            margin-top: 0;
            margin-bottom: 0 !important;
        }
        td.left-custom {
            width: 35%;
            font-weight: 600;
            background-color: #f9f9f9;
        }
        td.right-custom {
            width: 65%;
        }
        .invoice {
            padding: 20px;
            max-width: 100%;
            background-color: white;
            margin: 20px;
        }
        .text-center {
            text-align: center;
        }
        .text-weight-bold {
            font-weight: bold;
        }
        .mt-4 {
            margin-top: 1.5rem;
        }
        .mt-2 {
            margin-top: 0.5rem;
        }
        .k-term {
            margin-top: 2rem;
        }
        .expandable-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1rem;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        h2 {
            text-align: center;
            text-transform: uppercase;
            margin: 20px 0;
            font-size: 18px;
            color: #333;
        }
        h6 {
            font-size: 13px;
            margin: 10px 0;
        }
        .row {
            display: table;
            width: 100%;
            margin: 1.5rem 0;
        }
        .col-md-4 {
            display: table-cell;
            width: 33.33%;
            padding: 10px;
            vertical-align: top;
        }
        .col-md-6 {
            display: table-cell;
            width: 50%;
            padding: 10px;
            vertical-align: top;
        }
        .col-md-8 {
            display: table-cell;
            width: 66.66%;
            padding: 10px;
            vertical-align: top;
        }
        .footer-section {
            margin-top: 2rem;
            border-top: 1px solid #ddd;
            padding-top: 20px;
            text-align: center;
            font-size: 12px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="invoice">
        <h2 class="text-weight-bold">{{ __('models/invoices.invoice_title.' . $invoice->invoice_type_id) }}</h2>

        <div class="row">
            <div class="col-md-4">
                <table class="expandable-table" border="1">
                    <tr>
                        <td class="left-custom"><strong>TRN:</strong></td>
                        <td class="right-custom">100317831400003</td>
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
            <div class="col-md-4">
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
                    <td class="right-custom">{{ $company->name }}</td>
                </tr>
                <tr>
                    <td class="left-custom">Address</td>
                    <td class="right-custom">{{ $company->billing_address }}</td>
                </tr>
                <tr>
                    <td class="left-custom">Contact Person</td>
                    <td class="right-custom">{{ $company->billing_contact_person }}</td>
                </tr>
                <tr>
                    <td class="left-custom">Tel. No</td>
                    <td class="right-custom">{{ $company->contact_no }}</td>
                </tr>
                <tr>
                    <td class="left-custom">Email</td>
                    <td class="right-custom">{{ $company->billing_email }}</td>
                </tr>
                <tr>
                    <td class="left-custom">TRN:</td>
                    <td class="right-custom">{{ $company->vat_no }}</td>
                </tr>
            </table>
        </div>

        <table class="expandable-table" border="1" style="margin-top: 1.5rem;">
            <thead>
                <tr>
                    <th colspan="6" style="text-align: left; padding: 0.5rem; background-color: #f9f9f9;"><strong>Project:</strong> {{ $invoice->quotation->subject }}</th>
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
            <div style="display: table-cell; width: 50%; padding-right: 10px; vertical-align: top;">
                <h6><strong>Terms & Conditions:</strong></h6>
                <div style="font-size: 0.875rem;">
                    {!! $invoice->note1 !!}
                </div>
            </div>
            <div style="display: table-cell; width: 50%; padding-left: 10px; vertical-align: top;">
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

        <div class="footer-section">
            <p><strong>FTS - FIRE TECHNICAL SERVICES</strong></p>
            <p>accounts.rak@example.com</p>
            <hr style="border: none; border-top: 1px solid #ddd; margin: 10px 0;">
            <p>This is an automated invoice email. Please retain this email and attached invoice for your records.<br>
            For any inquiries regarding this invoice, please contact us.</p>
        </div>
    </div>
</body>
</html>
