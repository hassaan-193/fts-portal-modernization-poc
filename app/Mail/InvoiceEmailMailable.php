<?php

namespace App\Mail;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvoiceEmailMailable extends Mailable
{
    use Queueable, SerializesModels;

    public $invoice;
    protected $pdfPath;
    protected $ccEmails;

    /**
     * Create a new message instance.
     *
     * @param Invoice $invoice
     * @param string|null $pdfPath
     * @param array $ccEmails
     */
    public function __construct(Invoice $invoice, string $pdfPath = null, array $ccEmails = [])
    {
        $this->invoice = $invoice;
        $this->pdfPath = $pdfPath;
        $this->ccEmails = array_filter($ccEmails); // Remove null/empty values
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $company = $this->invoice->quotation->company;
        $recipientEmail = $company->email ?? $company->billing_email;
        
        // Start building email using the accounts mailer
        $mail = $this->mailer('accounts')
            ->to($recipientEmail, $company->name)
            ->subject("Invoice #{$this->invoice->invoice_no} - {$company->name}")
            ->view('emails.invoice-email')
            ->with([
                'invoice' => $this->invoice,
                'company' => $company,
            ])
            ->from(env('MAIL_FROM_ADDRESS_ACCOUNTS', 'accounts.rak@example.com'), 
                   env('MAIL_FROM_NAME_ACCOUNTS', 'FTS - Accounts Team'));

        // Add CC recipients if valid
        foreach ($this->ccEmails as $ccEmail) {
            if (filter_var($ccEmail, FILTER_VALIDATE_EMAIL)) {
                $mail->cc($ccEmail);
            }
        }

        // Attach PDF if exists
        if ($this->pdfPath && file_exists($this->pdfPath)) {
            $mail->attach($this->pdfPath, [
                'as' => "Invoice_{$this->invoice->invoice_no}.pdf",
                'mime' => 'application/pdf',
            ]);
        }

        return $mail;
    }
}
