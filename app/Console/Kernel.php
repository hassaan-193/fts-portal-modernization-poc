<?php

namespace App\Console;

use App\Jobs\PDCChequeWeekly;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\ArchiveFinishedProjects;
use App\Console\Commands\ImportInvoices;
use App\Console\Commands\SendBirthdayNotifications;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        ArchiveFinishedProjects::class,
        ImportInvoices::class,
        SendBirthdayNotifications::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->job(new PDCChequeWeekly)->everyMinute();
        $schedule->command('projects:archive-finished')
                 ->dailyAt('02:00')
                 ->withoutOverlapping();

        // Monthly attendance summary – runs on the 1st of each month at 8:00 AM
        $schedule->command('attendance:send-monthly-summary')
                 ->monthlyOn(1, '08:00')
                 ->withoutOverlapping();

        // Birthday notifications – runs daily at 7:00 AM
        $schedule->command('birthday:send-notifications')
                 ->dailyAt('07:00')
                 ->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
