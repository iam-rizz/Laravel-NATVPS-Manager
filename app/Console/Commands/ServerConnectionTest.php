<?php

namespace App\Console\Commands;

use App\Enums\UserRole;
use App\Models\Server;
use App\Models\User;
use App\Services\CronService;
use App\Services\MailService;
use App\Services\SettingService;
use App\Services\Virtualizor\VirtualizorService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ServerConnectionTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'server:connection-test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test connection to all active servers';

    /**
     * Execute the console command.
     */
    public function handle(
        VirtualizorService $virtualizorService,
        MailService $mailService,
        SettingService $settingService,
        CronService $cronService
    ): int {
        // Check if health check should run
        if (!$cronService->shouldRunHealthCheck()) {
            $this->info('Health check skipped (disabled or interval not reached).');
            return self::SUCCESS;
        }

        $this->info('Starting server connection test...');

        // Update health check last run timestamp
        $cronService->updateHealthCheckLastRun();

        // Get all active servers
        $servers = Server::where('is_active', true)->get();

        if ($servers->isEmpty()) {
            $this->info('No active servers found.');
            return self::SUCCESS;
        }

        $this->info("Testing {$servers->count()} active server(s)...");

        $success = 0;
        $failed = 0;

        foreach ($servers as $server) {
            $this->line("Testing: {$server->name} ({$server->ip_address})");

            try {
                $result = $virtualizorService->testConnection($server);

                if ($result->success) {
                    // Connection successful
                    $server->update([
                        'last_checked' => now(),
                        'last_check_status' => 'success',
                        'last_check_error' => null,
                    ]);

                    $this->info("  → Connection successful");
                    $success++;
                } else {
                    // Connection failed
                    $errorMessage = $result->message ?? 'Unknown error';
                    
                    $server->update([
                        'last_checked' => now(),
                        'last_check_status' => 'failed',
                        'last_check_error' => $errorMessage,
                    ]);

                    $this->error("  → Connection failed: {$errorMessage}");
                    $failed++;

                    // Send email notification if enabled
                    $this->sendFailureNotification($server, $errorMessage, $mailService, $settingService);
                }
            } catch (\Exception $e) {
                $errorMessage = $e->getMessage();
                
                $server->update([
                    'last_checked' => now(),
                    'last_check_status' => 'failed',
                    'last_check_error' => $errorMessage,
                ]);

                $this->error("  → Error: {$errorMessage}");
                Log::error('Server connection test error', [
                    'server_id' => $server->id,
                    'error' => $errorMessage,
                ]);
                $failed++;

                // Send email notification if enabled
                $this->sendFailureNotification($server, $errorMessage, $mailService, $settingService);
            }
        }

        $this->newLine();
        $this->info("Connection test complete!");
        $this->info("Success: {$success}, Failed: {$failed}");

        return self::SUCCESS;
    }

    /**
     * Send failure notification to all admin users.
     */
    protected function sendFailureNotification(
        Server $server,
        string $errorMessage,
        MailService $mailService,
        SettingService $settingService
    ): void {
        $sent = $mailService->sendServerConnectionFailed($server, $errorMessage);
        
        if ($sent) {
            $this->info("  → Notification sent to admin users");
        } else {
            $this->line("  → Email notification skipped (disabled or failed)");
        }
    }
}
