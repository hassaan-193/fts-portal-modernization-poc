<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UploadFile;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

/**
 * Class DrawingReceivedContribution
 * @package App\Models
 * @version March 27, 2026, 12:00 am GST
 *
 * @property integer drawing_received_id
 * @property integer contributed_by_id (engineer/user)
 * @property string contribution_type
 * @property text description
 * @property string status (after this contribution)
 */
class DrawingReceivedContribution extends Model implements HasMedia
{
    use HasMediaTrait;
    use UploadFile;

    public $table = 'drawing_received_contributions';

    protected $with = ['contributedBy', 'drawingReceived'];

    public $fillable = [
        'drawing_received_id',
        'contributed_by_id',
        'contribution_type',
        'description',
        'status',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'drawing_received_id' => 'integer',
        'contributed_by_id' => 'integer',
        'contribution_type' => 'string',
        'description' => 'string',
        'status' => 'string',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'drawing_received_id' => 'required|exists:drawing_receiveds,id',
        'contribution_type' => 'required|string',
        'description' => 'nullable|string',
        'status' => 'required|string',
    ];

    /**
     * Get the drawing received record
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function drawingReceived()
    {
        return $this->belongsTo(\App\Models\DrawingReceived::class, 'drawing_received_id', 'id');
    }

    /**
     * Get the user who made this contribution
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function contributedBy()
    {
        return $this->belongsTo(\App\User::class, 'contributed_by_id', 'id');
    }
}
