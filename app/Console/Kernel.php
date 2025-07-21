<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule)
    {
        // Deploy campaigns every 5 minutes
        $schedule->command('campaigns:deploy')
            ->everyFiveMinutes()
            ->onOneServer() // Prevent multiple servers from running simultaneously
            ->withoutOverlapping()
            ->runInBackground();
        
        // Clean up old deployment logs weekly
        $schedule->command('campaigns:cleanup-deployments')
            ->weekly()
            ->sundays()
            ->at('02:00');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}