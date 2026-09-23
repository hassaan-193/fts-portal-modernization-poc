<?php

namespace App\Repositories;

use App\Models\StafProfile;
use App\Models\StaffPayroll;
use Illuminate\Support\Facades\DB;
use App\Repositories\BaseRepository;

/**
 * Class StaffPayrollRepository
 * @package App\Repositories
 * @version November 14, 2022, 12:02 am PKT
*/

class StaffPayrollRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [

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
        return StaffPayroll::class;
    }

    public function create($input)
    {
        try{
            $indexes = explode(',', $input['checked_indexes']);
            array_pop($indexes);
            foreach ($indexes as $key => $index) {
                $index = (int)$index;
                StaffPayroll::create([
                    'date' =>  $input['date'] . '-1',
                    'member_id' =>  $input['id'][$index],
                    'absents' =>  $input['absents'][$index],
                    'hours' =>  $input['hours'][$index],
                    'plus_adjustment' =>  $input['plus_adjustment'][$index],
                    'minus_adjustment' =>  $input['minus_adjustment'][$index],
                    'total_amount' =>  $input['total_amount'][$index],
                    'note' =>  $input['note'][$index],
                ]);
            }
            return true;
        } catch(Exception $e) {
            return false;
        }
    }

    // Get list of staff members based on date
    public function getMembersList($type, $date)
    {
        $members = DB::select("
        SELECT sp.id,sp.name,sp.total_salary,sp.overtime_rate,sp.visa_expiry
        FROM staf_profile sp
        WHERE sp.staf_type = '".$type."'
        AND
        NOT EXISTS (
            SELECT p.member_id FROM payrolls p
            WHERE sp.id = p.member_id
            AND
            p.date = '".$date."-01')");
        return $members;
    }
}
