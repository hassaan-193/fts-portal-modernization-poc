<?php

namespace App\Models;

use Carbon\Carbon;
use Eloquent as Model;

class Transaction extends Model
{
    public $table = 'balance_transactions';

    protected $fillable = [
        'reference_type',
        'reference_id'
    ];

    protected $casts = [
        'reference_type' => 'string',
        'reference_id' => 'integer',
        'data' => 'array'
    ];

    public function reference()
    {
        return $this->morphTo();
    }

    public function getCompanyAttribute()
    {
        $reference = $this->reference;

        if (!$reference) {
            return null;
        }

        switch (get_class($reference)) {
            case \App\Models\Invoice::class:
                return $reference->quotation->company ?? null;

            case \App\Models\Receipt::class:
                return $reference->transactionable->quotation->company ?? null;

            default:
                return null;
        }
    }
}
