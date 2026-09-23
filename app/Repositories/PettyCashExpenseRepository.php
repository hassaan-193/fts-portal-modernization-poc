<?php

namespace App\Repositories;

use App\Models\PettyCashExpense;
use Illuminate\Support\Facades\DB;
use App\Repositories\BaseRepository;
use Illuminatech\Balance\Facades\Balance;

/**
 * Class PettyCashExpenseRepository
 * @package App\Repositories
 * @version September 15, 2020, 11:00 pm PKT
*/

class PettyCashExpenseRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'date',
        'amount',
        'category'
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
        return PettyCashExpense::class;
    }

    public function create($input)
    {
        try{
            DB::beginTransaction();
            // start insertion //

            $petty_cash = $this->model->newInstance($input);
            $petty_cash->save();

            // End Insertion //
            DB::commit();

            return true;
        } catch(Exception $e) {
            DB::rollback();
            return $e;
        }
    }
}
