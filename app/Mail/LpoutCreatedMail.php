<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Lpoout;

class LpoutCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $mailer = 'lpouts';
    public $lpoout;
    public $pdfPath;

    public function __construct(Lpoout $lpoout, ?string $pdfPath = null)
    {
        $this->lpoout = $lpoout;
        $this->pdfPath = $pdfPath;
    }

    public function build()
    {
        $message = $this->subject('Local Purchase Order (LPO) Created: ' . $this->lpoout->name)
                        ->view('emails.lpoout_created')
                        ->with(['lpoout' => $this->lpoout]);

        if ($this->pdfPath && file_exists($this->pdfPath)) {
            $filename = 'LPO_' . ($this->lpoout->lpo_invoice_no ?? $this->lpoout->id) . '.pdf';
            $message->attach($this->pdfPath, [
                'as'   => $filename,
                'mime' => 'application/pdf',
            ]);
        }

        return $message;
    }
}
