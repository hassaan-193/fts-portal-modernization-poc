<?php

namespace App\Repositories;

use App\Models\Quotation;
use App\Models\QuotationProduct;
use App\Repositories\BaseRepository;

/**
 * Class QuotationRepository
 * @package App\Repositories
 * @version June 28, 2020, 10:28 pm PKT
*/

class QuotationRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'company_id',
        'ref_no'
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
        return Quotation::class;
    }

    public function create($input)
    {
        // Now quotation can be created manually, so needs to handle amount
        if(!$input['amount']){
            $input['amount'] = 0;
        }
        $input['vat'] = $input['amount'] * config('enum.tax_rate');
        $input['total_amount'] = $input['amount'] + $input['vat'];
        $model = $this->model->newInstance($input);

        $model->save();

        // Attach products to the quotation
        if (isset($input['product_id']) && ($model->quotation_type->name === 'Annual Maintenance Contract' || $model->quotation_type->name === 'Maintenance')) {
            $productIds = $input['product_id'];
            $quantities = $input['quantity'];
            $prices = $input['unit_price'];
            $totals = $input['total_price'];

            foreach ($productIds as $index => $productId) {
                QuotationProduct::create([
                    'quotation_id' => $model->id,
                    'product_id' => $productId,
                    'quantity' => $quantities[$index],
                    'unit_price' => $prices[$index],
                    'total_price' => $totals[$index],
                ]);
            }
        }

        return $model;
    }

    public function update($input, $id){
        $query = $this->model->newQuery();
        $model = $query->findOrFail($id);

        $input['vat'] = $input['amount'] * config('enum.tax_rate');
        $input['total_amount'] = $input['amount'] + $input['vat'];

        $model->fill($input);
        $model->save();

        // Update products for the quotation
        if (isset($input['product_id']) && ($model->quotation_type->name === 'Annual Maintenance Contract' || $model->quotation_type->name === 'Maintenance')) {
            // Delete existing products
            $model->products()->delete();

            // Attach new products
            $productIds = $input['product_id'];
            $quantities = $input['quantity'];
            $prices = $input['unit_price'];
            $totals = $input['total_price'];

            foreach ($productIds as $index => $productId) {
                QuotationProduct::create([
                    'quotation_id' => $model->id,
                    'product_id' => $productId,
                    'quantity' => $quantities[$index],
                    'unit_price' => $prices[$index],
                    'total_price' => $totals[$index],
                ]);
            }
        }

        return $model;
    }
}
