<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Permission
 * @package App\Models
 * @version March 19, 2020, 9:01 am UTC
 *
 */
class Permission extends \Spatie\Permission\Models\Permission
{

    protected $guarded = [];
}
