Dear {{ $company->billing_contact_person ?? $company->name }},

We are pleased to send you the invoice {{ $invoice->invoice_no }} as per your request. 

Please find the invoice details and PDF documents attached to this email.

**Invoice Details:**
- Invoice Number: {{ $invoice->invoice_no }}
- Invoice Date: {{ $invoice->start_date }}
- Total Amount: {{ $invoice->currency }} {{ number_format($invoice->total_amount, 2) }}
- Project: {{ $invoice->quotation->subject }}

**Attached Documents:**
1. Invoice_{{ $invoice->invoice_no }}_Details.pdf - Complete invoice details in professional format
2. Invoice_{{ $invoice->invoice_no }}.pdf - Invoice copy

If you have any questions about this invoice, please contact us at accounts.rak@example.com.

Thank you,

**FTS - FIRE TECHNICAL SERVICES**
Accounts Department
accounts.rak@example.com
