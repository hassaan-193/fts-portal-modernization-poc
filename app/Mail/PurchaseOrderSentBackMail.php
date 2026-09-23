<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\PurchaseOrder;

/**
 * Sent to the requester when the department returns a purchase order request to
 * Step 1 instead of forwarding it to admin.
 */
class PurchaseOrderSentBackMail extends Mailable
{
    use Queueable, SerializesModels;

    public $po;
    public $notes;

    public function __construct(PurchaseOrder $po, ?string $notes = null)
    {
        $this->po = $po;
        $this->notes = $notes;
    }

    public function build()
    {
        return $this->subject('Purchase Order Request Sent Back: ' . $this->po->request_number)
                    ->view('emails.purchase_order_sent_back')
                    ->with([
                        'po' => $this->po,
                        'notes' => $this->notes,
                    ]);
    }
}
