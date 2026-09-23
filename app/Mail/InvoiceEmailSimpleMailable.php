<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvoiceEmailSimpleMailable extends Mailable
{
    use Queueable, SerializesModels;

    public $invoice;
    public $company;
    public $pdfPath;
    public $ccEmails = [];
    
    private $attachmentData = [];

    /**
     * Create a new message instance.
     */
    public function __construct($invoice, $company, $pdfPath, $ccEmails = [], $attachments = [])
    {
        $this->invoice = $invoice;
        $this->company = $company;
        $this->pdfPath = $pdfPath;
        $this->ccEmails = is_array($ccEmails) ? $ccEmails : [];
        $this->attachmentData = is_array($attachments) ? $attachments : [];
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $recipientEmail = $this->company->email ?? $this->company->billing_email;
        
        // Start building the message with invoice preview
        $message = $this->mailer('accounts')
            ->from('accounts.rak@example.com', 'FTS - Accounts Team')
            ->to($recipientEmail, $this->company->name)
            ->subject("Invoice #{$this->invoice->invoice_no} - {$this->company->name}")
            ->view('emails.invoice-preview', [
                'invoice' => $this->invoice,
                'company' => $this->company,
            ]);

        // Add CC recipients
        if (!empty($this->ccEmails) && is_array($this->ccEmails)) {
            foreach ($this->ccEmails as $ccEmail) {
                if (!empty($ccEmail) && filter_var($ccEmail, FILTER_VALIDATE_EMAIL)) {
                    $message->cc($ccEmail);
                }
            }
        }

        // Attach PDF if it exists
        if (!empty($this->pdfPath) && is_string($this->pdfPath) && file_exists($this->pdfPath)) {
            $message->attach($this->pdfPath, [
                'as' => "Invoice_{$this->invoice->invoice_no}.pdf",
                'mime' => 'application/pdf',
            ]);
        }

        // Attach uploaded files with proper filename and MIME type
        if (!empty($this->attachmentData) && is_array($this->attachmentData)) {
            foreach ($this->attachmentData as $attachment) {
                if (is_array($attachment) && isset($attachment['path'])) {
                    $filePath = $attachment['path'];
                    $fileName = $attachment['name'] ?? basename($filePath);
                    $mimeType = $attachment['mime'] ?? 'application/octet-stream';
                    
                    if (is_string($filePath) && !empty($filePath) && file_exists($filePath)) {
                        $message->attach($filePath, [
                            'as' => $fileName,
                            'mime' => $mimeType,
                        ]);
                    }
                }
            }
        }

        return $message;
    }
}

