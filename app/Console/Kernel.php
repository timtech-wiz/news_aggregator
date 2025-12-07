<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // Fetch general news every hour
        $schedule->command('news:fetch')
            ->hourly()
            ->withoutOverlapping()
            ->runInBackground();

        // Fetch technology news every 2 hours
        $schedule->command('news:fetch --category=technology')
            ->everyTwoHours()
            ->withoutOverlapping();

        // Fetch business news every 3 hours
        $schedule->command('news:fetch --category=business')
            ->everyThreeHours()
            ->withoutOverlapping();

        // Clean old articles daily at 2 AM
        $schedule->command('news:clean --days=30')
            ->dailyAt('02:00');

        // Weekly full cleanup
        $schedule->command('news:clean --days=60')
            ->weeklyOn(1, '03:00');
    }

    protected $commands = [
        \App\Console\Commands\FetchNewsCommand::class,
        \App\Console\Commands\CleanOldArticlesCommand::class,
    ];
}
