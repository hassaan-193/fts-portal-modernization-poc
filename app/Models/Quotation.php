<?php

namespace App\Models;

use Eloquent as Model;
use App\Traits\UploadFile;
use App\Traits\DeleteRecord;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use \Staudenmeir\EloquentHasManyDeep\HasRelationships;

/**
 * Class Quotation
 * @package App\Models
 * @version June 28, 2020, 10:28 pm PKT
 *
 * @property \App\Models\Companies companies
 * @property string name
 * @property integer company_id
 * @property string ref_no
 * @property number contract_value
 * @property string date
 * @property string subject
 * @property string location
 * @property string file
 */
class Quotation extends Model implements HasMedia
{
    //Use File Upload Traits
    use HasMediaTrait;
    use UploadFile;
    use DeleteRecord;
    use HasRelationships;

    public $table = 'quotations';

    public $fillable = [
        'name',
        'company_id',
        'quotation_type_id',
        'quotation_company',
        'ref_no',
        'date',
        'subject',
        'location',
        'amount',
        'vat',
        'total_amount',
        'file',
        'payment',
        'exclusion',
        'status',
        'category',
        'number_of_visits',
        'reference_project_id',
        'approved_by',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'company_id' => 'integer',
        'ref_no' => 'string',
        'subject' => 'string',
        'location' => 'string',
        'file' => 'string',
        'status' => 'integer',
        'approved_by' => 'integer',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required',
        'company_id' => 'required',
        'ref_no' => 'nullable|unique:quotations,ref_no',
        'amount' => 'nullable|numeric',
    ];

    protected $with = ['company', 'quotation_type'];

    public function getNameAttribute()
    {
        $company_name = $this->relationLoaded('company') && $this->company 
            ? "({$this->company->name})" 
            : "";
    
        $type = $this->relationLoaded('quotation_type') && $this->quotation_type 
            ? " ({$this->quotation_type->name}) " 
            : " ";
    
        $name = isset($this->attributes['name']) ? "({$this->attributes['name']})" : "(N/A)";
    
        return $company_name . $type . $name;
    }
    /**
    * @return \Illuminate\Database\Eloquent\Relations\belongsTo
    **/
    public function company()
    {
        return $this->belongsTo(\App\Models\Company::class, 'company_id', 'id');
    }

    public function quotation_type()
    {
        return $this->belongsTo(\App\Models\Lookup::class, 'quotation_type_id', 'id')->withDefault(['name' => '']);
    }

    public function quotation_company()
    {
        return $this->belongsTo(\App\Models\Lookup::class, 'quotation_company', 'id')->withDefault(['name' => '']);
    }

    public function project()
    {
        return $this->hasOne(\App\Models\Project::class, 'quotation_id', 'id');
    }

    public function projects()
    {
        return $this->hasMany(\App\Models\Project::class, 'quotation_id', 'id');
    }

    public function referenceProject()
    {
        return $this->belongsTo(\App\Models\Project::class, 'reference_project_id', 'id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(\App\User::class, 'approved_by', 'id')->withDefault(['name' => '']);
    }

    public function extension()
    {
        return $this->hasOne(\App\Models\ProjectExtension::class, 'quotation_id', 'id');
    }

    public function invoices()
    {
        return $this->hasMany(\App\Models\Invoice::class, 'quotation_id', 'id');
    }

    public function lpoins()
    {
        return $this->hasOne(\App\Models\Lpoin::class, 'quotation_id', 'id')->withDefault(['ref_no' => '']);
    }

    public function invoice_requests()
    {
        return $this->morphMany(\App\Models\InvoiceRequest::class, 'requestable');
    }

    public function products()
    {
        return $this->hasMany(\App\Models\QuotationProduct::class, 'quotation_id', 'id');
    }

    public function receipts()
    {
        return $this->hasManyDeep(
            \App\Models\Receipt::class,
            [\App\Models\Invoice::class],
            ['quotation_id', 'transactionable_id'],
            ['id','id']
        )
        ->where('transactionable_type', \App\Models\Invoice::class);;
    }

    public function extension_project()
    {
        return $this->hasOneThrough(
            'App\Models\Project',
            'App\Models\ProjectExtension',
            'quotation_id', // Foreign key on cars table...
            'id', // Foreign key on owners table...
            'id', // Local key on mechanics table...
            'project_id' // Local key on cars table...
        );
    }

    public function delete()
    {
        \DB::beginTransaction();
        $this->invoice_requests()->delete();

        $result = parent::delete();
        if ( ! $result)
            throw new \RuntimeException('Model deletion failed');

        \DB::commit();

        return $result;
    }
}
