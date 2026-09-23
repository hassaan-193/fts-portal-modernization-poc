<?php

namespace App\Traits\Models;

use Illuminate\Support\Facades\Auth;

trait InvoiceRequestTrait
{
    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    public static function bootInvoiceRequestTrait()
	{
		static::creating(function ($invoice_request) {
            $invoice_request->user_id = Auth::user()->id;
        });

        static::saved(function ($invoice_request) {
            if($user = \App\User::where('email','accounts.rak@example.com')->first())
                $user->notify(new \App\Notifications\InvoiceRequest($invoice_request));
        });
    }
}
