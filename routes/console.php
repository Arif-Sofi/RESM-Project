<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('app:send-event-reminders')->everyMinute();
Schedule::command('events:update-status')->everyMinute(); //run "docker-compose exec app php artisan schedule:work" at separate terminal
