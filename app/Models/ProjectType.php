<?php

namespace App\Models;

use Eloquent as Model;

/**
 * Class ProjectType
 * @package App\Models
 * @version July 2, 2020, 8:45 pm PKT
 *
 * @property string name
 */
class ProjectType extends Model
{

    public $table = 'project_types';
    



    public $fillable = [
        'name'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'name' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required'
    ];

    
}
