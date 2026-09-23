<?php

namespace App\Repositories;

use App\Models\InvoiceRequest;
use Illuminate\Support\Facades\DB;
use App\Repositories\BaseRepository;

/**
 * Class InvoiceRequestRepository
 * @package App\Repositories
 * @version July 11, 2020, 1:11 am PKT
*/

class InvoiceRequestRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'user_id',
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
        return InvoiceRequest::class;
    }

    public function create($input)
    {
        try {
            DB::beginTransaction();

            $quotation = \App\Models\Quotation::find($input['quotation_id']);
            $request = $quotation->invoice_requests()->create($input);
            // calculate invoice amount and vat
            $amount = 0.0;
            $vat = 0.0;
            $total_amount = 0.0;

            if(isset($input['product'])){
                //product details
                foreach ($input['product'] as $key => $product) {
                    if($product){
                        $vat += $input['amount'][$key] * config('enum.tax_rate');
                        $total_amount += $vat + $input['amount'][$key];

                        \App\Models\InvoiceRequestProduct::create(
                        [
                            'request_id' => $request->id,
                            'product' => $product,
                            'unit' => $input['unit'][$key],
                            'qty' => $input['qty'][$key],
                            'rate' => $input['rate'][$key],
                            'amount' => $input['amount'][$key],
                            'vat' => $input['amount'][$key] * config('enum.tax_rate'),
                            'total_amount' => ($input['amount'][$key] * config('enum.tax_rate')) + $input['amount'][$key],
                        ]);
                    }
                }
            }

            DB::commit();

            return true;
        } catch (\Throwable $th) {
            DB::rollback();
            return false;
        }

    }

    public function updateRequest($input, $request)
    {
        try {
            DB::beginTransaction();

            $request->update($input);
            $quotation = \App\Models\Quotation::find($input['quotation_id']);
            $request->requestable()->associate($quotation);
            $request->save();

            // calculate invoice amount and vat
            $amount = 0.0;
            $vat = 0.0;
            $total_amount = 0.0;
            if(isset($input['product'])){
                $request->request_products()->delete();

                //product details
                foreach ($input['product'] as $key => $product) {
                    if($product){
                        $vat += $input['amount'][$key] * config('enum.tax_rate');
                        $total_amount += $vat + $input['amount'][$key];

                        \App\Models\InvoiceRequestProduct::create(
                        [
                            'request_id' => $request->id,
                            'product' => $product,
                            'unit' => $input['unit'][$key],
                            'qty' => $input['qty'][$key],
                            'rate' => $input['rate'][$key],
                            'amount' => $input['amount'][$key],
                            'vat' => $input['amount'][$key] * config('enum.tax_rate'),
                            'total_amount' => ($input['amount'][$key] * config('enum.tax_rate')) + $input['amount'][$key],
                        ]);
                    }
                }
            }else{
                $request->request_products()->delete();
            }

            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollback();
            dd($th);
            return false;
        }
    }
}
