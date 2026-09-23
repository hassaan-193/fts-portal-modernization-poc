<?php

namespace App\Repositories;

use App\Models\Tasks;
use App\Repositories\BaseRepository;

/**
 * Class QuotationRepository
 * @package App\Repositories
 * @version June 28, 2020, 10:28 pm PKT
*/

class TasksRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'description',
        'assigned'
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
        return Tasks::class;
    }

    public function create($input)
    {
        $model = $this->model->newInstance($input);
        $model->save();

        // send task email to assinee
        $this->sendTaskEmail($model);
        return $model;
    }

    public function sendTaskEmail($task){
        $to_name = $task->engineer->name;
        $to_email = $task->engineer->email;
        $subject = 'Task Assigned: ' . $task->title;

        \Mail::send('emails.task', ['task' => $task], function($message) use ($to_name, $to_email, $subject) {
            $message->to($to_email, $to_name)
                ->subject($subject);
        });
    }

    public function update($input, $id){
        $query = $this->model->newQuery();
        $model = $query->findOrFail($id);
        $model->fill($input);
        $model->save();

        return $model;
    }
}
