<?php

namespace App\Services\Console;

use Illuminate\Support\Facades\App;

/**
 * WebSocket URL Builder Service
 * 
 * Builds secure WebSocket URLs for VNC and SSH console connections.
 * Uses unified console proxy (single port for both VNC and SSH).
 * 
 * Requirements: 6.1 - Use WSS (WebSocket Secure) protocol
 */
class WebSocketUrlBuilder
{
    /**
     * Build a WebSocket URL for VNC connection.
     */
    public function buildVncUrl(string $targetHost, int $targetPort, ?string $token = null): string
    {
        return $this->buildUrl('vnc', $targetHost, $targetPort, $token);
    }

    /**
     * Build a WebSocket URL for SSH connection.
     */
    public function buildSshUrl(string $targetHost, int $targetPort, ?string $token = null): string
    {
        return $this->buildUrl('ssh', $targetHost, $targetPort, $token);
    }

    /**
     * Build a WebSocket URL based on type and configuration.
     * 
     * Both VNC and SSH use the same console proxy server.
     */
    public function buildUrl(string $type, string $targetHost, int $targetPort, ?string $token = null): string
    {
        $protocol = $this->getProtocol();
        $host = $this->getProxyHost();
        $path = $this->buildPath($type, $targetHost, $targetPort, $token);

        // Check if public_host already includes port (e.g., "server.com:6080")
        $publicHost = config('websockify.proxy.public_host', '');
        if (!empty($publicHost)) {
            // Public host is set - use it directly (may include port)
            return "{$protocol}://{$host}{$path}";
        }

        // No public host - use internal host with port
        $port = $this->getProxyPort();
        return "{$protocol}://{$host}:{$port}{$path}";
    }

    /**
     * Get the WebSocket protocol (ws or wss).
     */
    public function getProtocol(): string
    {
        // Always use WSS in production
        if (App::environment('production')) {
            return 'wss';
        }

        // Check config for other environments
        $ssl = config('websockify.proxy.ssl', true);
        return $ssl ? 'wss' : 'ws';
    }

    /**
     * Check if using secure WebSocket.
     */
    public function isSecure(): bool
    {
        return $this->getProtocol() === 'wss';
    }

    /**
     * Get the proxy host for WebSocket connections.
     */
    protected function getProxyHost(): string
    {
        // Use public host if configured (domain)
        $publicHost = config('websockify.proxy.public_host', '');
        if (!empty($publicHost)) {
            return $publicHost;
        }

        // Fall back to APP_URL host
        return $this->getAppHost();
    }

    /**
     * Get the console proxy port.
     */
    protected function getProxyPort(): int
    {
        return (int) config('websockify.proxy.port', 6080);
    }

    /**
     * Get the app server host from APP_URL.
     */
    protected function getAppHost(): string
    {
        $appUrl = config('app.url', '');
        if (!empty($appUrl)) {
            $parsed = parse_url($appUrl);
            if (isset($parsed['host'])) {
                return $parsed['host'];
            }
        }
        return 'localhost';
    }

    /**
     * Build the WebSocket path.
     */
    protected function buildPath(string $type, string $targetHost, int $targetPort, ?string $token = null): string
    {
        if ($type === 'vnc') {
            // VNC: /websockify/HOST/PORT
            $path = "/websockify/{$targetHost}/{$targetPort}";
        } else {
            // SSH: /ssh?host=HOST&port=PORT
            $path = '/ssh?' . http_build_query([
                'host' => $targetHost,
                'port' => $targetPort,
            ]);
        }

        // Add token if provided
        if ($token !== null) {
            $separator = str_contains($path, '?') ? '&' : '?';
            $path .= "{$separator}token={$token}";
        }

        return $path;
    }

    /**
     * Validate that the WebSocket URL is properly secured for production.
     */
    public function validateSecureUrl(string $url): bool
    {
        if (App::environment('production')) {
            if (!str_starts_with($url, 'wss://')) {
                throw new \InvalidArgumentException(
                    'WebSocket URL must use WSS protocol in production environment'
                );
            }
        }
        return true;
    }
}
