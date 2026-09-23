<?php

namespace App\Traits;

trait UploadFile
{
   public static function bootUploadFile()
    {
        static::saving(function ($model) {
            $route = request()->route();
            $action = $route ? $route->getActionName() : '';

            // Skip only for LpoinController@store
            if (str_contains($action, 'LpoinController@store')) {
                return;
            }

            if (request()->hasFile('file')) {
                $model->addMedia(request()->file('file'))->toMediaCollection();
            }
        });
    }

}
