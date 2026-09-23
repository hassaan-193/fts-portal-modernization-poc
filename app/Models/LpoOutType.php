<?php

namespace App\Models;

use Eloquent as Model;

/**
 * Class LpoOutType
 * @package App\Models
 * @version July 1, 2020, 3:32 pm PKT
 *
 * @property string name
 */
class LpoOutType extends Model
{

    public $table = 'lpo_out_types';

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

    /**
    * @return \Illuminate\Database\Eloquent\Relations\hasMany
    **/
    public function lpoouts()
    {
        return $this->hasMany(\App\Models\Lpoout::class);
    }

}
