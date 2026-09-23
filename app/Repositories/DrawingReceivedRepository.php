<?php

namespace App\Repositories;

use App\Models\DrawingReceived;
use App\Repositories\BaseRepository;

/**
 * Class DrawingReceivedRepository
 * @package App\Repositories
 * @version March 27, 2026, 12:00 am GST
 */
class DrawingReceivedRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'lpoin_id',
        'type_of_work',
        'status',
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
        return DrawingReceived::class;
    }
}
