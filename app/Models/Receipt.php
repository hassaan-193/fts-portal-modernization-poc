<?php

namespace App\Models;

use Carbon\Carbon;
use Eloquent as Model;
use App\Traits\DeleteRecord;
use Illuminate\Database\Eloquent\Relations\Relation;
use \Staudenmeir\EloquentHasManyDeep\HasRelationships;

/**
 * Class Receipt
 * @package App\Models
 * @version August 4, 2020, 9:08 pm PKT
 *
 * @property string $date_time
 * @property integer $transactionable_id
 * @property string $transactionable_type
 * @property integer $transaction_type
 * @property integer $payment_type
 * @property string $payment_no
 * @property string $clearance_date
 * @property string $bank_name
 * @property number $vat
 * @property number $amount
 * @property number $total
 * @property integer $account_id
 * @property string $note
 * @property integer $status
 */
class Receipt extends Model
{
    use HasRelationships;
    use DeleteRecord;

    public $table = 'transactions';

    public $fillable = [
        'date_time',
        'type',
        'transactionable_id',
        'transactionable_type',
        'transaction_type',
        'payment_type',
        'payment_no',
        'clearance_date',
        'bank_name',
        'vat',
        'amount',
        'total',
        'account_id',
        'note',
        'from_account',
        'status'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'transactionable_id' => 'integer',
        'transactionable_type' => 'string',
        'transaction_type' => 'integer',
        'payment_type' => 'integer',
        'payment_no' => 'string',
        'clearance_date' => 'string',
        'bank_name' => 'string',
        'vat' => 'double',
        'amount' => 'double',
        'total' => 'double',
        'account_id' => 'integer',
        'note' => 'string',
        'status' => 'integer'
    ];

    protected $with = ['transactionable'];

    public function transactionable()
    {
        return $this->morphTo();
    }

    public function transaction_payment_type()
    {
        return $this->belongsTo(\App\Models\Lookup::class, 'payment_type', 'id');
    }

    public function account()
    {
        return $this->belongsTo(\App\Models\Account::class, 'account_id', 'id');
    }

    public function journal_entry()
    {
        return $this->morphOne(Transaction::class, 'reference');
    }

    public function project()
    {
        return $this->hasManyDeep(
            \App\Models\Project::class,
            [\App\Models\Invoice::class,\App\Models\Quotation::class],
            ['id', 'id', 'quotation_id'],
            ['transactionable_id','quotation_id','id']
        )
        ->where('transactionable_type', \App\Models\Invoice::class);;
    }

    public function extension_project()
    {
        return $this->hasManyDeep(
            \App\Models\Project::class,
            [\App\Models\Invoice::class,\App\Models\Quotation::class,\App\Models\ProjectExtension::class],
            ['id', 'id', 'quotation_id','id'],
            ['transactionable_id','quotation_id','id', 'project_id']
        )
        ->where('transactionable_type', \App\Models\Invoice::class);;
    }
}
