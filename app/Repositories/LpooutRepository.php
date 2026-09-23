<?php

namespace App\Repositories;

use App\Models\Lpoout;
use App\Repositories\BaseRepository;

/**
 * Class LpooutRepository
 * @package App\Repositories
 * @version July 1, 2020, 4:03 pm PKT
*/

class LpooutRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'lpo_type_out',
        'vendor_id',
        'date',
        'trn_no',
        'kindly_attn',
        'payment_type'
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
        return Lpoout::class;
    }

    public function create($input)
    {
        // Calculate VAT and total amount
        $input['vat'] = $input['vat'] ? $input['amount'] * config('enum.tax_rate') : 0;
        $input['total_amount'] = $input['amount'] + $input['vat'];
        
        // Handle items - ensure it's properly formatted
        if (isset($input['items']) && is_array($input['items'])) {
            // Items are already cleaned in controller, just ensure it's set
            $input['items'] = $input['items'];
        } else {
            $input['items'] = [];
        }

        // Clear cheque_date if payment type is not Cheque
        if (empty($input['payment_type']) || $input['payment_type'] !== 'Cheque') {
            $input['cheque_date'] = null;
        }

        $model = $this->model->newInstance($input);
        $model->save();
        
        return $model;
    }

    public function update($input, $id)
    {
        $query = $this->model->newQuery();
        $model = $query->findOrFail($id);
        if (isset($input['status']) && count($input) === 1) {
            $model->status = $input['status'];
            $model->save();
            return $model;
        }

        // Calculate VAT and total amount
        $input['vat'] = $input['vat'] ? $input['amount'] * config('enum.tax_rate') : 0;
        $input['total_amount'] = $input['amount'] + $input['vat'];

        // Handle items - ensure it's properly formatted
        if (isset($input['items']) && is_array($input['items'])) {
            $input['items'] = $input['items'];
        } else {
            $input['items'] = [];
        }

        // Clear cheque_date if payment type is not Cheque
        if (empty($input['payment_type']) || $input['payment_type'] !== 'Cheque') {
            $input['cheque_date'] = null;
        }

        $model->fill($input);
        $model->save();

        return $model;
    }
}
