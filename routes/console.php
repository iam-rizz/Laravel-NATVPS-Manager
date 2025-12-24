<?php

use App\Models\Setting;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Cron Heartbeat - runs every minute to track scheduler health
Schedule::command('cron:heartbeat')
    ->everyMinute()
    ->runInBackground();

// Server Health Check - runs every minute, command checks interval internally
Schedule::command('server:connection-test')
    ->everyMinute()
    ->when(function () {
        return Setting::get('server_health_check_enabled', false);
    })
    ->withoutOverlapping()
    ->runInBackground();

// VPS Resource Monitoring - runs based on configured interval
Schedule::command('vps:monitor-resources')
    ->everyFifteenMinutes()
    ->when(function () {
        return Setting::get('resource_monitor_enabled', false);
    })
    ->withoutOverlapping()
    ->runInBackground();

// Audit Log Cleanup - runs daily at midnight
Schedule::command('audit:cleanup')
    ->daily()
    ->at('00:00')
    ->withoutOverlapping()
    ->runInBackground();
