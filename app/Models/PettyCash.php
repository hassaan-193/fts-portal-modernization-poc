<?php

namespace App\Models;

use Eloquent as Model;
use App\Traits\UploadFile;
use App\Traits\DeleteRecord;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

/**
 * Class PettyCash
 * @package App\Models
 * @version September 15, 2020, 11:00 pm PKT
 *
 * @property integer $account_id
 * @property integer $user_id
 * @property string $type
 * @property string $description
 */
class PettyCash extends Model implements HasMedia
{
    use HasMediaTrait;
    use UploadFile;
    use DeleteRecord;

    public $table = 'petty_cashes';

    public $fillable = [
        'date_time',
        'voucher_no',
        'voucher_id',
        'is_user_deduction',
        'is_advance',
        'account_id',
        'user_id',
        'project_id',
        'vendor_id',
        'type',
        'amount',
        'vat',
        'total_amount',
        'description',
    ];

    public function getPettyCashAccountIdAttribute()
    {
        return \App\Models\Account::whereType('PettyCash')->first()->id;
    }

    public function account()
    {
        return $this->belongsTo(\App\Models\Account::class, 'account_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\Employee::class, 'user_id', 'id')->withDefault([
            'name' => '',
        ]);
    }

    public function project()
    {
        return $this->belongsTo(\App\Models\Project::class, 'project_id', 'id');
    }

    public function vendor()
    {
        return $this->belongsTo(\App\Models\Vendor::class, 'vendor_id', 'id')->withDefault([
            'name' => '',
        ]);
    }

    public function payment_type()
    {
        return $this->belongsTo(\App\Models\Lookup::class, 'type', 'id');
    }

    public static function boot()
    {
        parent::boot();
        self::updating(function($model){
            $model->project_id = request()->get('trans_type') === 'Project' ? request()->get('project_id') : null;
        });
    }
}
