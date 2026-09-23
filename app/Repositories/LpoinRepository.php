<?php

namespace App\Repositories;

use App\Models\Lpoin;
use App\Repositories\BaseRepository;

/**
 * Class LpoinRepository
 * @package App\Repositories
 * @version June 29, 2020, 12:00 am PKT
*/

class LpoinRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'quotation_id',
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
        return Lpoin::class;
    }
}
