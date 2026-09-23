<?php

namespace App\Repositories;

use App\Models\Role;
// use Spatie\Permission\Models\Role;
use App\Repositories\BaseRepository;

/**
 * Class RoleRepository
 * @package App\Repositories
 * @version March 19, 2020, 7:55 am UTC
*/

class RoleRepository extends BaseRepository
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
        return Role::class;
    }

    public function createRole($input)
    {
        $role = Role::create([
            'name' => $input['name']
        ]);

        if(isset($input['permissions']))
            $role->syncPermissions($input['permissions']);

        return $role;
    }

    public function updateRole($input, $role)
    {
        $role->update([
            'name' => $input['name']
        ]);

        //Check if super-user
        if($role->id ==1 )
            return $role;


        $role->syncPermissions( isset($input['permissions']) ? $input['permissions'] :'' );
        return $role;
    }
}
