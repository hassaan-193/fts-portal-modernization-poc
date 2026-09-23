<?php

namespace App\Repositories;

use App\Models\Lookup;
use App\Models\Account;
use App\Models\Invoice;
use App\Models\Receipt;
use Illuminate\Support\Facades\DB;
use App\Repositories\BaseRepository;
use Illuminatech\Balance\Facades\Balance;

/**
 * Class ReceiptRepository
 * @package App\Repositories
 * @version August 4, 2020, 9:08 pm PKT
*/

class ReceiptRepository extends BaseRepository
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
        return Receipt::class;
    }
    public function create($input)
    {
        try{
            DB::beginTransaction();
            // start insertion //

            // get transaction type
            $transaction_type = Lookup::where('name','Receipt')->first();

            // get payment method
            $payment_type = Lookup::find($input['payment_type']);

            // account recievable and vat out
            $receivable = Account::where('code', 'account_receivable')->first();
            $balance = new \App\Services\CustomBalanceManager(\DB::connection());

            if($input['type'] == "company"){
                foreach ($input['invoices'] as $invoice) {
                    // find invoice
                    $actualInvoice = Invoice::find($invoice);

                    // create transaction
                    $transaction = $actualInvoice->transaction()->create([
                        'date_time' =>  $input['date_time'],
                        'type' =>  'Receipt',
                        'account_id' =>  $input['account_id'],
                        'transaction_type' =>  $transaction_type->id,
                        'payment_type' =>  $payment_type->id,
                        'payment_no' =>  $input['payment_no'],
                        'clearance_date' =>  $input['clearance_date'],
                        'bank_name' =>  $input['bank_name'],
                        'total' =>  $actualInvoice->total_amount,
                        'note' =>  $input['note'],
                        'status' => ($payment_type->name != 'Cheque') ? 1 : 0
                    ]);

                    // update invoice status
                    $actualInvoice->update(['status' => 1]);

                    //update account in case of cash
                    if($payment_type->name != 'Cheque'){
                        $company_name = $actualInvoice->quotation->company->name ?? '';
                        
                        // Debit: Actual Account
                        $balance->increase($input['account_id'], $actualInvoice->total_amount,[
                            'reference_id' => $transaction->id,
                            'reference_type' => \App\Models\Receipt::class,
                            'transaction_detail' => $company_name .' - '.$actualInvoice->invoice_no
                        ]);

                        // Credit: Accounts Receivable
                        $balance->decrease($receivable->id, $actualInvoice->total_amount, [
                            'reference_id' => $transaction->id,
                            'reference_type' => \App\Models\Receipt::class,
                            'transaction_detail' => $company_name .' - '.$actualInvoice->invoice_no
                        ]);
                    }
                }
            }else{
                // create transaction
                // $transaction = \App\models\Receipt::create([
                //     'date_time' =>  $input['date_time'],
                //     'type' =>  'Receipt',
                //     'account_id' =>  $input['account_id'],
                //     'transaction_type' =>  $transaction_type->id,
                //     'payment_type' =>  $payment_type->id,
                //     'payment_no' =>  $input['payment_no'],
                //     'clearance_date' =>  $input['clearance_date'],
                //     'bank_name' =>  $input['bank_name'],
                //     'total' =>  $input['total_amount'],
                //     'from_account' =>  $input['from_account'],
                //     'note' =>  $input['note'],
                //     'status' => ($payment_type->name != 'Cheque') ? 1 : 0
                // ]);

                // //update account in case of cash
                // if($payment_type->name != 'Cheque'){
                //     $balance->increase($input['account_id'], $input['total_amount'],[
                //         'transaction_id' => $transaction->id,
                //         'type' => "General",
                //         'transaction_detail' => $input['note']
                //     ]);
                // }
            }
            
            // End Insertion //
            DB::commit();

            return true;
        } catch(Exception $e) {
            DB::rollback();
            return $e;
        }
    }

    public function update($input, $id)
    {
        try{
            DB::beginTransaction();

            // get receipt
            $query = $this->model->newQuery();
            $receipt = $query->find($id);

            // update transaction
            $receipt->update([
                'date_time' =>  $input['date_time'],
                'account_id' =>  $input['account_id'],
                'payment_no' =>  $input['payment_no'],
                'clearance_date' =>  $input['clearance_date'],
                'bank_name' =>  $input['bank_name'],
                'note' =>  $input['note']
            ]);

            DB::commit();

            return true;
        } catch(Exception $e) {
            DB::rollback();
            return $e;
        }
    }

    public function delete($id)
    {
        try{
            DB::beginTransaction();
            // start insertion //

            $query = $this->model->newQuery();
            $receipt = $query->find($id);

            //update invoice status
            if($receipt->transactionable)
                $receipt->transactionable()->decrement('status');

            //update account
            if($receipt->status == 1){
                $balance = new \App\Services\CustomBalanceManager(\DB::connection());
                $receivable = Account::where('code', 'account_receivable')->first();

                // reverse the balance entry for actual account
                $balance->decrease($receipt->account->id, $receipt->total);

                // reverse the balance entry for account receivable
                if ($receipt->transactionable instanceof Invoice) {
                    $invoice = $receipt->transactionable;
                
                    $balance->increase($receivable->id, $invoice->total_amount);
                }
            }

            //delete invoice
            $receipt->delete();

            // End Insertion //
            DB::commit();

            return true;
        } catch(Exception $e) {
            DB::rollback();
            return $e;
        }
    }
}
