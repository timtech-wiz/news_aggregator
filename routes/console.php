<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('news:fetch')
    ->everyMinute()
    ->withoutOverlapping()
    ->runInBackground();

Schedule::command('news:fetch', ['--q' => 'technology'])
    ->everyTwoHours()
    ->withoutOverlapping();

Schedule::command('news:fetch', ['--q' => 'business'])
    ->everyThreeHours()
    ->withoutOverlapping();

Schedule::command('news:clean', ['--days' => 30])
    ->dailyAt('02:00');

Schedule::command('news:clean', ['--days' => 60])
    ->weeklyOn(1, '03:00');
