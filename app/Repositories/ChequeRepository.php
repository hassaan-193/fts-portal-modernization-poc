<?php

namespace App\Repositories;

use App\Models\Cheque;
use Illuminate\Support\Facades\DB;
use App\Repositories\BaseRepository;
use Illuminatech\Balance\Facades\Balance;

/**
 * Class ChequeRepository
 * @package App\Repositories
 * @version August 6, 2020, 5:03 pm PKT
*/

class ChequeRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [

    ];

    /**
     * Return searchable fields
     *
     * @return array
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Cheque::class;
    }

    public function update($input, $id)
    {
        try{
            DB::beginTransaction();
            // start insertion //

            $query = $this->model->newQuery();
            $cheque = $query->findOrFail($id);
            $balance = new \App\Services\CustomBalanceManager(\DB::connection());

            if($cheque->type == 'Receipt'){
                $company_name = $cheque->transactionable->quotation->company->name ?? '';

                $balance->increase($input['account_id'], $cheque->total,[
                    'reference_id' => $cheque->id,
                    'reference_type' => \App\Models\Receipt::class,
                    'transaction_detail' => $company_name .' - '.$cheque->transactionable->invoice_no
                ]);

                $receivable = \App\Models\Account::where('code', 'account_receivable')->first();
                $balance->decrease($receivable->id, $cheque->total, [
                    'reference_id' => $cheque->id,
                    'reference_type' => \App\Models\Receipt::class,
                    'transaction_detail' => $company_name .' - '.$cheque->transactionable->invoice_no
                ]);
            }else{
                if ($cheque->transactionable) {
                    $vendor_name = ($cheque->transactionable->lpoout ? ($cheque->transactionable->lpoout->vendor->name ?? '') : ($cheque->transactionable->vendor->name ?? ''));

                    $balance->decrease($input['account_id'], $cheque->total,[
                        'reference_id' => $cheque->id,
                        'reference_type' => \App\Models\Payment::class,
                        'transaction_detail' => $vendor_name .' - '.$cheque->transactionable->invoice_no
                    ]);

                    $payableAccount = \App\Models\Account::where('code', 'account_payable')->first();
                    $balance->increase($payableAccount->id, $cheque->total,[
                        'reference_id' => $cheque->id,
                        'reference_type' => \App\Models\Payment::class,
                        'transaction_detail' => $vendor_name .' - '.$cheque->transactionable->invoice_no
                    ]);
                } else {
                    $balance->decrease($input['account_id'], $cheque->total,[
                        'reference_id' => $cheque->id,
                        'reference_type' => \App\Models\Payment::class,
                        'transaction_detail' => $cheque->note
                    ]);

                    $balance->increase($cheque->expense_account, $cheque->amount,[
                        'reference_id' => $cheque->id,
                        'reference_type' => \App\Models\Payment::class,
                        'transaction_detail' => $cheque->note
                    ]);

                    if($cheque->vat){
                        $vatInput = \App\Models\Account::where('code', 'vat_in')->first();
                        $balance->increase($vatInput->id, $cheque->vat,[
                            'reference_id' => $cheque->id,
                            'reference_type' => \App\Models\Payment::class,
                            'transaction_detail' => $cheque->note
                        ]);
                    }
                }
            }

            //update status
            $cheque->update(['account_id' => $input['account_id'], 'status' => 1 ]);

            // End Insertion //
            DB::commit();

            return true;
        } catch(Exception $e) {
            DB::rollback();
            return $e;
        }
    }
}
