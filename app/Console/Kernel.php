<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Publish scheduled articles - every 5 minutes
        $schedule->command('articles:publish-scheduled')
            ->everyFiveMinutes()
            ->appendOutputTo(storage_path('logs/schedule.log'));

        // Calculate reading time - daily 02:00 WIB (Asia/Jakarta)
        $schedule->command('articles:calculate-reading-time')
            ->dailyAt('02:00')
            ->timezone('Asia/Jakarta')
            ->appendOutputTo(storage_path('logs/schedule.log'));

        // Clean soft-deleted records >30 days - Sunday 03:00 WIB
        $schedule->command('cleanup:soft-deletes')
            ->weeklyOn(0, '03:00')
            ->timezone('Asia/Jakarta')
            ->appendOutputTo(storage_path('logs/schedule.log'));

        // Generate daily analytics - daily 01:00 WIB
        $schedule->command('analytics:generate-daily')
            ->dailyAt('01:00')
            ->timezone('Asia/Jakarta')
            ->appendOutputTo(storage_path('logs/schedule.log'));

        // Send weekly digest - Monday 08:00 WIB
        $schedule->command('mail:weekly-digest')
            ->weeklyOn(1, '08:00')
            ->timezone('Asia/Jakarta')
            ->appendOutputTo(storage_path('logs/schedule.log'));

        // Clean unverified users >7 days - daily 04:00 WIB
        $schedule->command('users:clean-unverified')
            ->dailyAt('04:00')
            ->timezone('Asia/Jakarta')
            ->appendOutputTo(storage_path('logs/schedule.log'));

        // Update trending cache - every 30 minutes
        $schedule->command('cache:update-trending')
            ->everyThirtyMinutes()
            ->appendOutputTo(storage_path('logs/schedule.log'));

        // Reminder pending approvals to guru - daily 09:00 WIB
        $schedule->command('notifications:pending-approvals')
            ->dailyAt('09:00')
            ->timezone('Asia/Jakarta')
            ->appendOutputTo(storage_path('logs/schedule.log'));

        // Update tag usage count - daily 03:30 WIB
        $schedule->command('tags:update-usage-count')
            ->dailyAt('03:30')
            ->timezone('Asia/Jakarta')
            ->appendOutputTo(storage_path('logs/schedule.log'));
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
