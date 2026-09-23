<?php

namespace App\Models;

use Eloquent as Model;
use App\Traits\UploadFile;
use App\Traits\DeleteRecord;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

class Letter extends Model implements HasMedia
{
    use HasMediaTrait;
    use UploadFile;
    use DeleteRecord;

    public $table = 'letters';

    protected $fillable = [
        'staff_profile_id',
        'type',
        'title',
        'content',
        'issued_by',
        'issued_at',
        'ref_no',
        'days_deduct',
    ];

    protected $casts = [
        'id' => 'integer',
        'staff_profile_id' => 'integer',
        'type' => 'string',
        'title' => 'string',
        'content' => 'string',
        'issued_by' => 'string',
        'issued_at' => 'date',
        'ref_no' => 'string',
        'days_deduct' => 'integer',
    ];

    public static $rules = [
        'staff_profile_id' => 'required|exists:staf_profile,id',
        'type' => 'required|in:warning,appreciation,general_notice,poor_performance_notice,accommodation_notice,vehicle_notice,attendance_notice,weather_notice,eid_holidays_notice',
        'title' => 'required|string|max:255',
        'content' => 'required|string',
        'issued_by' => 'required|string|max:255',
        'issued_at' => 'required|date',
        'ref_no' => 'nullable|string|max:255|unique:letters,ref_no',
        'days_deduct' => 'nullable|integer|min:1|max:365',
    ];

    public function stafProfile()
    {
        return $this->belongsTo(\App\Models\StafProfile::class, 'staff_profile_id', 'id');
    }
}
