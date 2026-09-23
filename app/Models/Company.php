<?php

namespace App\Models;

use App\Traits\UploadFile;
use App\Traits\DeleteRecord;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Class Company
 * @package App\Models
 * @version July 4, 2020, 8:10 pm PKT
 *
 * @property string name
 * @property string email
 * @property string contact_person
 * @property string contact_no
 * @property string contact_no_two
 * @property string vat_no
 * @property string location
 * @property string file
 * @property string billing_address
 * @property string billing_contact_person
 * @property string billing_pob
 * @property string billing_email
 * @property string shipping_address
 * @property string shipping_contact_person
 * @property string shipping_pob
 * @property string shipping_email
 */
class Company extends Authenticatable implements HasMedia
{
    //Use File Upload Traits
    use HasMediaTrait;
    use UploadFile;
    use DeleteRecord;


    public $table = 'companies';

    // protected $with = ['quotations'];

    public $fillable = [
        'name',
        'email',
        'contact_person',
        'contact_no',
        'contact_no_two',
        'vat_no',
        'location',
        'file',
        'billing_address',
        'billing_contact_person',
        'billing_pob',
        'billing_email',
        'shipping_address',
        'shipping_contact_person',
        'shipping_pob',
        'shipping_email',
        'payment_terms',
        'credit_limit',
        'password'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'email' => 'string',
        'contact_person' => 'string',
        'contact_no' => 'string',
        'contact_no_two' => 'string',
        'vat_no' => 'string',
        'location' => 'string',
        'file' => 'string',
        'billing_address' => 'string',
        'billing_contact_person' => 'string',
        'billing_pob' => 'string',
        'billing_email' => 'string',
        'shipping_address' => 'string',
        'shipping_contact_person' => 'string',
        'shipping_pob' => 'string',
        'shipping_email' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required'
    ];

    public function quotations()
    {
        return $this->hasMany(\App\Models\Quotation::class, 'company_id', 'id');
    }

}
