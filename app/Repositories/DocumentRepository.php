<?php

namespace App\Repositories;

use App\Models\Document;
use App\Repositories\BaseRepository;

/**
 * Class QuotationRepository
 * @package App\Repositories
 * @version June 28, 2020, 10:28 pm PKT
*/

class DocumentRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'type',
        'date'
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
        return Document::class;
    }

    public function create($input)
    {
        $model = $this->model->newInstance($input);
        $model->save();
        return $model;
    }

    public function update($input, $id){
        $query = $this->model->newQuery();
        $model = $query->findOrFail($id);

        $model->fill($input);
        $model->save();

        return $model;
    }
}
