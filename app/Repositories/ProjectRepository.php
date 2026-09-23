<?php

namespace App\Repositories;

use App\Models\Project;
use App\Repositories\BaseRepository;

/**
 * Class ProjectRepository
 * @package App\Repositories
 * @version July 2, 2020, 11:21 pm PKT
*/

class ProjectRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'date',
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
        return Project::class;
    }

    // Attach Extensions
    public function addExtension($project, $input)
    {
        if (! $project->extensions->contains($input['quotation_id']))
                $project->extensions()->attach($input['quotation_id'],$input);

        return true;
    }

    // Remove Extensions
    public function removeExtension($project, $input)
    {
        $project->extensions()->detach($input['id']);
        return true;
    }

     // Attach lpoout
     public function addLpoout($project, $input)
     {
            $project->lpoouts()->syncwithoutdetaching($input['lpoout_id']);
            return true;
     }

    // Remove lpoout
    public function removeLpoout($project, $input)
    {
         $project->lpoouts()->detach($input['lpoout_id']);
         return true;
    }

    public function storeComment($input)
    {
        $project = $this->model->find($input['id']);
        if($project && $input['comments'])
            return $project->comments()->create($input);

        return false;
    }
}
