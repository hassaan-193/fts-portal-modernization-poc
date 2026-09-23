<?php

namespace App\Rules;

use App\Models\Quotation;
use Illuminate\Contracts\Validation\Rule;

class LpoInDuplicationCheck implements Rule
{
    private $id;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // in case of update check if the request is for same record
        if($this->id){
            $invoice_request = \App\Models\InvoiceRequest::find($this->id);
            if($invoice_request->requestable_id == request()->get('quotation_id')){
                return true;
            }
        }

        $status = \App\Models\InvoiceRequest::whereHasMorph('requestable', Quotation::class, function ($query)  {
            $query->whereId(request()->get('quotation_id'));
        })
        ->whereStatus(0)
        ->count();
        return $status > 0 ? false :true ;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The Lpo has already invoice created.';
    }
}
