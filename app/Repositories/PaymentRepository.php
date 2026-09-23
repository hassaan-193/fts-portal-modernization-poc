<?php

namespace App\Repositories;

use App\Models\Lookup;
use App\Models\Account;
use App\Models\PaymentInvoice;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use App\Repositories\BaseRepository;
use Illuminatech\Balance\Facades\Balance;
/**
 * Class PaymentRepository
 * @package App\Repositories
 * @version September 19, 2020, 12:43 am PKT
*/

class PaymentRepository extends BaseRepository
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
        return Payment::class;
    }

    public function create($input)
    {
        try{
            DB::beginTransaction();
            // start insertion //

            // get transaction type
            $transaction_type = Lookup::where('name','Payment')->first();

            // get payment method
            $payment_type = Lookup::find($input['payment_type']);

            // account payable and vat in
            $payableAccount = Account::where('code', 'account_payable')->first();
            $vatInput = Account::where('code', 'vat_in')->first();

            $balance = new \App\Services\CustomBalanceManager(\DB::connection());

            if($input['type'] == "vendor"){
                foreach ($input['invoices'] ?? [] as $invoice) {
                    // find invoice
                    $actualInvoice = PaymentInvoice::find($invoice);

                    // create transaction
                    $transaction = $actualInvoice->transaction()->create([
                        'date_time' =>  $input['date_time'],
                        'type' =>  'Payment',
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
                        $vendor_name = ($actualInvoice->lpoout ? ($actualInvoice->lpoout->vendor->name ?? '') : ($actualInvoice->vendor->name ?? ''));
                        
                        $balance->decrease($input['account_id'], $actualInvoice->total_amount,[
                            'reference_id' => $transaction->id,
                            'reference_type' => \App\Models\Payment::class,
                            'transaction_detail' => $vendor_name .' - '.$actualInvoice->invoice_no
                        ]);

                        $balance->increase($payableAccount->id, $actualInvoice->total_amount, [
                            'reference_id' => $transaction->id,
                            'reference_type' => \App\Models\Payment::class,
                            'transaction_detail' => $vendor_name .' - '.$actualInvoice->invoice_no
                        ]);
                    }
                }
            }else{
                
                $input['vat'] = $input['vat'] ? $input['total_amount'] * config('enum.tax_rate') : 0;
                $input['total_amount'] = $input['total_amount'] + $input['vat'];

                // create transaction
                $transaction = \App\models\Payment::create([
                    'date_time' =>  $input['date_time'],
                    'type' =>  'Payment',
                    'expense_account' =>  $input['expense_account'],
                    'account_id' =>  $input['account_id'],
                    'transaction_type' =>  $transaction_type->id,
                    'payment_type' =>  $payment_type->id,
                    'payment_no' =>  $input['payment_no'],
                    'clearance_date' =>  $input['clearance_date'],
                    'bank_name' =>  $input['bank_name'],
                    'vat' =>  $input['vat'],
                    'amount' =>  $input['total_amount'] - $input['vat'],
                    'total' =>  $input['total_amount'],
                    'note' =>  $input['note'],
                    'status' => ($payment_type->name != 'Cheque') ? 1 : 0
                ]);

                //update account in case of cash
                if($payment_type->name != 'Cheque'){
                    
                    // Credit: Actual Account
                    $balance->decrease($input['account_id'], $transaction->total,[
                        'reference_id' => $transaction->id,
                        'reference_type' => \App\Models\Payment::class,
                        'transaction_detail' => $input['note'] ?? ''
                    ]);
                    
                    // Debit: Materials Expense
                    $balance->increase($input['expense_account'], $transaction->amount, [
                        'reference_id' => $transaction->id,
                        'reference_type' => \App\Models\Payment::class,
                        'transaction_detail' => $input['note'] ?? ''
                    ]);

                    // Update the balance of the VAT in account
                    if($input['vat']){
                        $balance->increase($vatInput->id, $transaction->vat, [
                            'reference_id' => $transaction->id,
                            'reference_type' => \App\Models\Payment::class,
                            'transaction_detail' => $input['note'] ?? ''
                        ]);
                    }
                }
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
            // start insertion //

            // get payment
            $query = $this->model->newQuery();
            $payment = $query->find($id);

            // update transaction
            $payment->update([
                'date_time' =>  $input['date_time'],
                'account_id' =>  $input['account_id'],
                'payment_no' =>  $input['payment_no'],
                'clearance_date' =>  $input['clearance_date'],
                'bank_name' =>  $input['bank_name'],
                'note' =>  $input['note']
            ]);

            // End Insertion //
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
            $payment = $query->find($id);

            if($payment->status == 1){
                $balance = new \App\Services\CustomBalanceManager(\DB::connection());
                if ($payment->transactionable instanceof PaymentInvoice) {
                    $payableAccount = Account::where('code', 'account_payable')->first();
                    $invoice = $payment->transactionable;
                    
                    //update invoice status
                    $payment->transactionable()->decrement('status');
                    
                    $balance->increase($payment->account->id, $payment->total);
                    $balance->decrease($payableAccount->id, $invoice->total_amount);
                }else {
                    $vatInput = Account::where('code', 'vat_in')->first();

                    $balance->increase($payment->account->id, $payment->total);
                    $balance->decrease($payment->expense_account, $payment->amount);
                    $balance->decrease($vatInput->id, $payment->vat);
                }
            }

            //delete invoice
            $payment->delete();

            // End Insertion //
            DB::commit();

            return true;
        } catch(Exception $e) {
            DB::rollback();
            return $e;
        }
    }
}
