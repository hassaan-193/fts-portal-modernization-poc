<?php

namespace App\Events;

use App\Models\Lpoin;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LpoinCreated
{
    use Dispatchable, SerializesModels;

    public $lpoin;
    public $subject;
    public $startDate;


    public function __construct(Lpoin $lpoin, $startDate, $subject = null)
    {
        $this->lpoin = $lpoin;
        $this->startDate = $startDate;
        $this->subject = $subject;
    }
}
