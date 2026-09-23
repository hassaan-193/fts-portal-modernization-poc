<?php

namespace App\Providers;

use App\Services\GeoValidationService;
use App\Services\ShiftRuleService;
use App\Services\AttendanceSessionService;
use App\Services\AttendanceEvaluationService;
use App\Services\QRValidationService;
use Illuminate\Support\ServiceProvider;


class AttendanceServiceProvider extends ServiceProvider
{
    /**
     * Register services
     */
    public function register()
    {
        // Register as singletons for better performance
        $this->app->singleton(GeoValidationService::class);
        $this->app->singleton(ShiftRuleService::class);
        $this->app->singleton(QRValidationService::class);
        $this->app->singleton(AttendanceSessionService::class);
        $this->app->singleton(AttendanceEvaluationService::class);
    }

    /**
     * Boot services
     */
    public function boot()
    {
        // Macros for convenience (optional)
        // These allow you to use helper methods on models
        
        // Example usage in controller:
        // $staff->evaluateAttendanceToday()
    }
}
