<?php

namespace App\Models;

use Eloquent as Model;
use App\Traits\UploadFile;
use App\Traits\DeleteRecord;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
/**
 * Class Lpoout
 * @package App\Models
 * @version July 1, 2020, 4:03 pm PKT
 *
 * @property string name
 * @property integer lpo_type_out
 * @property integer vendor_id
 * @property string date
 * @property number total_amount
 * @property string file
 */
class Lpoout extends Model implements HasMedia
{
    //Use File Upload Traits
    use HasMediaTrait;
    use UploadFile;
    use DeleteRecord;

    public $table = 'lpoouts';

    protected $with = ['lpo_out_type','vendor'];

    public $fillable = [
        'lpo_invoice_no',
        'name',
        'lpo_out_type_id',
        'project_id',
        'vendor_id',
        'trn_no',
        'kindly_attn',
        'date',
        'amount',
        'payment_type',
        'cheque_date',
        'vat',
        'total_amount',
        'file',
        'items',
        'pricing_mode',
        'has_item_code',
        'status',
        'terms',
        'lpo_payment_preference_option',
        'lpo_payment_preference',
        'lpo_pdc_number_of_days',
        'lpo_pdc_payment_option',
        // Revision fields
        'revision_number',
        'parent_lpoout_id',
        'is_latest_revision',
        'revised_by',
        'revision_reason',
        'revised_at',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'lpo_out_type_id' => 'integer',
        'vendor_id' => 'integer',
        'file' => 'string',
        'project_id' => 'integer',
        'items' => 'array',
        'pricing_mode' => 'string',
        'has_item_code' => 'boolean',
        'cheque_date' => 'date',
        'lpo_payment_preference_option' => 'string',
        'lpo_payment_preference' => 'string',
        'lpo_pdc_number_of_days' => 'integer',
        'lpo_pdc_payment_option' => 'string',
        'revision_number' => 'integer',
        'parent_lpoout_id' => 'integer',
        'is_latest_revision' => 'boolean',
        'revised_at' => 'datetime',
    ];

    /**
     * Lump-sum pricing: one batch total instead of per-row costs, so the
     * UNIT PRICE / TOTAL columns are dropped from prints and vendor emails.
     */
    public function isLumpSum(): bool
    {
        return ($this->pricing_mode ?? 'unit') === 'lump';
    }

    /**
     * Whether line items carry an item code (extra column before the description).
     */
    public function hasItemCode(): bool
    {
        return (bool) $this->has_item_code;
    }

    /**
    * @return \Illuminate\Database\Eloquent\Relations\belongsTo
    **/
    public function lpo_out_type()
    {
        return $this->belongsTo(\App\Models\LpoOutType::class, 'lpo_out_type_id', 'id');
    }
    /**
    * @return \Illuminate\Database\Eloquent\Relations\belongsTo
    **/
    public function vendor()
    {
        return $this->belongsTo(\App\Models\Vendor::class, 'vendor_id', 'id')->withDefault(['name' => '']);
    }

    public function invoice_request()
    {
        return $this->morphOne(\App\Models\InvoiceRequest::class, 'requestable');
    }

    public function project()
    {
        return $this->belongsTo(\App\Models\Project::class, 'project_id', 'id')->withDefault();
    }

    public function purchaseOrder()
    {
        return $this->hasOne(\App\Models\PurchaseOrder::class, 'lpout_id', 'id');
    }

    // Purchase Order requests spawned by revising this LPO
    public function revisionRequests()
    {
        return $this->hasMany(\App\Models\PurchaseOrder::class, 'revised_from_lpoout_id', 'id');
    }

    // Revision relationships
    public function parentRevision()
    {
        return $this->belongsTo(Lpoout::class, 'parent_lpoout_id');
    }

    public function childRevisions()
    {
        return $this->hasMany(Lpoout::class, 'parent_lpoout_id');
    }

    public function allRevisions()
    {
        // Walk up to root then load all siblings sharing the same logical LPO number
        return Lpoout::where('lpo_invoice_no', $this->lpo_invoice_no)->orderBy('revision_number');
    }

    public function revisedByUser()
    {
        return $this->belongsTo(\App\User::class, 'revised_by');
    }

    // Payment invoices linked to this LPO
    public function paymentInvoices()
    {
        return $this->hasMany(\App\Models\PaymentInvoice::class, 'lpoout_id');
    }

    /**
     * Returns true when no payment has been initiated against this LPO.
     * Only the latest revision of an LPO can be revised.
     */
    public function canBeRevised(): bool
    {
        if (!$this->is_latest_revision) {
            return false;
        }

        return $this->paymentInvoices()->count() === 0;
    }

    /**
     * True when a revision request spawned from this LPO is still in flight
     * (not yet rejected by department/admin, not yet turned into a new LPO).
     */
    public function hasPendingRevisionRequest(): bool
    {
        return $this->revisionRequests()
            ->where('department_status', '!=', 'Rejected')
            ->where('status', '!=', 'Rejected')
            ->where('status', '!=', 'Admin Approved')
            ->exists();
    }

    public function getVendorLinkAttribute(){
        if ($this->vendor && $this->vendor->id) {
            return '<a href="'.route("vendors.show",$this->vendor->id).'">'.$this->vendor->name.'</a>';
        }
        return '-';
    }

    public static function boot()
    {
        parent::boot();

        // Auto-generate invoice number on creating
        self::creating(function($model){
            if (empty($model->lpo_invoice_no)) {
                $model->lpo_invoice_no = self::generateInvoiceNumber();
            }
            
            // Only set project_id if lpo_out_type_id is in the request
            $lpo_out_type_id = request()->get('lpo_out_type_id');
            if ($lpo_out_type_id) {
                $type = \App\Models\LpoOutType::find($lpo_out_type_id);
                if ($type && $type->name != 'Project') {
                    $model->project_id = null;
                } elseif ($type && $type->name === 'Project') {
                    $model->project_id = request()->get('project_id');
                }
            }
        });

        self::updating(function($model){

            // ✅ Skip logic if we're doing a simple status-only update
            if (request()->has('status') && request()->keys() === ['status']) {
                return;
            }

            // ✅ Only run if lpo_out_type_id is present in request
            if (request()->has('lpo_out_type_id')) {
                $type = \App\Models\LpoOutType::find(request()->get('lpo_out_type_id'));

                if ($type) {
                    $model->project_id = $type->name != 'Project'
                        ? null
                        : request()->get('project_id');
                }
            }
        });

    }

    /**
     * Generate unique invoice number
     * Format: {COMPANY3}-LPO-{COUNTER}-{MMYY}
     * Example: TELE-LPO-3470-0726
     */
    public static function generateInvoiceNumber()
    {
        // Get company from quotation (if available through project)
        $company = null;
        if (request()->has('project_id') && request()->get('project_id')) {
            $project = \App\Models\Project::find(request()->get('project_id'));
            if ($project && $project->quotation) {
                $company = $project->quotation->company;
            }
        }

        return self::buildLpoInvoiceNo(self::companyPrefix($company));
    }

    /**
     * First 3 uppercase letters of the company name, or 'COMP' as a fallback.
     */
    public static function companyPrefix($company): string
    {
        if ($company && !empty($company->name)) {
            $letters = preg_replace('/[^A-Za-z]/', '', $company->name);
            if (!empty($letters)) {
                return strtoupper(substr($letters, 0, 3));
            }
        }

        return 'COMP';
    }

    /**
     * Build a full LPO invoice number for the given company prefix.
     * Format: {COMPANY3}-LPO-{COUNTER}-{MMYY}
     */
    public static function buildLpoInvoiceNo(?string $companyPrefix = null): string
    {
        $companyPrefix = $companyPrefix ?: 'COMP';
        $counter = str_pad((string) self::nextLpoCounter(), 3, '0', STR_PAD_LEFT);

        return $companyPrefix . '-LPO-' . $counter . '-' . date('my');
    }

    /**
     * The next GLOBAL LPO counter.
     *
     * The counter is shared across every company and every month and never
     * resets. It is always max(highest existing counter + 1, start number),
     * so once the data catches up it simply keeps incrementing.
     */
    public static function nextLpoCounter(): int
    {
        $start = (int) config('purchase-orders.lpo_start_number', 1);

        $maxNumber = 0;
        $invoiceNumbers = self::where('lpo_invoice_no', 'LIKE', '%-LPO-%')
            ->pluck('lpo_invoice_no');

        foreach ($invoiceNumbers as $lpoNo) {
            if (preg_match('/-LPO-(\d+)-\d{4}$/', (string) $lpoNo, $matches)) {
                $maxNumber = max($maxNumber, (int) $matches[1]);
            }
        }

        return max($maxNumber + 1, $start);
    }
}
