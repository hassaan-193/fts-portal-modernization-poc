<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\PurchaseOrder;

class PurchaseOrderCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $po;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(PurchaseOrder $po)
    {
        $this->po = $po;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('New Purchase Order Request: ' . $this->po->request_number)
                    ->view('emails.purchase_order_created')
                    ->with([
                        'po' => $this->po,
                    ]);
    }
}
