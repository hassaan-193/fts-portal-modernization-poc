<?php

namespace App\Models;

use App\Models\NewsLetter;
use Illuminate\Database\Eloquent\Model;

class VerifyEmail extends Model
{
    protected $guarded = [];

    public function newsLetter()
    {
        return $this->belongsTo(NewsLetter::class, 'email' , 'email');
    }
}
