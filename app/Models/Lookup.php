<?php

namespace App\Models;

use Eloquent as Model;

class Lookup extends Model
{
    public $table = 'lookups';

    public $fillable = [
        'name',
        'tag'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required',
        'tag' => 'required'
    ];
}
