<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // General Settings
            [
                'key' => 'app_name',
                'value' => 'NAT VPS Manager',
                'type' => 'string',
                'group' => 'general',
                'label' => 'Application Name',
                'description' => 'The name of your application',
            ],
            [
                'key' => 'app_logo',
                'value' => null,
                'type' => 'file',
                'group' => 'general',
                'label' => 'Application Logo',
                'description' => 'Upload your application logo',
            ],
            [
                'key' => 'app_favicon',
                'value' => null,
                'type' => 'file',
                'group' => 'general',
                'label' => 'Favicon',
                'description' => 'Upload your favicon (ICO or PNG)',
            ],
            [
                'key' => 'app_timezone',
                'value' => 'Asia/Jakarta',
                'type' => 'string',
                'group' => 'general',
                'label' => 'Timezone',
                'description' => 'Application timezone',
            ],
            [
                'key' => 'app_locale',
                'value' => 'en',
                'type' => 'string',
                'group' => 'general',
                'label' => 'Language',
                'description' => 'Application language (en, id)',
            ],

            // Mail Settings
            [
                'key' => 'mail_enabled',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'mail',
                'label' => 'Enable Email',
                'description' => 'Enable or disable email notifications',
            ],
            [
                'key' => 'mail_host',
                'value' => 'smtp.gmail.com',
                'type' => 'string',
                'group' => 'mail',
                'label' => 'SMTP Host',
                'description' => 'SMTP server hostname',
            ],
            [
                'key' => 'mail_port',
                'value' => '587',
                'type' => 'integer',
                'group' => 'mail',
                'label' => 'SMTP Port',
                'description' => 'SMTP server port (usually 587 for TLS, 465 for SSL)',
            ],
            [
                'key' => 'mail_username',
                'value' => null,
                'type' => 'string',
                'group' => 'mail',
                'label' => 'SMTP Username',
                'description' => 'SMTP authentication username',
            ],
            [
                'key' => 'mail_password',
                'value' => null,
                'type' => 'encrypted',
                'group' => 'mail',
                'label' => 'SMTP Password',
                'description' => 'SMTP authentication password',
            ],
            [
                'key' => 'mail_encryption',
                'value' => 'tls',
                'type' => 'string',
                'group' => 'mail',
                'label' => 'Encryption',
                'description' => 'SMTP encryption (tls or ssl)',
            ],
            [
                'key' => 'mail_from_address',
                'value' => 'noreply@example.com',
                'type' => 'string',
                'group' => 'mail',
                'label' => 'From Address',
                'description' => 'Default sender email address',
            ],
            [
                'key' => 'mail_from_name',
                'value' => 'NAT VPS Manager',
                'type' => 'string',
                'group' => 'mail',
                'label' => 'From Name',
                'description' => 'Default sender name',
            ],

            // Notification Settings
            [
                'key' => 'notify_vps_assigned',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'notification',
                'label' => 'VPS Assigned',
                'description' => 'Send email when VPS is assigned to user',
            ],
            [
                'key' => 'notify_vps_unassigned',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'notification',
                'label' => 'VPS Unassigned',
                'description' => 'Send email when VPS is unassigned from user',
            ],
            [
                'key' => 'notify_welcome',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'notification',
                'label' => 'Welcome Email',
                'description' => 'Send welcome email to new users',
            ],
            [
                'key' => 'notify_vps_power_action',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'notification',
                'label' => 'VPS Power Actions',
                'description' => 'Send email when VPS is started/stopped/restarted',
            ],
            [
                'key' => 'notify_resource_warning',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'notification',
                'label' => 'Resource Warning',
                'description' => 'Send email when VPS resource usage is high',
            ],
            [
                'key' => 'resource_warning_cpu_threshold',
                'value' => '90',
                'type' => 'integer',
                'group' => 'notification',
                'label' => 'CPU Warning Threshold (%)',
                'description' => 'Send warning when CPU usage exceeds this percentage',
            ],
            [
                'key' => 'resource_warning_ram_threshold',
                'value' => '90',
                'type' => 'integer',
                'group' => 'notification',
                'label' => 'RAM Warning Threshold (%)',
                'description' => 'Send warning when RAM usage exceeds this percentage',
            ],
            [
                'key' => 'resource_warning_disk_threshold',
                'value' => '90',
                'type' => 'integer',
                'group' => 'notification',
                'label' => 'Disk Warning Threshold (%)',
                'description' => 'Send warning when disk usage exceeds this percentage',
            ],
            [
                'key' => 'resource_monitor_enabled',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'notification',
                'label' => 'Enable Resource Monitoring',
                'description' => 'Enable automatic resource monitoring (requires scheduler)',
            ],
            [
                'key' => 'resource_monitor_interval',
                'value' => '15',
                'type' => 'integer',
                'group' => 'notification',
                'label' => 'Monitor Interval (minutes)',
                'description' => 'How often to check resource usage (5-60 minutes)',
            ],
            [
                'key' => 'resource_warning_cooldown',
                'value' => '60',
                'type' => 'integer',
                'group' => 'notification',
                'label' => 'Warning Cooldown (minutes)',
                'description' => 'Minimum time between warning emails for same VPS',
            ],

            // Audit Log Settings
            [
                'key' => 'audit_log_retention_days',
                'value' => '90',
                'type' => 'integer',
                'group' => 'audit',
                'label' => 'Audit Log Retention (days)',
                'description' => 'Number of days to retain audit logs (0 = keep forever)',
            ],

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
}
