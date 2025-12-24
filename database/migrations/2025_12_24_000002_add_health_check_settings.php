<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Setting;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $settings = [
            // Health Check Settings
            [
                'key' => 'server_health_check_enabled',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'health_check',
                'label' => 'Enable Health Check',
                'description' => 'Enable automatic server health checks',
            ],
            [
                'key' => 'server_health_check_interval',
                'value' => '5',
                'type' => 'integer',
                'group' => 'health_check',
                'label' => 'Check Interval (minutes)',
                'description' => 'How often to check server connections (1, 5, 10, 30, 60)',
            ],
            [
                'key' => 'notify_server_connection_failed',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'health_check',
                'label' => 'Notify on Connection Failure',
                'description' => 'Send email notification when server connection fails',
            ],
            [
                'key' => 'server_health_check_last_run',
                'value' => null,
                'type' => 'string',
                'group' => 'health_check',
                'label' => 'Last Health Check Run',
                'description' => 'Timestamp of last health check execution',
            ],

            // Cron Settings
            [
                'key' => 'cron_last_run',
                'value' => null,
                'type' => 'string',
                'group' => 'cron',
                'label' => 'Last Cron Run',
                'description' => 'Timestamp of last cron heartbeat',
            ],
            [
                'key' => 'cron_environment',
                'value' => 'vps',
                'type' => 'string',
                'group' => 'cron',
                'label' => 'Cron Environment',
                'description' => 'Server environment type (vps or hosting)',
            ],
            [
                'key' => 'cron_warning_dismissed_at',
                'value' => null,
                'type' => 'string',
                'group' => 'cron',
                'label' => 'Warning Dismissed At',
                'description' => 'Timestamp when cron warning was dismissed',
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Setting::whereIn('key', [
            'server_health_check_enabled',
            'server_health_check_interval',
            'notify_server_connection_failed',
            'server_health_check_last_run',
            'cron_last_run',
            'cron_environment',
            'cron_warning_dismissed_at',
        ])->delete();
    }
};
