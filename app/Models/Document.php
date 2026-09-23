<?php

namespace App\Models;

use Eloquent as Model;
use App\Traits\DeleteRecord;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;


class Document extends Model implements HasMedia
{
    //Use File Upload Traits
    use HasMediaTrait;
    use DeleteRecord;

    public $table = 'documents';
    public $fillable = [
        'name',
        'type',
        'date',
    ];
    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [];
}
