<?php

namespace App\Services;

use App\Models\Setting;
use Carbon\Carbon;

class CronService
{
    const STATUS_RUNNING = 'running';
    const STATUS_STOPPED = 'stopped';
    const STATUS_NEVER = 'never';

    const HEARTBEAT_THRESHOLD = 5; // minutes
    const WARNING_DISMISS_HOURS = 24;

    /**
     * Update heartbeat timestamp.
     */
    public function updateHeartbeat(): void
    {
        Setting::set('cron_last_run', Carbon::now()->toIso8601String());
    }

    /**
     * Get current cron status.
     *
     * @return array{status: string, message: string, is_warning: bool, last_run: ?string}
     */
    public function getStatus(): array
    {
        $lastRun = Setting::get('cron_last_run');

        if ($lastRun === null) {
            return [
                'status' => self::STATUS_NEVER,
                'message' => __('app.cron_never_run'),
                'is_warning' => true,
                'last_run' => null,
            ];
        }

        $lastRunCarbon = Carbon::parse($lastRun);
        $minutesAgo = $lastRunCarbon->diffInMinutes(Carbon::now());

        if ($minutesAgo <= self::HEARTBEAT_THRESHOLD) {
            return [
                'status' => self::STATUS_RUNNING,
                'message' => __('app.cron_running'),
                'is_warning' => false,
                'last_run' => $lastRunCarbon->toDateTimeString(),
            ];
        }

        return [
            'status' => self::STATUS_STOPPED,
            'message' => __('app.cron_stopped'),
            'is_warning' => true,
            'last_run' => $lastRunCarbon->toDateTimeString(),
        ];
    }

    /**
     * Get application paths for cron setup.
     *
     * @return array{base_path: string, php_binary: string}
     */
    public function getPaths(): array
    {
        return [
            'base_path' => base_path(),
            'php_binary' => PHP_BINARY,
        ];
    }

    /**
     * Check if health check should run based on interval.
     */
    public function shouldRunHealthCheck(): bool
    {
        $enabled = Setting::get('server_health_check_enabled', false);

        if (!$enabled) {
            return false;
        }

        $interval = Setting::get('server_health_check_interval', 5);
        $lastRun = Setting::get('server_health_check_last_run');

        if ($lastRun === null) {
            return true;
        }

        $lastRunCarbon = Carbon::parse($lastRun);
        $minutesAgo = $lastRunCarbon->diffInMinutes(Carbon::now());

        return $minutesAgo >= $interval;
    }

    /**
     * Get cron command based on environment.
     */
    public function getCronCommand(): string
    {
        $environment = Setting::get('cron_environment', 'vps');
        $paths = $this->getPaths();

        if ($environment === 'hosting') {
            // Format for cPanel/Plesk - single line command
            return sprintf(
                '%s %s/artisan schedule:run >> /dev/null 2>&1',
                $paths['php_binary'],
                $paths['base_path']
            );
        }

        // VPS format - full crontab entry with cd
        return sprintf(
            '* * * * * cd %s && %s artisan schedule:run >> /dev/null 2>&1',
            $paths['base_path'],
            $paths['php_binary']
        );
    }

    /**
     * Check if warning banner should be shown.
     */
    public function shouldShowWarning(): bool
    {
        $status = $this->getStatus();

        // Only show warning if cron is not running
        if ($status['status'] === self::STATUS_RUNNING) {
            return false;
        }

        // Check if warning was dismissed within 24 hours
        $dismissedAt = Setting::get('cron_warning_dismissed_at');

        if ($dismissedAt === null) {
            return true;
        }

        $dismissedCarbon = Carbon::parse($dismissedAt);
        $hoursAgo = $dismissedCarbon->diffInHours(Carbon::now());

        return $hoursAgo >= self::WARNING_DISMISS_HOURS;
    }

    /**
     * Dismiss warning for 24 hours.
     */
    public function dismissWarning(): void
    {
        Setting::set('cron_warning_dismissed_at', Carbon::now()->toIso8601String());
    }

    /**
     * Update health check last run timestamp.
     */
    public function updateHealthCheckLastRun(): void
    {
        Setting::set('server_health_check_last_run', Carbon::now()->toIso8601String());
    }
}
