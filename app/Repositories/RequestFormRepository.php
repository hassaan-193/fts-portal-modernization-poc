<?php

namespace App\Repositories;

use App\Models\RequestForm;
use App\Services\PushNotification;
use App\Repositories\BaseRepository;

/**
 * Class RequestFormRepository
 * @package App\Repositories
 * @version September 10, 2020, 1:53 pm PKT
*/

class RequestFormRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name'
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
        return RequestForm::class;
    }

    public function create($input)
    {
        $input['user_id'] = \Auth::id();
        $model = $this->model->newInstance($input);

        $model->save();

        //send notification
        $user = \App\User::whereEmail('Jalaa.rak@example.com')->first();
        if($user){
            $firebase_token = $user->firebase_token ;
            if (isset($firebase_token)) {
                $notification = new PushNotification();
                $msg = \Auth::user()->name.' submit a request form.';
                $notification->sendNotification($msg, $firebase_token, []);
            }
        }

        return $model;
    }
}
