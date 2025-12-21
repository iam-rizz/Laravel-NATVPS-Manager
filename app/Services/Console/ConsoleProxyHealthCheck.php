<?php

namespace App\Services\Console;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Console Proxy Health Check Service
 * 
 * Checks the health status of the console proxy server.
 * Supports various deployment scenarios:
 * - Same server (internal host)
 * - Shared hosting with subdomain
 * - External server
 */
class ConsoleProxyHealthCheck
{
    /**
     * Check console proxy health status.
     */
    public function check(bool $useCache = true): array
    {
        if (!config('websockify.proxy.enabled', true)) {
            return [
                'status' => 'disabled',
                'message' => __('app.console_proxy_disabled') ?? 'Console proxy is disabled',
                'online' => false,
            ];
        }

        $cacheKey = 'console_proxy_health';
        $cacheTtl = config('websockify.health.cache_ttl', 30);

        // Return cached result if available
        if ($useCache && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $result = $this->performHealthCheck();
        
        // Cache the result
        Cache::put($cacheKey, $result, $cacheTtl);

        return $result;
    }

    /**
     * Perform the actual health check.
     */
    protected function performHealthCheck(): array
    {
        $healthUrl = $this->getHealthCheckUrl();
        $timeout = config('websockify.health.timeout', 5);
        $publicHost = $this->getPublicHost();

        $startTime = microtime(true);

        try {
            $response = Http::timeout($timeout)->get($healthUrl);
            $responseTime = round((microtime(true) - $startTime) * 1000);

            if ($response->successful()) {
                $data = $response->json();
                
                return [
                    'status' => 'online',
                    'online' => true,
                    'message' => __('app.console_proxy_running') ?? 'Console proxy is running',
                    'response_time_ms' => $responseTime,
                    'services' => $data['services'] ?? ['vnc', 'ssh'],
                    'proxy_timestamp' => $data['timestamp'] ?? null,
                    'checked_at' => now()->toIso8601String(),
                    'host' => $publicHost,
                ];
            }

            return [
                'status' => 'error',
                'online' => false,
                'message' => __('app.console_proxy_error') ?? 'Console proxy returned error',
                'error' => 'HTTP ' . $response->status(),
                'response_time_ms' => $responseTime,
                'checked_at' => now()->toIso8601String(),
                'host' => $publicHost,
            ];

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            return [
                'status' => 'offline',
                'online' => false,
                'message' => __('app.console_proxy_offline') ?? 'Cannot connect to console proxy',
                'error' => 'Connection refused or timeout',
                'checked_at' => now()->toIso8601String(),
                'host' => $publicHost,
            ];
        } catch (\Exception $e) {
            Log::error('Console proxy health check failed', [
                'url' => $healthUrl,
                'error' => $e->getMessage(),
            ]);

            return [
                'status' => 'error',
                'online' => false,
                'message' => __('app.console_proxy_check_failed') ?? 'Health check failed',
                'error' => $e->getMessage(),
                'checked_at' => now()->toIso8601String(),
                'host' => $publicHost,
            ];
        }
    }

    /**
     * Get the health check URL.
     * 
     * Priority:
     * 1. CONSOLE_PROXY_HEALTH_URL (explicit health check URL)
     * 2. CONSOLE_PROXY_PUBLIC_HOST (external/subdomain deployment)
     * 3. CONSOLE_PROXY_HOST:PORT (internal deployment)
     */
    protected function getHealthCheckUrl(): string
    {
        // 1. Explicit health check URL
        $healthUrl = config('websockify.health.url');
        if (!empty($healthUrl)) {
            return $healthUrl;
        }

        // 2. Public host (subdomain/external server)
        $publicHost = config('websockify.proxy.public_host', '');
        if (!empty($publicHost)) {
            // Determine protocol
            $ssl = config('websockify.proxy.ssl', true);
            $protocol = $ssl ? 'https' : 'http';
            
            // Check if public_host includes port
            if (str_contains($publicHost, ':')) {
                return "{$protocol}://{$publicHost}/health";
            }
            
            return "{$protocol}://{$publicHost}/health";
        }

        // 3. Internal host (same server)
        $host = config('websockify.proxy.host', '127.0.0.1');
        $port = config('websockify.proxy.port', 6080);
        
        return "http://{$host}:{$port}/health";
    }

    /**
     * Get the public host for display.
     */
    protected function getPublicHost(): string
    {
        $publicHost = config('websockify.proxy.public_host', '');
        if (!empty($publicHost)) {
            return $publicHost;
        }

        $host = config('websockify.proxy.host', '127.0.0.1');
        $port = config('websockify.proxy.port', 6080);
        
        return "{$host}:{$port}";
    }

    /**
     * Check if console proxy is online.
     */
    public function isOnline(): bool
    {
        return $this->check()['online'] ?? false;
    }

    /**
     * Clear cached health status.
     */
    public function clearCache(): void
    {
        Cache::forget('console_proxy_health');
    }
}
