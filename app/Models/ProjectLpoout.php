<?php

namespace App\Models;

use Eloquent as Model;

class ProjectLpoout extends Model
{

    public $table = 'project_lpoout';


    public function project()
    {
        return $this->belongsTo('App\models\Project');
    }

    public function lpoout()
    {
        return $this->belongsTo('App\models\Lpoout');
    }

    public function getLpooutLinkAttribute(){
        return '<a href="'.route("lpoouts.show",$this->lpoout_id).'">'.$this->lpoout->name.'</a>';
    }

    public function getVendorLinkAttribute(){
        return '<a href="'.route("vendors.show",$this->lpoout->vendor->id).'">'.$this->lpoout->vendor->name.'</a>';
    }
}
