<?php

namespace App\Models;

use Eloquent as Model;

/**
 * Class Product
 * @package App\Models
 * @version May 27, 2024, 3:44 pm PKT
 *
 * @property string $name
 * @property number $price
 */
class Product extends Model
{

    public $table = 'products';

    public $fillable = [
        'name',
        'price'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'price' => 'double'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required',
        'price' => 'required:numeric'
    ];

    public function quotation()
    {
        return $this->belongsTo(\App\Models\Quotation::class, 'quotation_id', 'id');
    }

}
