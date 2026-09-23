<?php

namespace App\Repositories;

use App\Models\StafProfile;
use App\Models\StafDates;
use App\Repositories\BaseRepository;

/**
 * Class QuotationRepository
 * @package App\Repositories
 * @version June 28, 2020, 10:28 pm PKT
*/

class StafProfileRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'last_name'
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
        return StafProfile::class;
    }

    public function create($input)
    {
        $model = $this->model->newInstance($input);
        $model->save();

         // add vication receord in date table if it is provided
        if(isset($input['last_vacation_start'])&&isset($input['last_vacation_end'])){
            $modelVicationDate=[
                "staff_id"=>$model->id,
                "start_date"=>$input['last_vacation_start'],
                "end_date"=>$input['last_vacation_end'],
                "days"=> $input['last_vacation_days'] ? $input['last_vacation_days'] : null
            ];
            $dateModel = new StafDates($modelVicationDate);
            $dateModel->save();
        }
        return $model;
    }

    public function update($input, $id){
        $query = $this->model->newQuery();
        $model = $query->findOrFail($id);

        // add vication receord in date table if it's changed
        if(
            isset($input['last_vacation_end']) &&
            $input['last_vacation_end'] != $model->last_vacation_end
        ){
            $modelVicationDate=[
                "staff_id"=>$model->id,
                "start_date"=>$input['last_vacation_start'],
                "end_date"=>$input['last_vacation_end'],
                "days"=> $input['last_vacation_days'] ? $input['last_vacation_days'] : null
            ];
            $dateModel = new StafDates($modelVicationDate);
            $dateModel->save();
        }

        // updating staf profile
        $model->fill($input);
        $model->save();

        return $model;
    }
    public function delete_date($id)
    {
      $date = new StafDates();
      $status=$date->findOrFail($id)->delete();
      return $status;
    }
}
