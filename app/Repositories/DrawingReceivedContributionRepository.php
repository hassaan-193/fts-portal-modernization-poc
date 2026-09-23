<?php

namespace App\Repositories;

use App\Models\DrawingReceivedContribution;
use App\Repositories\BaseRepository;

/**
 * Class DrawingReceivedContributionRepository
 * @package App\Repositories
 * @version March 27, 2026, 12:00 am GST
 */
class DrawingReceivedContributionRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'drawing_received_id',
        'contribution_type',
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
        return DrawingReceivedContribution::class;
    }
}
