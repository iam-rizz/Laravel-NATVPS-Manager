<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'slug' => 'vps_assigned',
                'name' => 'VPS Assigned',
                'subject' => 'Your VPS is Ready - {{vps_hostname}}',
                'body' => $this->getVpsAssignedTemplate(),
                'variables' => ['user_name', 'vps_hostname', 'vps_ip', 'vps_os', 'vps_ram', 'vps_cpu', 'vps_disk', 'ssh_port', 'nat_ports'],
                'is_active' => true,
            ],
            [
                'slug' => 'vps_unassigned',
                'name' => 'VPS Unassigned',
                'subject' => 'VPS Access Removed - {{vps_hostname}}',
                'body' => $this->getVpsUnassignedTemplate(),
                'variables' => ['user_name', 'vps_hostname'],
                'is_active' => true,
            ],
            [
                'slug' => 'welcome',
                'name' => 'Welcome Email',
                'subject' => 'Welcome to {{app_name}}',
                'body' => $this->getWelcomeTemplate(),
                'variables' => ['user_name', 'user_email', 'login_url'],
                'is_active' => true,
            ],
            [
                'slug' => 'vps_power_action',
                'name' => 'VPS Power Action',
                'subject' => 'VPS {{action}} - {{vps_hostname}}',
                'body' => $this->getVpsPowerActionTemplate(),
                'variables' => ['user_name', 'vps_hostname', 'action', 'action_by', 'action_time'],
                'is_active' => true,
            ],
            [
                'slug' => 'resource_warning',
                'name' => 'Resource Warning',
                'subject' => '⚠️ High Resource Usage Alert - {{vps_hostname}}',
                'body' => $this->getResourceWarningTemplate(),
                'variables' => ['user_name', 'vps_hostname', 'cpu_usage', 'ram_usage', 'disk_usage', 'warning_type', 'threshold'],
                'is_active' => true,
            ],
            [
                'slug' => 'server_connection_failed',
                'name' => 'Server Connection Failed',
                'subject' => '🚨 [Alert] Server Connection Failed: {{server_name}}',
                'body' => $this->getServerConnectionFailedTemplate(),
                'variables' => ['server_name', 'server_ip', 'error_message', 'check_time'],
                'is_active' => true,
            ],
        ];

        foreach ($templates as $template) {
            EmailTemplate::updateOrCreate(
                ['slug' => $template['slug']],
                $template
            );
        }
    }

    private function getVpsAssignedTemplate(): string
    {
        return <<<'HTML'
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #4F46E5; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #f9fafb; padding: 20px; border: 1px solid #e5e7eb; }
        .info-box { background: white; padding: 15px; border-radius: 8px; margin: 15px 0; border: 1px solid #e5e7eb; }
        .info-row { display: flex; padding: 8px 0; border-bottom: 1px solid #f3f4f6; }
        .info-label { font-weight: bold; width: 120px; color: #6b7280; }
        .info-value { color: #111827; }
        .footer { text-align: center; padding: 20px; color: #6b7280; font-size: 12px; }
        .btn { display: inline-block; background: #4F46E5; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; margin-top: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎉 Your VPS is Ready!</h1>
        </div>
        <div class="content">
            <p>Hello <strong>{{user_name}}</strong>,</p>
            <p>Great news! A VPS has been assigned to your account. Here are the details:</p>
            
            <div class="info-box">
                <h3 style="margin-top: 0;">VPS Information</h3>
                <div class="info-row">
                    <span class="info-label">Hostname:</span>
                    <span class="info-value">{{vps_hostname}}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">IP Address:</span>
                    <span class="info-value">{{vps_ip}}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">OS:</span>
                    <span class="info-value">{{vps_os}}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">RAM:</span>
                    <span class="info-value">{{vps_ram}}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">CPU:</span>
                    <span class="info-value">{{vps_cpu}} Core(s)</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Disk:</span>
                    <span class="info-value">{{vps_disk}}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">SSH Port:</span>
                    <span class="info-value">{{ssh_port}}</span>
                </div>
                <div class="info-row" style="border-bottom: none;">
                    <span class="info-label">NAT Ports:</span>
                    <span class="info-value">{{nat_ports}}</span>
                </div>
            </div>

            <p>You can manage your VPS from the dashboard.</p>
            <p>If you have any questions, please contact our support team.</p>
        </div>
        <div class="footer">
            <p>© {{app_name}}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
HTML;
    }

    private function getVpsUnassignedTemplate(): string
    {
        return <<<'HTML'
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #EF4444; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #f9fafb; padding: 20px; border: 1px solid #e5e7eb; }
        .footer { text-align: center; padding: 20px; color: #6b7280; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>VPS Access Removed</h1>
        </div>
        <div class="content">
            <p>Hello <strong>{{user_name}}</strong>,</p>
            <p>This is to inform you that your access to the following VPS has been removed:</p>
            
            <p style="background: white; padding: 15px; border-radius: 8px; border: 1px solid #e5e7eb;">
                <strong>Hostname:</strong> {{vps_hostname}}
            </p>

            <p>If you believe this was done in error, please contact our support team.</p>
        </div>
        <div class="footer">
            <p>© {{app_name}}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
HTML;
    }

    private function getWelcomeTemplate(): string
    {
        return <<<'HTML'
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #4F46E5; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #f9fafb; padding: 20px; border: 1px solid #e5e7eb; }
        .footer { text-align: center; padding: 20px; color: #6b7280; font-size: 12px; }
        .btn { display: inline-block; background: #4F46E5; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; margin-top: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Welcome to {{app_name}}!</h1>
        </div>
        <div class="content">
            <p>Hello <strong>{{user_name}}</strong>,</p>
            <p>Welcome to {{app_name}}! Your account has been created successfully.</p>
            
            <p>You can now log in to your dashboard to manage your VPS instances.</p>
            
            <p style="text-align: center;">
                <a href="{{login_url}}" class="btn">Go to Dashboard</a>
            </p>

            <p>If you have any questions, feel free to contact our support team.</p>
        </div>
        <div class="footer">
            <p>© {{app_name}}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
HTML;
    }

    private function getVpsPowerActionTemplate(): string
    {
        return <<<'HTML'
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #3B82F6; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #f9fafb; padding: 20px; border: 1px solid #e5e7eb; }
        .info-box { background: white; padding: 15px; border-radius: 8px; margin: 15px 0; border: 1px solid #e5e7eb; }
        .footer { text-align: center; padding: 20px; color: #6b7280; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔄 VPS Power Action</h1>
        </div>
        <div class="content">
            <p>Hello <strong>{{user_name}}</strong>,</p>
            <p>A power action has been performed on your VPS:</p>
            
            <div class="info-box">
                <p><strong>VPS:</strong> {{vps_hostname}}</p>
                <p><strong>Action:</strong> {{action}}</p>
                <p><strong>Performed by:</strong> {{action_by}}</p>
                <p><strong>Time:</strong> {{action_time}}</p>
            </div>

            <p>If you did not initiate this action, please contact support immediately.</p>
        </div>
        <div class="footer">
            <p>© {{app_name}}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
HTML;
    }

    private function getResourceWarningTemplate(): string
    {
        return <<<'HTML'
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #F59E0B; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #f9fafb; padding: 20px; border: 1px solid #e5e7eb; }
        .warning-box { background: #FEF3C7; padding: 15px; border-radius: 8px; margin: 15px 0; border: 1px solid #F59E0B; }
        .resource-bar { background: #e5e7eb; border-radius: 4px; height: 20px; margin: 5px 0; overflow: hidden; }
        .resource-fill { height: 100%; border-radius: 4px; }
        .resource-fill.warning { background: #F59E0B; }
        .resource-fill.danger { background: #EF4444; }
        .resource-fill.normal { background: #10B981; }
        .footer { text-align: center; padding: 20px; color: #6b7280; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>⚠️ Resource Warning</h1>
        </div>
        <div class="content">
            <p>Hello <strong>{{user_name}}</strong>,</p>
            <p>Your VPS <strong>{{vps_hostname}}</strong> has exceeded the resource threshold:</p>
            
            <div class="warning-box">
                <p><strong>Warning Type:</strong> {{warning_type}}</p>
                <p><strong>Threshold:</strong> {{threshold}}%</p>
            </div>

            <h3>Current Resource Usage:</h3>
            
            <p><strong>CPU:</strong> {{cpu_usage}}%</p>
            <div class="resource-bar">
                <div class="resource-fill" style="width: {{cpu_usage}}%; background: {{cpu_usage}} > 90 ? '#EF4444' : '{{cpu_usage}} > 70 ? '#F59E0B' : '#10B981'';"></div>
            </div>
            
            <p><strong>RAM:</strong> {{ram_usage}}%</p>
            <div class="resource-bar">
                <div class="resource-fill" style="width: {{ram_usage}}%;"></div>
            </div>
            
            <p><strong>Disk:</strong> {{disk_usage}}%</p>
            <div class="resource-bar">
                <div class="resource-fill" style="width: {{disk_usage}}%;"></div>
            </div>

            <p style="margin-top: 20px;">Please consider optimizing your applications or upgrading your VPS resources.</p>
        </div>
        <div class="footer">
            <p>© {{app_name}}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
HTML;
    }

    private function getServerConnectionFailedTemplate(): string
    {
        return <<<'HTML'
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #EF4444; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #f9fafb; padding: 20px; border: 1px solid #e5e7eb; }
        .alert-box { background: #FEE2E2; padding: 15px; border-radius: 8px; margin: 15px 0; border: 1px solid #EF4444; }
        .info-row { padding: 8px 0; border-bottom: 1px solid #f3f4f6; }
        .info-row:last-child { border-bottom: none; }
        .info-label { font-weight: bold; color: #6b7280; }
        .info-value { color: #111827; }
        .footer { text-align: center; padding: 20px; color: #6b7280; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🚨 Server Connection Failed</h1>
        </div>
        <div class="content">
            <p>A server connection test has failed. Please investigate immediately.</p>
            
            <div class="alert-box">
                <div class="info-row">
                    <span class="info-label">Server Name:</span>
                    <span class="info-value">{{server_name}}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">IP Address:</span>
                    <span class="info-value">{{server_ip}}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Error Message:</span>
                    <span class="info-value">{{error_message}}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Check Time:</span>
                    <span class="info-value">{{check_time}}</span>
                </div>
            </div>

            <p>Please check the server status and connectivity. If the issue persists, contact your hosting provider.</p>
        </div>
        <div class="footer">
            <p>© {{app_name}}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
HTML;
    }
}
