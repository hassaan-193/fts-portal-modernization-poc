<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Invoice;

class InvoiceCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $mailer = 'accounts';
    public $invoice;
    public $pdfPath;

    public function __construct(Invoice $invoice, ?string $pdfPath = null)
    {
        $this->invoice = $invoice;
        $this->pdfPath = $pdfPath;
    }

    public function build()
    {
        $message = $this->subject('Invoice Created: ' . $this->invoice->invoice_no)
                        ->view('emails.invoice_created')
                        ->with(['invoice' => $this->invoice]);

        if ($this->pdfPath && file_exists($this->pdfPath)) {
            $filename = 'Invoice_' . ($this->invoice->invoice_no ?? $this->invoice->id) . '.pdf';
            $message->attach($this->pdfPath, [
                'as'   => $filename,
                'mime' => 'application/pdf',
            ]);
        }

        return $message;
    }
}
