@php
use App\Models\Invoice;
$file=Invoice::find($invoice->id);
if (isset($file->getMedia()[0])) {
    $url=$file->getMedia()[0]->getUrl();
}
else{
    $url="";
}
@endphp
@extends('layouts.master')

<style>
    .table td, .table th {
        padding: .25rem .75rem !important;
       font-size: .875rem!important;
       border-top: 1px solid #ababab !important;
       border:1px solid #ababab;
    }
    .table thead th{
        border:1px solid #ababab !important;
    }
    dl, ol, ul {
        margin-top: 0;
        margin-bottom: 0 !important;
    }
    td.left-custom{
        width: 35%;
        font-weight: 600;
    }
    td.right-custom{
        width: 65%;
    }
</style>

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark text-weight-bold">@lang('crud.detail') @lang('models/invoices.singular')</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{!! route('invoices.index') !!}">@lang('models/invoices.singular')</a></li>
                        <li class="breadcrumb-item active">@lang('crud.detail')</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        @include('flash::message')
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="invoice p-3 mb-3">
                        <div class="row no-print">
                            <div class="col-12" style="position: absolute;width: 100%;right: 15px;">
                                <button type="button" onclick="printWitFtsits()" class="btn btn-success float-right m-1"><i class="fas fa-print"></i> FTSITS Print</button>
                                <button type="button" onclick="printWithExpert()" class="btn btn-warning float-right m-1"><i class="fas fa-print"></i> EXPERTS Print</button>
                                <button type="button" onclick="printWithFts()" class="btn btn-danger float-right m-1"><i class="fas fa-print"></i> FTS Print</button>
                                <button type="button" class="btn btn-info float-right m-1" data-toggle="modal" data-target="#sendEmailModal"><i class="fas fa-envelope"></i> Send Email</button>
                            </div>
                        </div>
                        <div class="col-xs-12 text-center" >
                            <img class="fts_img images" src="{{asset('dist/img/fts_latter_head.jpeg')}}" style="width:100%;margin:auto;" />
                            <img class="expert_img images" src="{{asset('dist/img/experts_letter_head.jpeg')}}" style="width:100%; margin:auto; display:none;" />
                            <img class="ftsits_img images" src="{{asset('dist/img/ftsits_letter_head.jpeg')}}" style="width:100%;margin:auto; display:none;" />
                        </div>
                        <h2 class="text-center text-weight-bold" style="text-transform: uppercase;">@lang('models/invoices.invoice_title.'.$invoice->invoice_type_id)</h2>
                        <div class="row justify-content-between mt-4">
                            <div class="col-md-4">
                                <table class="table expandable-table" border="1">
                                    <tr>
                                        <td class="left-custom"><strong>TRN:</strong></td>
                                        <td class="right-custom trn_no">100317831400003</td>
                                    </tr>
                                    <tr>
                                        <td class="left-custom">@lang('models/invoices.fields.invoice_no')</td>
                                        <td class="right-custom">{{$invoice->invoice_no}}</td>
                                    </tr>
                                    <tr>
                                        <td class="left-custom">@lang('models/invoices.fields.start_date')</td>
                                        <td class="right-custom">{{$invoice->start_date}}</td>
                                    </tr>
                                    <tr>
                                        <td class="left-custom">@lang('models/invoices.fields.delivery_date')</td>
                                        <td class="right-custom">{{$invoice->delivery_date}}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-4">
                                <table class="table expandable-table" border="1">
                                    <tr>
                                        <td class="left-custom">@lang('models/invoices.fields.quotation_id')</td>
                                        <td class="right-custom">{{$invoice->quotation->ref_no}}</td>
                                    </tr>
                                    <tr>
                                        <td class="left-custom">@lang('models/invoices.fields.lpoin_id')</td>
                                        <td class="right-custom">{{$invoice->quotation->lpoins->ref_no}}</td>
                                    </tr>
                                    <tr>
                                        <td class="left-custom">@lang('models/invoices.fields.end_date')</td>
                                        <td class="right-custom">{{$invoice->end_date}}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-4">
                                <table class="table expandable-table" border="1">
                                    <tr>
                                        <td class="left-custom">@lang('models/quotations.fields.amount')</td>
                                        <td class="right-custom">{{$invoice->currency}}: &nbsp;{{ number_format($invoice->quotation->amount,2)}}</td>
                                    </tr>
                                    <tr>
                                        <td class="left-custom">@lang('models/invoices.fields.invoice_value')</td>
                                        <td class="right-custom">{{$invoice->currency}}: &nbsp;{{ number_format($invoice->total_amount,2)}}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-8">
                                <h6>
                                    <strong>BILL TO</strong>
                                </h6>
                                <table class="table expandable-table" border="1">
                                    <tr>
                                        <td class="left-custom">@lang('models/companies.singular')</td>
                                        <td class="right-custom">{{$invoice->quotation->company->name}}</td>
                                    </tr>
                                    <tr>
                                        <td class="left-custom">Address</td>
                                        <td class="right-custom">{{$invoice->quotation->company->billing_address}}</td>
                                    </tr>
                                    <tr>
                                        <td class="left-custom">Contact Person</td>
                                        <td class="right-custom">{{$invoice->quotation->company->billing_contact_person}}</td>
                                    </tr>
                                    <tr>
                                        <td class="left-custom">Tel. No</td>
                                        <td class="right-custom">{{$invoice->quotation->company->contact_no}}</td>
                                    </tr>
                                    <tr>
                                        <td class="left-custom">Mobile No</td>
                                        <td class="right-custom">{{$invoice->quotation->company->billing_pob}}</td>
                                    </tr>
                                    <tr>
                                        <td class="left-custom">Email</td>
                                        <td class="right-custom">
                                        {{$invoice->quotation->company->billing_email}}</td>
                                    </tr>
                                    <tr>
                                        <td class="left-custom">TRN:</td>
                                        <td class="right-custom">{{$invoice->quotation->company->vat_no}}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <table class="table expandable-table" border="1">
                          <thead>
                            <tr class="expandable-body" data-expandable-table="collapsed">
                              <th colspan="1">Project</th>
                            <th colspan="5">{{ $invoice->quotation->subject }}</th>
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
                                <tr class="expandable-header">
                                    <td>{{ ++$key }}</td>
                                    <td>{{ $item->product }}</td>
                                    <td>{{ $item->unit }}</td>
                                    <td>{{ $item->qty }}</td>
                                    <td>{{ number_format($item->rate,2) }}</td>
                                    <td>{{ number_format($item->amount,2) }}</td>
                                </tr>
                            @endforeach
                            <tr class="expandable-body" data-expandable-table="collapsed">
                                <td colspan="2"></td>
                                <td>
                                    <strong>VAT (5%)</strong>
                                </td>
                                <td colspan="1">
                                    {{ number_format($invoice->invoice_product_details->sum('vat'),2)}}
                                </td>
                                <td>
                                    <strong>Subtotal</strong>
                                </td>
                                <td colspan="1">
                                    {{ number_format($invoice->invoice_product_details->sum('total_amount') - $invoice->invoice_product_details->sum('vat'),2) }}
                                </td>
                            </tr>
                            @foreach($invoice->invoice_service_details as $key => $item)
                                <tr class="expandable-body" data-expandable-table="collapsed">
                                    <td colspan="4"></td>
                                    <td>
                                        <strong>{{ $item->description}}</strong>
                                    </td>
                                    <td colspan="1">
                                        {{ number_format($item->amount,2)}}
                                    </td>
                                </tr>
                            @endforeach
                            <tr class="expandable-body" data-expandable-table="collapsed">
                                <td colspan="4"></td>
                                <td>
                                    <strong>Total Amount</strong><br>
                                    {{$invoice->amount_in_word}}
                                </td>
                                <td colspan="1">
                                    {{ number_format($invoice->total_amount,2)}}
                                </td>
                            </tr>
                          </tbody>
                        </table>
                        <div class="row justify-content-between k-term">
                            <div class="col-md-6">
                                <dl class="row">
                                    <dt class="col-sm-12">Terms & Conditions:</dt>
                                </dl>
                                    {!!  $invoice->note1 !!}
                            </div>
                            <div class="col-md-6">
                                <dl class="row">
                                    <dt class="col-sm-12">Payment Instruction:</dt>
                                </dl>
                                    {!!  $invoice->note2 !!}
                                <table class="mt-2 table expandable-table" border="1">
                                    <tr class="expandable-body" data-expandable-table="collapsed">
                                        <td class="left-custom">Beneficiary Account Name:</td>
                                        <td class="right-custom">{{$invoice->invoice_bank->beneficary_account_name}}</td>
                                    </tr>
                                    <tr class="expandable-body" data-expandable-table="collapsed">
                                        <td class="left-custom">Bank Name:</td>
                                        <td class="right-custom">{{$invoice->invoice_bank->bank_name}}</td>
                                    </tr>
                                    <tr class="expandable-body" data-expandable-table="collapsed">
                                        <td class="left-custom">Bank Branch:</td>
                                        <td class="right-custom">{{$invoice->invoice_bank->bank_branch}}</td>
                                    </tr>
                                    <tr class="expandable-body" data-expandable-table="collapsed">
                                        <td class="left-custom">Account No:</td>
                                        <td class="right-custom">{{$invoice->invoice_bank->account_no}}</td>
                                    </tr>
                                    <tr class="expandable-body" data-expandable-table="collapsed">
                                        <td class="left-custom">Account Currency:</td>
                                        <td class="right-custom">{{$invoice->invoice_bank->account_currency}}</td>
                                    </tr>
                                    <tr class="expandable-body" data-expandable-table="collapsed">
                                        <td class="left-custom">IBAN No:</td>
                                        <td class="right-custom">{{$invoice->invoice_bank->iban_no}}</td>
                                    </tr>
                                    <tr class="expandable-body" data-expandable-table="collapsed">
                                        <td class="left-custom">Swift code:</td>
                                        <td class="right-custom">{{$invoice->invoice_bank->swift_code}}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="row justify-content-between">
                            <div class="col-md-6">
                                <h6>
                                    <strong id="title">FIRE TECHNICAL SERVICES</strong>
                                </h6>
                                <table class="table expandable-table" border="1">
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
                                        <td class="right-custom">
                                            {{-- <img width="180" src="{{asset('dist/img/account_signature.png')}}" alt="signature"> --}}
                                            @foreach ($invoice->request->user->getMedia() as $item)
                                                <img width="180" src="{{ $item->getFullUrl() }}" alt="signature">
                                            @endforeach

                                            <!-- show stamps based on selected template -->
                                            <img class="expert_img images" width="180" src="{{asset('dist/img/eic_stamp.png')}}" alt="eic-stamp" style="display:none;">
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
                            <div class="col-md-6">
                                <h6>
                                    <strong>APPROVED & RECEIVED BY</strong>
                                </h6>
                                 <table class="table expandable-table" border="1">
                                    <tr>
                                        <td class="left-custom">Company Name:</td>
                                        <td class="right-custom">{{ $invoice->quotation->company->name}}</td>
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
                        <div class="row">
                            <div class="col-xs-12 text-center">
                                <img class="fts_img images" src="{{asset('dist/img/fts_letter_footer.jpg')}}" style="width:100%;margin:auto;" />
                                <img class="expert_img images" src="{{asset('dist/img/experts_letter_footer.jpeg')}}" style="width:100%; margin:auto; display:none;" />
                                <img class="ftsits_img images" src="{{asset('dist/img/ftsits_letter_footer.jpeg')}}" style="width:100%;margin:auto; display:none;" />
                          </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
<script type="text/javascript">
    function printWithFts() {
        // change title
        $('#title').text("FIRE TECHNICAL SERVICES");
        $('.trn_no').text('100317831400003');

        toggleImages('fts_img');
        window.print();
    }
    function printWithExpert() {
        // change title
        $('#title').text("EIC");
        $('.trn_no').text('104116226200003');

        toggleImages('expert_img');
        window.print();
    }
    function printWitFtsits() {
        // change title
        $('#title').text("FTS ITS");
        $('.trn_no').text('100317831400003');

        toggleImages('ftsits_img');
        window.print();
    }
    function toggleImages(className) {
        for (let el of document.querySelectorAll('.images')) el.style.display = 'none';
        for (let el of document.querySelectorAll(`.${className}`)) el.style.display = 'block';
    }
</script>

<!-- Send Email Modal -->
<div class="modal fade" id="sendEmailModal" tabindex="-1" role="dialog" aria-labelledby="sendEmailModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sendEmailModalLabel">Send Invoice Email</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="sendEmailForm" method="POST" action="{{ route('invoices.send-email', $invoice->id) }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="letterhead_type_modal">Select Invoice Template <span class="text-danger">*</span></label>
                        <select name="letterhead_type" id="letterhead_type_modal" class="form-control" required>
                            <option value="">-- Select Template --</option>
                            <option value="fts">FTS (Fire Technical Services)</option>
                            <option value="experts">EXPERTS (EIC)</option>
                            <option value="ftsits">FTSITS</option>
                        </select>
                        <small class="form-text text-muted">
                            The selected template will be used for the invoice PDF.
                        </small>
                    </div>

                    <div class="form-group">
                        <label>Company Email</label>
                        <input type="text" class="form-control" readonly value="{{ $invoice->quotation->company->email ?? $invoice->quotation->company->billing_email ?? 'No email' }}">
                        <small class="form-text text-muted">
                            Invoice will be sent to this email address.
                        </small>
                    </div>

                    <!-- Fixed CC Emails (Checkboxes) -->
                    <div class="form-group">
                        <label>Default CC Recipients </label>
                        <div class="form-check">
                            @foreach(config('invoices.fixed_cc_emails', []) as $key => $email)
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="fixed_cc_emails[]" id="modal_fixed_cc_{{ $key }}" value="{{ $email }}">
                                    <label class="form-check-label" for="modal_fixed_cc_{{ $key }}">
                                        {{ ucfirst($key) }} - {{ $email }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="cc_emails_modal">Other CC</label>
                        <textarea name="cc_emails" id="cc_emails_modal" class="form-control" rows="3" ></textarea>
                        <small class="form-text text-muted">
                            Enter multiple email addresses separated by commas.
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="attachments_modal">Attach Additional Files</label>
                        <input type="file" class="form-control-file" name="attachments[]" id="attachments_modal" multiple accept="*/*">
                        <small class="form-text text-muted">
                            You can upload multiple files. Max 10MB per file.
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-danger btn-flat btn-lg text-maroon" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger btn-flat btn-lg" id="sendEmailBtn">
                        <i class="fas fa-envelope"></i> Send Email
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Handle send email form submission
    document.getElementById('sendEmailForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const form = this;
        const letterheadType = document.getElementById('letterhead_type_modal').value;
        
        if (!letterheadType) {
            alert('Please select a letterhead template.');
            return;
        }
        
        const sendBtn = document.getElementById('sendEmailBtn');
        const originalText = sendBtn.innerHTML;
        sendBtn.disabled = true;
        sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
        
        // Use FormData for multipart file upload support
        const formData = new FormData(form);
        
        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            sendBtn.disabled = false;
            sendBtn.innerHTML = originalText;
            
            if (data.success) {
                alert(data.message);
                $('#sendEmailModal').modal('hide');
                // Reload page to show updated status
                setTimeout(() => location.reload(), 1500);
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            sendBtn.disabled = false;
            sendBtn.innerHTML = originalText;
            alert('Error sending email: ' + error.message);
        });
    });
</script>

<style>
.k-term ul li{
    font-size:14px;
}
</style>
