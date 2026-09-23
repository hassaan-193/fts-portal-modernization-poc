<?php

namespace App\Traits;

trait UplodeFileStafProfile
{
    public static function bootUplodeFileStafProfile()
    {
        static::saving(function ($model) {
            $files = ['passport', 'visa', 'contract' ,'labor_card', 'emirates_id', 'insurance' , 'driving_license' , 'jalaa_house_lease','profile_picture'];
            foreach ($files as $file) {
                if (request($file)){
                    $model->addMedia(request($file))->usingName($file)->toMediaCollection();
                }
            }
        });
    }
}
