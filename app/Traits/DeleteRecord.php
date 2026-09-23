<?php

namespace App\Traits;

trait DeleteRecord
{
    public static function bootDeleteRecord()
	{
		static::deleting(function ($model) {
            if(!auth()->user()->hasRole('Super-User')){
                return false;
            }
        });
    }

}
