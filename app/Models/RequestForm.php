<?php

namespace App\Models;

use Eloquent as Model;
use App\Traits\UploadFile;
use App\Traits\DeleteRecord;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

/**
 * Class RequestForm
 * @package App\Models
 * @version September 10, 2020, 1:53 pm PKT
 *
 * @property integer $user_id
 * @property string $name
 * @property string $date_time
 * @property string $note
 */
class RequestForm extends Model implements HasMedia
{
    use HasMediaTrait, UploadFile;
    use DeleteRecord;

    public $table = 'request_forms';

    public $fillable = [
        'user_id',
        'name',
        'date_time',
        'note',
        'comments',
        'status'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'name' => 'string',
        'note' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required',
        'date_time' => 'required',
        'note' => 'required',
    ];

    public function scopeWhereLike($query, $column, $value)
    {
        return $query->where($column, 'like', '%'.$value.'%');
    }

    public function scopeOrWhereLike($query, $column, $value)
    {
        return $query->orWhere($column, 'like', '%'.$value.'%');
    }

    public function media_files()
    {
        $files = [];

        foreach ($this->getMedia() as $item)
            $files[] = $item->getFullUrl();

        return $files;
    }

    public function user()
    {
        return $this->belongsTo(\App\User::class, 'user_id', 'id');
    }
}
