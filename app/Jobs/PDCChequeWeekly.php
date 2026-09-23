<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;


class PDCChequeWeekly implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $cheques = \App\Models\Cheque::with('transaction_payment_type')
        ->whereHas('transaction_payment_type',function($q){
            $q->where('name','Cheque');
        })
        ->where('status',0)
        ->whereDate('clearance_date', '>=', now())
        ->whereDate('clearance_date', '<=', now()->addDays(7))
        ->orderBy('clearance_date', 'asc')
        ->get();

        // send email
        $this->sendEmail($cheques);
    }

    public function sendEmail($cheques){
        $to_name = 'Mr Jalaa';
        $to_email = 'jalaa.rak@example.com';
        $subject = 'PDC Cheque Weekly Report';
        $cc_email = 'shamaeel@example.com';

        Mail::send('emails.pdc_cheque_weekly', ['cheques' => $cheques], function($message) use ($to_name, $to_email, $cc_email, $subject) {
            $message->to($to_email, $to_name)
                ->subject($subject);

            $message->cc($cc_email);
        });
    }
}
