<?php

namespace App\Models;

use App\User;
use Eloquent as Model;

/**
 * Class SampleComment
 * @package App\Models
 * @version June 29, 2020, 8:20 pm PKT
 *
 */
class Comment extends Model
{
    public $table = 'comments';

    public $fillable = [
        'commentable_id',
        'commentable_type',
        'user_id',
        'comments',
        'status',
    ];

    public function commentable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
