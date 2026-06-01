<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('campaigns:send-scheduled')->everyMinute();
Schedule::command('clients:send-followup-reminders')->dailyAt('08:00');

// Retry pending emails every hour — picks up any emails once daily SMTP limits reset at midnight
Schedule::command('emails:retry-pending')->hourly();

