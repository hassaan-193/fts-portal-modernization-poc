<?php

namespace App\Repositories;

use App\Models\PaymentInvoice;
use Illuminate\Support\Facades\DB;
use App\Repositories\BaseRepository;
use App\Models\Account;
use Illuminatech\Balance\Facades\Balance;

/**
 * Class PaymentInvoiceRepository
 * @package App\Repositories
 * @version July 25, 2020, 2:14 pm PKT
*/

class PaymentInvoiceRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'lpoout_id'
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
        return PaymentInvoice::class;
    }

    public function create($input)
    {
        try{
            DB::beginTransaction();
            // start insertion //

            $input['vat'] = $input['vat'] ? $input['amount'] * config('enum.tax_rate') : 0;

            $input['total_amount'] = $input['amount'] + $input['vat'];

            $invoice = $this->model->newInstance($input);
            $invoice->save();

            // update request status
            $invoice->request()->increment('status');

            // -----------------------------------
            // ✅ Bookkeeping Entries Start Here
            // -----------------------------------

            // Fetch necessary accounts
            $expenseAccount = Account::where('code', 'material_expense')->first();
            $payableAccount = Account::where('code', 'account_payable')->first();
            $vatInput = Account::where('code', 'vat_in')->first();
            $balance = new \App\Services\CustomBalanceManager(\DB::connection());
    
            $invoiceNo = $invoice->invoice_no;
            $vendor_name = ($invoice->lpoout ? ($invoice->lpoout->vendor->name ?? '') : ($invoice->vendor->name ?? ''));
    
            // Credit: Accounts Payable
            $balance->decrease($payableAccount->id, $invoice->total_amount, [
                'reference_type' => \App\Models\PaymentInvoice::class,
                'reference_id' => $invoice->id,
                'transaction_detail' => $vendor_name .' - '.$invoiceNo,
            ]);
    
            // Debit: Materials Expense
            $balance->increase($expenseAccount->id, $invoice->amount, [
                'reference_type' => \App\Models\PaymentInvoice::class,
                'reference_id' => $invoice->id,
                'transaction_detail' => $vendor_name .' - '.$invoiceNo,
            ]);

            // Debit: Vat Input
            if($input['vat']){
                $balance->increase($vatInput->id, $invoice->vat, [
                    'reference_type' => \App\Models\PaymentInvoice::class,
                    'reference_id' => $invoice->id,
                    'transaction_detail' => $vendor_name .' - '.$invoiceNo
                ]);
            }
    
            // -----------------------------------
            // ✅ Bookkeeping Done
            // -----------------------------------

            // End Insertion //
            DB::commit();
            return true;

        } catch(Exception $e) {
            DB::rollback();
            return $e;
        }
    }

    public function update($input, $id){
        try {
            DB::beginTransaction();
    
            $invoice = $this->model->find($id);

            // Reverse Previous Bookkeeping Entries
            $expenseAccount = Account::where('code', 'material_expense')->first();
            $payableAccount = Account::where('code', 'account_payable')->first();
            $vatInput = Account::where('code', 'vat_in')->first();
    
            // Reverse original entries
            Balance::increase($payableAccount->id, $invoice->total_amount);
            Balance::decrease($expenseAccount->id, $invoice->amount);
            
            if($invoice->vat)
                Balance::decrease($vatInput->id, $invoice->vat);

            $input['vat'] = $input['vat'] ? $input['amount'] * config('enum.tax_rate') : 0;
            $input['total_amount'] = $input['amount'] + $input['vat'];

            // Update invoice
            $invoice->fill($input);
            $invoice->save();

            $invoiceNo = $invoice->invoice_no;
            $vendor_name = ($invoice->lpoout ? ($invoice->lpoout->vendor->name ?? '') : ($invoice->vendor->name ?? ''));
        
            // Re-post updated Bookkeeping
            $balance = new \App\Services\CustomBalanceManager(\DB::connection());

            // Credit: Accounts Payable
            $balance->decrease($payableAccount->id, $invoice->total_amount, [
                'reference_type' => \App\Models\PaymentInvoice::class,
                'reference_id' => $invoice->id,
                'transaction_detail' => $vendor_name .' - '.$invoiceNo,
            ]);

            // Debit: Materials Expense
            $balance->increase($expenseAccount->id, $invoice->amount, [
                'reference_type' => \App\Models\PaymentInvoice::class,
                'reference_id' => $invoice->id,
                'transaction_detail' => $vendor_name .' - '.$invoiceNo,
            ]);

            // Debit: Vat Input
            if($input['vat']){
                $balance->increase($vatInput->id, $invoice->vat, [
                    'reference_type' => \App\Models\PaymentInvoice::class,
                    'reference_id' => $invoice->id,
                    'transaction_detail' => $vendor_name .' - '.$invoiceNo
                ]);
            }

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
        try {
            DB::beginTransaction();
    
            $invoice = $this->model->find($id);
    
            $expenseAccount = Account::where('code', 'material_expense')->first();
            $payableAccount = Account::where('code', 'account_payable')->first();
            $vatInput = Account::where('code', 'vat_in')->first();
    
            // Reverse Accounting Entries
            $balance = new \App\Services\CustomBalanceManager(\DB::connection());

            $balance->decrease($expenseAccount->id, $invoice->amount);
            $balance->increase($payableAccount->id, $invoice->total_amount);

            if($invoice->vat)
                $balance->decrease($vatInput->id, $invoice->vat);
    
            // Decrement request status
            $invoice->request()->decrement('status');
    
            // Delete related transactions using your custom delete logic
            if($invoice->transaction) {
                app(\App\Repositories\PaymentRepository::class)->delete($invoice->transaction->id);
            }
    
            // Delete invoice
            $invoice->delete();
    
            DB::commit();
            return true;
    
        } catch (Exception $e) {
            DB::rollback();
            return $e;
        }
    }
    
}
