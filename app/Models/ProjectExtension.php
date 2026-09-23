<?php

namespace App\Models;

use Eloquent as Model;

class ProjectExtension extends Model
{

    public $table = 'project_extension';

    public $fillable = [
        'name',
        'value'
    ];

    public function project()
    {
        return $this->belongsTo('App\models\Project');
    }

    public function quotation()
    {
        return $this->belongsTo('App\models\Quotation');
    }

    public function invoices()
    {
        return $this->hasManyThrough('App\models\Invoice', 'App\models\Quotation');
    }

    public function getQuotationLinkAttribute(){
        return '<a href="'.route("quotations.show",$this->quotation_id).'">'.$this->quotation->name.'</a>';
    }

        // public function invoices()
    // {
    //     return $this->hasManyThrough(
    //         'App\Models\Invoice',          // The model to access to
    //         'App\Models\ProjectExtension', // The intermediate table that connects the User with the Podcast.
    //         'project_id',                 // The column of the intermediate table that connects to this model by its ID.
    //         'quotation_id',              // The column of the intermediate table that connects the Podcast by its ID.
    //         'id',                      // The column that connects this model with the intermediate model table.
    //         'quotation_id'               // The column of the Audio Files table that ties it to the Podcast.
    //     );
    // }

    // public function lpoins()
    // {
    //     return $this->hasManyThrough(
    //         'App\Models\Lpoin',          // The model to access to
    //         'App\Models\ProjectExtension', // The intermediate table that connects the User with the Podcast.
    //         'project_id',                 // The column of the intermediate table that connects to this model by its ID.
    //         'quotation_id',              // The column of the intermediate table that connects the Podcast by its ID.
    //         'id',                      // The column that connects this model with the intermediate model table.
    //         'quotation_id'               // The column of the Audio Files table that ties it to the Podcast.
    //     );
    // }
}
