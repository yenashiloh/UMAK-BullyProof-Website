<?php

use Illuminate\Console\Scheduling\Schedule;

return function (Schedule $schedule) {
    // Run every hour to check for expired appointments
    $schedule->command('appointments:auto-reschedule')
         ->hourlyAt(32) // Run at 32 minutes past every hour
         ->appendOutputTo(storage_path('logs/auto-reschedule.log'));
};