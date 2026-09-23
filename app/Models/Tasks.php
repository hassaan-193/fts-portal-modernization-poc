<?php

namespace App\Models;

use Eloquent as Model;

class Tasks extends Model
{
    protected $table = 'tasks';
    protected $fillable = [
        'start_date',
        'end_date',
        'title',
        'assigned',
        'description',
        'status'
    ];
    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'start_date' => 'required',
        'title' => 'required',
        'assigned' => 'required',
    ];
    public static $allowedEmails = ['prasad.rak@example.com'];

    public function engineer()
    {
        return $this->belongsTo(\App\User::class,'assigned');
    }
}
