<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $table = 'tickets';

    protected $fillable = [
        'staf_id',
        'ticket_date',
        'agent_name',
        'travel_type',
        'travel_date',
        'return_date',
        'amount',
        'vat',
        'total_value',
        'payment_status'
    ];

    public function stafProfile()
    {
        return $this->belongsTo(StafProfile::class, 'staf_id');
    }
}
