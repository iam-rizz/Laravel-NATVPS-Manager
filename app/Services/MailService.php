<?php

namespace App\Services;

use App\Models\EmailTemplate;
use App\Models\NatVps;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MailService
{
    protected SettingService $settingService;

    public function __construct(SettingService $settingService)
    {
        $this->settingService = $settingService;
    }

    /**
     * Send email using template.
     */
    public function sendTemplate(string $templateSlug, string $toEmail, string $toName, array $data = []): bool
    {
        if (!$this->settingService->isMailEnabled()) {
            Log::info("Mail disabled, skipping email: {$templateSlug} to {$toEmail}");
            return false;
        }

        $template = EmailTemplate::getBySlug($templateSlug);
        if (!$template) {
            Log::warning("Email template not found: {$templateSlug}");
            return false;
        }

        try {
            $rendered = $template->render($data);
            
            $this->configureMailer();

            Mail::html($rendered['body'], function ($message) use ($toEmail, $toName, $rendered) {
                $config = $this->settingService->getMailConfig();
                
                $message->to($toEmail, $toName)
                    ->from($config['from']['address'], $config['from']['name'])
                    ->subject($rendered['subject']);
            });

            Log::info("Email sent: {$templateSlug} to {$toEmail}");
            return true;

        } catch (\Exception $e) {
            Log::error("Failed to send email: {$templateSlug} to {$toEmail}", [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Send VPS assigned notification.
     */
    public function sendVpsAssigned(User $user, NatVps $vps): bool
    {
        if (!$this->settingService->isNotificationEnabled('vps_assigned')) {
            return false;
        }

        $data = [
            'user_name' => $user->name,
            'vps_hostname' => $vps->hostname,
            'vps_ip' => $vps->server->ip_address ?? 'N/A',
            'vps_os' => $vps->os ?? 'N/A',
            'vps_ram' => $this->formatBytes($vps->ram ?? 0),
            'vps_cpu' => $vps->cpu ?? 'N/A',
            'vps_disk' => $this->formatBytes($vps->disk ?? 0),
            'ssh_port' => $vps->ssh_port ?? 'N/A',
            'nat_ports' => $vps->nat_start && $vps->nat_end 
                ? "{$vps->nat_start} - {$vps->nat_end}" 
                : 'N/A',
        ];

        return $this->sendTemplate('vps_assigned', $user->email, $user->name, $data);
    }

    /**
     * Send VPS unassigned notification.
     */
    public function sendVpsUnassigned(User $user, NatVps $vps): bool
    {
        if (!$this->settingService->isNotificationEnabled('vps_unassigned')) {
            return false;
        }

        $data = [
            'user_name' => $user->name,
            'vps_hostname' => $vps->hostname,
        ];

        return $this->sendTemplate('vps_unassigned', $user->email, $user->name, $data);
    }

    /**
     * Send welcome email.
     */
    public function sendWelcome(User $user): bool
    {
        if (!$this->settingService->isNotificationEnabled('welcome')) {
            return false;
        }

        $data = [
            'user_name' => $user->name,
            'user_email' => $user->email,
            'login_url' => route('login'),
        ];

        return $this->sendTemplate('welcome', $user->email, $user->name, $data);
    }

    /**
     * Send VPS power action notification.
     */
    public function sendVpsPowerAction(User $user, NatVps $vps, string $action, string $actionBy): bool
    {
        if (!$this->settingService->isNotificationEnabled('vps_power_action')) {
            return false;
        }

        $actionLabels = [
            'start' => 'Started',
            'stop' => 'Stopped',
            'restart' => 'Restarted',
            'poweroff' => 'Powered Off',
        ];

        $data = [
            'user_name' => $user->name,
            'vps_hostname' => $vps->hostname,
            'action' => $actionLabels[$action] ?? ucfirst($action),
            'action_by' => $actionBy,
            'action_time' => now()->format('Y-m-d H:i:s'),
        ];

        return $this->sendTemplate('vps_power_action', $user->email, $user->name, $data);
    }

    /**
     * Send resource warning notification.
     */
    public function sendResourceWarning(User $user, NatVps $vps, array $resourceUsage, string $warningType): bool
    {
        if (!$this->settingService->isNotificationEnabled('resource_warning')) {
            return false;
        }

        $thresholds = [
            'cpu' => Setting::get('resource_warning_cpu_threshold', 90),
            'ram' => Setting::get('resource_warning_ram_threshold', 90),
            'disk' => Setting::get('resource_warning_disk_threshold', 90),
        ];

        // Get the highest threshold from warning types
        $warningTypes = array_map('trim', explode(',', strtolower($warningType)));
        $maxThreshold = 0;
        foreach ($warningTypes as $type) {
            $type = trim($type);
            if (isset($thresholds[$type]) && $thresholds[$type] > $maxThreshold) {
                $maxThreshold = $thresholds[$type];
            }
        }

        $data = [
            'user_name' => $user->name,
            'vps_hostname' => $vps->hostname,
            'cpu_usage' => round($resourceUsage['cpu'] ?? 0, 1),
            'ram_usage' => round($resourceUsage['ram'] ?? 0, 1),
            'disk_usage' => round($resourceUsage['disk'] ?? 0, 1),
            'warning_type' => $warningType . ' Usage',
            'threshold' => $maxThreshold ?: 90,
        ];

        return $this->sendTemplate('resource_warning', $user->email, $user->name, $data);
    }

    /**
     * Send server connection failed notification to all admins.
     */
    public function sendServerConnectionFailed(\App\Models\Server $server, string $errorMessage): bool
    {
        if (!$this->settingService->isNotificationEnabled('server_connection_failed')) {
            return false;
        }

        $data = [
            'server_name' => $server->name,
            'server_ip' => $server->ip_address,
            'error_message' => $errorMessage,
            'check_time' => now()->format('Y-m-d H:i:s'),
        ];

        // Send to all admin users
        $adminUsers = \App\Models\User::where('role', \App\Enums\UserRole::Admin)->get();
        $sent = false;

        foreach ($adminUsers as $admin) {
            $result = $this->sendTemplate('server_connection_failed', $admin->email, $admin->name, $data);
            if ($result) {
                $sent = true;
            }
        }

        return $sent;
    }

    /**
     * Check resource usage and send warning if needed.
     */
    public function checkAndSendResourceWarning(NatVps $vps, array $resourceUsage): void
    {
        if (!$vps->user) {
            return;
        }

        $cpuThreshold = Setting::get('resource_warning_cpu_threshold', 90);
        $ramThreshold = Setting::get('resource_warning_ram_threshold', 90);
        $diskThreshold = Setting::get('resource_warning_disk_threshold', 90);

        $cpuUsage = $resourceUsage['cpu'] ?? 0;
        $ramUsage = $resourceUsage['ram'] ?? 0;
        $diskUsage = $resourceUsage['disk'] ?? 0;

        if ($cpuUsage >= $cpuThreshold) {
            $this->sendResourceWarning($vps->user, $vps, $resourceUsage, 'cpu');
        }

        if ($ramUsage >= $ramThreshold) {
            $this->sendResourceWarning($vps->user, $vps, $resourceUsage, 'ram');
        }

        if ($diskUsage >= $diskThreshold) {
            $this->sendResourceWarning($vps->user, $vps, $resourceUsage, 'disk');
        }
    }

    /**
     * Send test email.
     */
    public function sendTestEmail(string $toEmail): bool
    {
        try {
            $this->configureMailer();
            $config = $this->settingService->getMailConfig();
            $appName = $this->settingService->appName();

            Mail::html(
                "<h1>Test Email</h1><p>This is a test email from {$appName}.</p><p>If you received this, your SMTP settings are configured correctly!</p>",
                function ($message) use ($toEmail, $config, $appName) {
                    $message->to($toEmail)
                        ->from($config['from']['address'], $config['from']['name'])
                        ->subject("Test Email from {$appName}");
                }
            );

            return true;
        } catch (\Exception $e) {
            Log::error("Test email failed", ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Send password reset email.
     */
    public function sendPasswordReset(User $user, string $resetUrl): bool
    {
        try {
            $this->configureMailer();
            $config = $this->settingService->getMailConfig();
            $appName = $this->settingService->appName();

            $html = "
                <h1>Reset Your Password</h1>
                <p>Hello {$user->name},</p>
                <p>You are receiving this email because we received a password reset request for your account.</p>
                <p><a href=\"{$resetUrl}\" style=\"display: inline-block; padding: 12px 24px; background-color: #4F46E5; color: white; text-decoration: none; border-radius: 8px;\">Reset Password</a></p>
                <p>This password reset link will expire in 60 minutes.</p>
                <p>If you did not request a password reset, no further action is required.</p>
                <p>Regards,<br>{$appName}</p>
            ";

            Mail::html($html, function ($message) use ($user, $config, $appName) {
                $message->to($user->email, $user->name)
                    ->from($config['from']['address'], $config['from']['name'])
                    ->subject("Reset Password - {$appName}");
            });

            return true;
        } catch (\Exception $e) {
            Log::error("Password reset email failed", ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Configure mailer with database settings.
     */
    protected function configureMailer(): void
    {
        $config = $this->settingService->getMailConfig();

        config([
            'mail.default' => 'smtp',
            'mail.mailers.smtp.host' => $config['host'],
            'mail.mailers.smtp.port' => $config['port'],
            'mail.mailers.smtp.username' => $config['username'],
            'mail.mailers.smtp.password' => $config['password'],
            'mail.mailers.smtp.encryption' => $config['encryption'] === 'null' ? null : $config['encryption'],
            'mail.from.address' => $config['from']['address'],
            'mail.from.name' => $config['from']['name'],
        ]);

        // Purge the mailer to use new config
        Mail::purge('smtp');
    }

    /**
     * Format bytes to human readable.
     */
    protected function formatBytes(int $bytes): string
    {
        if ($bytes >= 1073741824) {
            return round($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return round($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return round($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' B';
    }
}
