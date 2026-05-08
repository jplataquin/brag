<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('app:auto-cancel-battles')->everyMinute()->appendOutputTo(storage_path('logs/cron.log'));
Schedule::command('payments:auto-approve')->everyMinute()->appendOutputTo(storage_path('logs/cron.log'));
