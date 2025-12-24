<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\CronService;
use App\Services\MailService;
use App\Services\SettingService;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function __construct(
        protected SettingService $settingService,
        protected MailService $mailService,
        protected CronService $cronService
    ) {}

    /**
     * Show general settings.
     */
    public function general()
    {
        $settings = Setting::where('group', 'general')->get()->keyBy('key');
        
        return view('admin.settings.general', compact('settings'));
    }

    /**
     * Update general settings.
     */
    public function updateGeneral(Request $request)
    {
        $request->validate([
            'app_name' => 'required|string|max:255',
            'app_timezone' => 'required|string|max:100',
            'app_logo' => 'nullable|image|mimes:png,jpg,jpeg,svg|max:2048',
            'app_favicon' => 'nullable|image|mimes:png,ico|max:1024',
        ]);

        Setting::set('app_name', $request->app_name);
        Setting::set('app_timezone', $request->app_timezone);

        if ($request->hasFile('app_logo')) {
            $this->settingService->uploadFile('app_logo', $request->file('app_logo'));
        }

        if ($request->hasFile('app_favicon')) {
            $this->settingService->uploadFile('app_favicon', $request->file('app_favicon'));
        }

        return redirect()->route('settings.general')
            ->with('success', 'General settings updated successfully.');
    }

    /**
     * Show mail settings.
     */
    public function mail()
    {
        $settings = Setting::where('group', 'mail')->get()->keyBy('key');
        
        return view('admin.settings.mail', compact('settings'));
    }

    /**
     * Update mail settings.
     */
    public function updateMail(Request $request)
    {
        $request->validate([
            'mail_enabled' => 'nullable|boolean',
            'mail_host' => 'required|string|max:255',
            'mail_port' => 'required|integer|min:1|max:65535',
            'mail_username' => 'nullable|string|max:255',
            'mail_password' => 'nullable|string|max:255',
            'mail_encryption' => 'required|in:tls,ssl,null',
            'mail_from_address' => 'required|email|max:255',
            'mail_from_name' => 'required|string|max:255',
        ]);

        Setting::set('mail_enabled', $request->boolean('mail_enabled') ? '1' : '0');
        Setting::set('mail_host', $request->mail_host);
        Setting::set('mail_port', $request->mail_port);
        Setting::set('mail_username', $request->mail_username);
        
        // Only update password if provided
        if ($request->filled('mail_password')) {
            Setting::set('mail_password', $request->mail_password);
        }
        
        Setting::set('mail_encryption', $request->mail_encryption);
        Setting::set('mail_from_address', $request->mail_from_address);
        Setting::set('mail_from_name', $request->mail_from_name);

        return redirect()->route('settings.mail')
            ->with('success', 'Mail settings updated successfully.');
    }

    /**
     * Send test email.
     */
    public function testMail(Request $request)
    {
        $request->validate([
            'test_email' => 'required|email',
        ]);

        try {
            $this->mailService->sendTestEmail($request->test_email);
            
            return redirect()->route('settings.mail')
                ->with('success', 'Test email sent successfully to ' . $request->test_email);
        } catch (\Exception $e) {
            return redirect()->route('settings.mail')
                ->with('error', 'Failed to send test email: ' . $e->getMessage());
        }
    }

    /**
     * Show notification settings.
     */
    public function notifications()
    {
        $settings = Setting::where('group', 'notification')->get()->keyBy('key');
        
        return view('admin.settings.notifications', compact('settings'));
    }

    /**
     * Update notification settings.
     */
    public function updateNotifications(Request $request)
    {
        $request->validate([
            'resource_warning_cpu_threshold' => 'nullable|integer|min:1|max:100',
            'resource_warning_ram_threshold' => 'nullable|integer|min:1|max:100',
            'resource_warning_disk_threshold' => 'nullable|integer|min:1|max:100',
            'resource_monitor_interval' => 'nullable|integer|in:5,10,15,30,60',
            'resource_warning_cooldown' => 'nullable|integer|in:15,30,45,60,90,120,180,240,360,720,1440',
        ]);

        // VPS notifications
        Setting::set('notify_vps_assigned', $request->boolean('notify_vps_assigned') ? '1' : '0');
        Setting::set('notify_vps_unassigned', $request->boolean('notify_vps_unassigned') ? '1' : '0');
        Setting::set('notify_vps_power_action', $request->boolean('notify_vps_power_action') ? '1' : '0');
        
        // Resource monitoring
        Setting::set('resource_monitor_enabled', $request->boolean('resource_monitor_enabled') ? '1' : '0');
        Setting::set('notify_resource_warning', $request->boolean('notify_resource_warning') ? '1' : '0');
        Setting::set('resource_monitor_interval', $request->resource_monitor_interval ?? '15');
        Setting::set('resource_warning_cooldown', $request->resource_warning_cooldown ?? '60');
        Setting::set('resource_warning_cpu_threshold', $request->resource_warning_cpu_threshold ?? '90');
        Setting::set('resource_warning_ram_threshold', $request->resource_warning_ram_threshold ?? '90');
        Setting::set('resource_warning_disk_threshold', $request->resource_warning_disk_threshold ?? '90');
        
        // User notifications
        Setting::set('notify_welcome', $request->boolean('notify_welcome') ? '1' : '0');

        return redirect()->route('settings.notifications')
            ->with('success', 'Notification settings updated successfully.');
    }

    /**
     * Show audit log settings.
     */
    public function audit()
    {
        $settings = Setting::where('group', 'audit')->get()->keyBy('key');
        
        return view('admin.settings.audit', compact('settings'));
    }

    /**
     * Update audit log settings.
     */
    public function updateAudit(Request $request)
    {
        $request->validate([
            'audit_log_retention_days' => 'required|integer|min:0|max:3650',
        ]);

        Setting::set('audit_log_retention_days', $request->audit_log_retention_days);

        return redirect()->route('settings.audit')
            ->with('success', 'Audit log settings updated successfully.');
    }

    /**
     * Show health check settings.
     */
    public function healthCheck()
    {
        $settings = Setting::whereIn('group', ['health_check', 'cron'])->get()->keyBy('key');
        $cronStatus = $this->cronService->getStatus();
        $cronCommand = $this->cronService->getCronCommand();
        $paths = $this->cronService->getPaths();

        return view('admin.settings.health-check', compact('settings', 'cronStatus', 'cronCommand', 'paths'));
    }

    /**
     * Update health check settings.
     */
    public function updateHealthCheck(Request $request)
    {
        $request->validate([
            'server_health_check_enabled' => 'nullable|boolean',
            'server_health_check_interval' => 'required|integer|in:1,5,10,30,60',
            'notify_server_connection_failed' => 'nullable|boolean',
            'cron_environment' => 'required|string|in:vps,hosting',
        ]);

        Setting::set('server_health_check_enabled', $request->boolean('server_health_check_enabled') ? '1' : '0');
        Setting::set('server_health_check_interval', $request->server_health_check_interval);
        Setting::set('notify_server_connection_failed', $request->boolean('notify_server_connection_failed') ? '1' : '0');
        Setting::set('cron_environment', $request->cron_environment);

        return redirect()->route('settings.health-check')
            ->with('success', 'Health check settings updated successfully.');
    }

    /**
     * Dismiss cron warning for 24 hours.
     */
    public function dismissCronWarning()
    {
        $this->cronService->dismissWarning();

        return redirect()->back()
            ->with('success', 'Cron warning dismissed for 24 hours.');
    }
}
