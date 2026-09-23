<?php

namespace App\Models;

use Eloquent as Model;
use App\Traits\UploadFile;
use App\Traits\DeleteRecord;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

/**
 * Class DrawingReceived
 * @package App\Models
 * @version March 27, 2026, 12:00 am GST
 *
 * @property integer lpoin_id
 * @property integer responsible_engineer_id
 * @property string type_of_work
 * @property date start_date
 * @property date review_comments_date
 * @property date approval_date
 * @property string status
 * @property text notes
 */
class DrawingReceived extends Model implements HasMedia
{
    //Use File Upload Traits
    use HasMediaTrait;
    use UploadFile;
    use DeleteRecord;

    public $table = 'drawing_receiveds';

    protected $with = ['lpoin', 'responsibleEngineer'];

    public $fillable = [
        'lpoin_id',
        'responsible_engineer_id',
        'type_of_work',
        'start_date',
        'review_comments_date',
        'approval_date',
        'status',
        'notes',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'lpoin_id' => 'integer',
        'responsible_engineer_id' => 'integer',
        'type_of_work' => 'string',
        'start_date' => 'date',
        'review_comments_date' => 'date',
        'approval_date' => 'date',
        'status' => 'string',
        'notes' => 'string',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'lpoin_id' => 'required|exists:lpoins,id',
        'responsible_engineer_id' => 'required|exists:users,id',
        'type_of_work' => 'required|string',
        'start_date' => 'required|date',
        'review_comments_date' => 'nullable|date',
        'approval_date' => 'nullable|date',
        'status' => 'required|string',
        'notes' => 'nullable|string',
    ];

    /**
     * Get the related Lpoin record
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function lpoin()
    {
        return $this->belongsTo(\App\Models\Lpoin::class, 'lpoin_id', 'id');
    }

    /**
     * Get the responsible engineer (User)
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function responsibleEngineer()
    {
        return $this->belongsTo(\App\User::class, 'responsible_engineer_id', 'id');
    }

    /**
     * Get all contributions for this drawing
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function contributions()
    {
        return $this->hasMany(\App\Models\DrawingReceivedContribution::class, 'drawing_received_id', 'id')->orderBy('created_at', 'desc');
    }
}
