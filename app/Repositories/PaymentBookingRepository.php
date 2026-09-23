<?php

namespace App\Repositories;

use App\Models\PaymentBooking;
use App\Repositories\BaseRepository;

/**
 * Class PaymentBookingRepository
 * @package App\Repositories
 *
 * Thin on purpose — every rule that matters (references, review chain, released
 * amounts) lives in App\Services\PaymentBookingService so the web controller and
 * the mobile API share one implementation.
 */
class PaymentBookingRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'reference_no',
        'payee',
        'payment_against',
        'project_cost_centre',
        'purpose',
        'cheque_number',
        'bank_account',
        'cash_account',
        'booking_type',
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
        return PaymentBooking::class;
    }
}
