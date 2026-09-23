<?php

namespace App\Repositories;
use Exception;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;
use App\Models\InvoiceProductDetail;
use App\Models\InvoiceServiceDetail;
use App\Repositories\BaseRepository;
use Illuminatech\Balance\Facades\Balance;
use App\Models\Account;

/**
 * Class InvoiceRepository
 * @package App\Repositories
 * @version July 5, 2020, 3:24 pm PKT
*/

class InvoiceRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'invoice_type_id',
        'quotation_id'
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
        return Invoice::class;
    }

    public function createInvoice($input, $request)
    {
        try {
            DB::beginTransaction();
    
            // Create invoice
            $invoice = $this->model->newInstance($request->except('amount'));
            $invoice->save();
    
            if (request('file')) {
                $invoice->addMedia(request('file'))->toMediaCollection();
            }
    
            // Calculate amounts
            $amount = 0.0;
            $vat = 0.0;
            $total_amount = 0.0;
            $includeVat = $input['vat'];
    
            if (isset($input['product'])) {
                foreach ($input['product'] as $key => $product) {
                    $vatAmount = $this->includeVat($input['amount'][$key], $includeVat);
                    $lineTotal = $input['amount'][$key] + $vatAmount;
    
                    $vat += $vatAmount;
                    $total_amount += $lineTotal;
    
                    InvoiceProductDetail::create([
                        'invoice_id' => $invoice->id,
                        'product' => $product,
                        'unit' => $input['unit'][$key],
                        'qty' => $input['qty'][$key],
                        'rate' => $input['rate'][$key],
                        'amount' => $input['amount'][$key],
                        'vat' => $vatAmount,
                        'total_amount' => $lineTotal,
                    ]);
                }
            }
    
            if (isset($input['description'])) {
                foreach ($input['description'] as $key => $description) {
                    $serviceAmount = $input['service_amount'][$key];
                    $total_amount += $serviceAmount;
    
                    InvoiceServiceDetail::updateOrCreate([
                        'invoice_id' => $invoice->id,
                        'description' => $description,
                        'amount' => $serviceAmount,
                    ]);
                }
            }
            
            // Update invoice totals
            $invoice->update([
                'amount' => $total_amount - $vat,
                'vat' => $vat,
                'total_amount' => $total_amount,
            ]);
    
            // Update request status
            $invoice->request()->increment('status');
    
            // -----------------------------------
            // ✅ Bookkeeping Entries Start Here
            // -----------------------------------

            // Fetch necessary accounts
            $salesAccount = Account::where('code', 'sales')->first();
            $receivableAccount = Account::where('code', 'account_receivable')->first();
            $vatOutput = Account::where('code', 'vat_out')->first();
            $balance = new \App\Services\CustomBalanceManager(\DB::connection());
    
            $invoiceNo = $invoice->invoice_no;
            $company_name = $invoice->quotation->company->name ?? '';
    
            // Debit: Accounts Receivable
            $balance->increase($receivableAccount->id, $invoice->total_amount, [
                'reference_type' => \App\Models\Invoice::class,
                'reference_id' => $invoice->id,
                'transaction_detail' => $company_name .' - '.$invoiceNo,
            ]);
    
            // Credit: Sales
            $balance->decrease($salesAccount->id, $invoice->amount, [
                'reference_type' => \App\Models\Invoice::class,
                'reference_id' => $invoice->id,
                'transaction_detail' => $company_name .' - '.$invoiceNo,
            ]);

            // Credit: Vat Output
            $balance->decrease($vatOutput->id, $invoice->vat, [
                'reference_type' => \App\Models\Invoice::class,
                'reference_id' => $invoice->id,
                'transaction_detail' => $company_name .' - '.$invoiceNo
            ]);
    
            // -----------------------------------
            // ✅ Bookkeeping Done
            // -----------------------------------
    
            DB::commit();
            return true;
    
        } catch (Exception $e) {
            DB::rollback();
            return $e;
        }
    }
    

    public function updateInvoice($input, $id, $request)
    {
        try {
            DB::beginTransaction();
    
            $invoice = $this->model->find($id);
    
            // Reverse Previous Bookkeeping Entries
            $receivableAccount = Account::where('code', 'account_receivable')->first();
            $salesAccount = Account::where('code', 'sales')->first();
            $vatOutput = Account::where('code', 'vat_out')->first();
    
            $invoiceNo = $invoice->invoice_no ?? ('INV-' . $invoice->id);
            $company_name = $invoice->quotation->company->name ?? '';
    
            // Reverse original entries
            Balance::decrease($receivableAccount->id, $invoice->total_amount);
            Balance::increase($salesAccount->id, $invoice->amount);
            Balance::increase($vatOutput->id, $invoice->vat);

            // Update invoice
            $invoice->fill($request->except('amount'));
            $invoice->save();
    
            if (request('file')) {
                $invoice->addMedia(request('file'))->toMediaCollection();
            }
    
            $vat = 0.0;
            $total_amount = 0.0;
            $includeVat = $input['vat'];
    
            // Handle Product Details
            $invoice->invoice_product_details()->delete();
            if (isset($input['product'])) {
                foreach ($input['product'] as $key => $product) {
                    $vatAmount = $this->includeVat($input['amount'][$key], $includeVat);
                    $lineTotal = $input['amount'][$key] + $vatAmount;
    
                    $vat += $vatAmount;
                    $total_amount += $lineTotal;
    
                    InvoiceProductDetail::updateOrCreate([
                        'invoice_id' => $invoice->id,
                        'product' => $product,
                        'unit' => $input['unit'][$key],
                        'qty' => $input['qty'][$key],
                        'rate' => $input['rate'][$key],
                        'amount' => $input['amount'][$key],
                        'vat' => $vatAmount,
                        'total_amount' => $lineTotal,
                    ]);
                }
            }
    
            // Handle Service Details
            $invoice->invoice_service_details()->delete();
            if (isset($input['description'])) {
                foreach ($input['description'] as $key => $description) {
                    $serviceAmount = $input['service_amount'][$key];
                    $total_amount += $serviceAmount;
    
                    InvoiceServiceDetail::updateOrCreate([
                        'invoice_id' => $invoice->id,
                        'description' => $description,
                        'amount' => $serviceAmount,
                    ]);
                }
            }
    
            // Update invoice totals
            $invoice->update([
                'amount' => $total_amount - $vat,
                'vat' => $vat,
                'total_amount' => $total_amount,
            ]);
    
            // Re-post updated Bookkeeping
            $balance = new \App\Services\CustomBalanceManager(\DB::connection());

            $balance->increase($receivableAccount->id, $invoice->total_amount, [
                'reference_type' => \App\Models\Invoice::class,
                'reference_id' => $invoice->id,
                'transaction_detail' => $company_name .' - '.$invoiceNo

            ]);
    
            $balance->decrease($salesAccount->id, $invoice->amount, [
                'reference_type' => \App\Models\Invoice::class,
                'reference_id' => $invoice->id,
                'transaction_detail' => $company_name .' - '.$invoiceNo

            ]);

            // Credit: Vat Output
            $balance->decrease($vatOutput->id, $invoice->vat, [
                'reference_type' => \App\Models\Invoice::class,
                'reference_id' => $invoice->id,
                'transaction_detail' => $company_name .' - '.$invoiceNo
            ]);
    
            DB::commit();
            return true;
    
        } catch (Exception $e) {
            DB::rollback();
            return $e;
        }
    }
    

    public function delete($id)
    {
        try {
            DB::beginTransaction();
    
            $invoice = $this->model->find($id);
    
            $receivableAccount = Account::where('code', 'account_receivable')->first();
            $salesAccount = Account::where('code', 'sales')->first();
            $vatOutput = Account::where('code', 'vat_out')->first();
    
            // Reverse Accounting Entries
            $balance = new \App\Services\CustomBalanceManager(\DB::connection());

            $balance->decrease($receivableAccount->id, $invoice->total_amount);
            $balance->increase($salesAccount->id, $invoice->amount);
            $balance->increase($vatOutput->id, $invoice->vat);

    
            // Decrement request status
            $invoice->request()->decrement('status');

            // Delete related transactions using your custom delete logic
            if($invoice->transaction) {
                app(\App\Repositories\ReceiptRepository::class)->delete($invoice->transaction->id);
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
    

    function includeVat($amount , $includeVat = true){
        if ($includeVat) {
            return $amount * config('enum.tax_rate');
        } else {
            return 0;
        }
    }
}