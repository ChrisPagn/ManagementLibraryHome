<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

/**
 * Define your Closure based console commands here, and then they will automatically be registered.
 */
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/**
 * Schedule the console commands.
 */
Schedule::command('loans:reminders')
         ->dailyAt('09:00')
         ->withoutOverlapping()
         ->appendOutputTo(storage_path('logs/loan-reminders.log'));
