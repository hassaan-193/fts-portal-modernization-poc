<?php

namespace App\Models;

use Eloquent as Model;
use App\Traits\UploadFile;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
/**
 * Class Vendor
 * @package App\Models
 * @version July 1, 2020, 3:27 pm PKT
 *
 * @property string name
 * @property string email
 * @property string contact_person
 * @property string contact_no
 * @property string contact_no_two
 * @property string vat_no
 * @property string location
 * @property string file
 * @property string payment_preference
 * @property integer pdc_number_of_days
 * @property string pdc_payment_option
 * @property string vendor_specialization
 */
class Vendor extends Model implements HasMedia
{
    //Use File Upload Traits
    use HasMediaTrait;
    use UploadFile;

    public $table = 'vendors';

    protected $with = ['media'];

    public $fillable = [
        'name',
        'email',
        'emails',
        'contact_person',
        'contact_no',
        'contact_no_two',
        'vat_no',
        'location',
        'file',
        'payment_terms',
        'credit_limit',
        'payment_preference',
        'pdc_number_of_days',
        'pdc_payment_option',
        'vendor_specialization',
        'terms_and_conditions',
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
        'emails' => 'array',
        'contact_person' => 'string',
        'contact_no' => 'string',
        'contact_no_two' => 'string',
        'vat_no' => 'string',
        'location' => 'string',
        'file' => 'string',
        'payment_preference' => 'string',
        'pdc_number_of_days' => 'integer',
        'pdc_payment_option' => 'string',
        'vendor_specialization' => 'string'
    ];

    /**
     * Every address this vendor should be mailed on: the primary `email` first,
     * then the additional ones, de-duplicated and validated.
     *
     * @return array<int, string>
     */
    public function allEmails(): array
    {
        $emails = array_merge(
            [$this->email],
            is_array($this->emails) ? $this->emails : []
        );

        $emails = array_filter(array_map('trim', $emails), function ($email) {
            return $email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL);
        });

        return array_values(array_unique($emails));
    }

    /**
     * The additional addresses only (everything except the primary `email`).
     *
     * @return array<int, string>
     */
    public function additionalEmails(): array
    {
        return array_values(array_filter(
            is_array($this->emails) ? array_map('trim', $this->emails) : [],
            function ($email) {
                return $email !== ''
                    && filter_var($email, FILTER_VALIDATE_EMAIL)
                    && strcasecmp($email, (string) $this->email) !== 0;
            }
        ));
    }

    /**
    * @return \Illuminate\Database\Eloquent\Relations\hasMany
    **/
    public function lpoouts()
    {
        return $this->hasMany(\App\Models\Lpoout::class);
    }
}
