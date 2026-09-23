<?php

namespace App\Models;
use Carbon\Carbon;
use Eloquent as Model;
use App\Traits\UploadFile;
use App\Traits\DeleteRecord;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use \Staudenmeir\EloquentHasManyDeep\HasRelationships;

/**
 * Class Project
 * @package App\Models
 * @version July 2, 2020, 11:21 pm PKT
 *
 * @property string date
 * @property string name
 * @property string ref
 * @property integer project_type_id
 * @property number contract_value
 * @property string subject
 * @property string payment_terms
 * @property number labour_charges
 * @property number material_charges
 * @property string project_source
 * @property string note
 */
class Project extends Model implements HasMedia
{
    //Use File Upload Traits
    use HasMediaTrait;
    use UploadFile;
    use HasRelationships;
    use DeleteRecord;

    public $table = 'projects';

    protected $with = ['project_type'];

    public $fillable = [
        'date',
        'quotation_id',
        'project_type_id',
        'subject',
        'payment_terms',
        'labour_charges',
        'material_charges',
        'project_source',
        'project_estimation',
        'user_id',
        'note',
        'category',
        'visits',
        'visit_schedule'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'labour_charges' => 'nullable|numeric',
        'material_charges' => 'nullable|numeric',
        'project_estimation' => 'nullable|numeric',
        'quotation_id' => 'required',
        'visits' => 'required|integer|min:4|max:12',
    ];

    public function project_type()
    {
        return $this->belongsTo(\App\Models\ProjectType::class);
    }

    public function quotation()
    {
        return $this->belongsTo(\App\Models\Quotation::class);
    }

    public function engineer()
    {
        return $this->belongsTo(\App\User::class,'user_id');
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable')->orderBy('id','desc');;
    }

    public function extensions()
    {
        return $this->belongsToMany(\App\Models\Quotation::class, 'project_extension','project_id', 'quotation_id')
            ->withPivot('id','name','value')
            ->withTimestamps();
    }

    // public function lpoouts()
    // {
    //     return $this->belongsToMany(\App\Models\LpoOut::class, 'project_lpoout','lpoout_id', 'project_id')
    //         ->withTimestamps();
    // }

    public function lpoouts()
    {
        return $this->hasMany(Lpoout::class, 'project_id', 'id');
    }

    public function receipts()
    {
        return $this->hasManyDeep(
            \App\Models\Receipt::class,
            [\App\Models\Quotation::class,\App\Models\Invoice::class],
            ['id', 'quotation_id', 'transactionable_id'],
            ['id','id','id']
        )
        ->where('transactionable_type', \App\Models\Invoice::class);;
    }

    public function petty_cash()
    {
        return $this->hasMany(\App\Models\PettyCash::class);
    }

    public function invoices()
    {
        return $this->hasManyDeep(
            \App\Models\Invoice::class,
            [\App\Models\Quotation::class],
            ['id', 'quotation_id'],
            ['quotation_id', 'id']
        )
        ->where('invoices.status', 1);
    }

    public static function boot()
    {
        parent::boot();

        self::deleting(function ($model) {
            $model->petty_cash()->delete();
        });
    }
    public function getVisitStatus()
    {
        $visitSchedule = json_decode($this->visit_schedule, true);
        $currentDate = Carbon::now();
        $pastVisits = [];
        $presentVisits = [];
        $upcomingVisits = [];

        foreach ($visitSchedule as $visitDate) {
            $visitCarbon = Carbon::parse($visitDate);
            if ($visitCarbon->isBefore($currentDate)) {
                $pastVisits[] = $visitCarbon;
            } elseif ($visitCarbon->isToday()) {
                $presentVisits[] = $visitCarbon;
            } else {
                $upcomingVisits[] = $visitCarbon;
            }
        }
        // Return the earliest visit date for sorting
        $earliestVisit = count($visitSchedule) > 0 ? min(array_map(fn($date) => Carbon::parse($date)->timestamp, $visitSchedule)) : null;
        return [
            'past_visits' => $pastVisits,
            'present_visits' => $presentVisits,
            'upcoming_visits' => $upcomingVisits,
            'earliest_visit' => $earliestVisit,
        ];
    }
    public function visitSchedules()
    {
        return $this->hasMany(VisitSchedule::class, 'project_id', 'id');
    }
    
    public function visitScheduleHistories()
    {
        return $this->hasMany(VisitScheduleHistory::class);
    }

    public function getEndDateAttribute(): Carbon
    {
        return Carbon::parse($this->date)->addYear();
    }
    public function history()
    {
        return $this->hasOne(\App\Models\VisitScheduleHistory::class, 'project_id');
    }

    
}
