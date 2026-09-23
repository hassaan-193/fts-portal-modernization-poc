<?php

namespace App\Models;

use Eloquent as Model;
use App\Traits\UploadFile;
use App\Traits\DeleteRecord;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
/**
 * Class Lpoin
 * @package App\Models
 * @version June 29, 2020, 12:00 am PKT
 *
 * @property integer quotation_id
 * @property string ref_no
 * @property string date_issue
 * @property string date_due
 */
class Lpoin extends Model implements HasMedia
{
    //Use File Upload Traits
    use HasMediaTrait;
    use UploadFile;
    use DeleteRecord;

    public $table = 'lpoins';

    protected $with = ['quotation'];

    public $fillable = [
        'quotation_id',
        'ref_no',
        'payment_terms',
        'date_issue',
        'civil_defence_fee',
        'government_fee',
        'adjustment_fee',
        'date_due',
        'amount',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'quotation_id' => 'integer',
        'ref_no' => 'string',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'quotation_id' => 'required',
        // 'amount' => 'required|numeric',
        'civil_defence_fee' => 'nullable|numeric',
        'government_fee' => 'nullable|numeric',
        'adjustment_fee' => 'nullable|numeric',
    ];

    /**
    * @return \Illuminate\Database\Eloquent\Relations\belongsTo
    **/
    public function quotation()
    {
        return $this->belongsTo(\App\Models\Quotation::class, 'quotation_id', 'id');
    }
}
