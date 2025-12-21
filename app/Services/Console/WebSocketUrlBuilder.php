<?php

namespace App\Services\Console;

use Illuminate\Support\Facades\App;

/**
 * WebSocket URL Builder Service
 * 
 * Builds secure WebSocket URLs for VNC and SSH console connections.
 * Ensures WSS protocol is used in production environments.
 * 
 * Requirements: 6.1 - Use WSS (WebSocket Secure) protocol
 */
class WebSocketUrlBuilder
{
    /**
     * Build a WebSocket URL for VNC connection.
     * 
     * @param string $targetHost The VNC server host
     * @param int $targetPort The VNC server port
     * @param string|null $token Optional authentication token
     * @return string The WebSocket URL
     */
    public function buildVncUrl(string $targetHost, int $targetPort, ?string $token = null): string
    {
        return $this->buildUrl('vnc', $targetHost, $targetPort, $token);
    }

    /**
     * Build a WebSocket URL for SSH connection.
     * 
     * @param string $targetHost The SSH server host
     * @param int $targetPort The SSH server port
     * @param string|null $token Optional authentication token
     * @return string The WebSocket URL
     */
    public function buildSshUrl(string $targetHost, int $targetPort, ?string $token = null): string
    {
        return $this->buildUrl('ssh', $targetHost, $targetPort, $token);
    }

    /**
     * Build a WebSocket URL based on type and configuration.
     * 
     * Requirements: 6.1 - Use WSS in production
     * 
     * Both VNC and SSH proxies run on the Laravel app server.
     * 
     * @param string $type Connection type ('vnc' or 'ssh')
     * @param string $targetHost The target server host
     * @param int $targetPort The target server port
     * @param string|null $token Optional authentication token
     * @return string The WebSocket URL
     */
    public function buildUrl(string $type, string $targetHost, int $targetPort, ?string $token = null): string
    {
        $protocol = $this->getProtocol();
        
        // Both VNC and SSH proxies run on the app server (Laravel server)
        // They connect to the target VPS from there
        if ($type === 'ssh') {
            $host = $this->getSshProxyHost();
        } else {
            $host = $this->getVncProxyHost();
        }
        
        $port = $this->getProxyPort($type);
        $path = $this->buildPath($type, $targetHost, $targetPort, $token);

        return "{$protocol}://{$host}:{$port}{$path}";
    }

    /**
     * Get the WebSocket protocol (ws or wss).
     * 
     * Requirements: 6.1 - Use WSS in production
     * 
     * @return string 'wss' for secure connections, 'ws' for non-secure
     */
    public function getProtocol(): string
    {
        // Always use WSS in production (Requirement 6.1)
        if (App::environment('production')) {
            return 'wss';
        }

        // For local/development, default to ws:// unless explicitly enabled
        if (App::environment('local')) {
            $sslEnabled = config('websockify.ssl.enabled', false);
            return $sslEnabled ? 'wss' : 'ws';
        }

        // For other environments (staging, testing), check config
        $secure = config('services.console.websocket.secure', true);
        $sslEnabled = config('websockify.ssl.enabled', true);

        return ($secure || $sslEnabled) ? 'wss' : 'ws';
    }

    /**
     * Check if the current configuration uses secure WebSocket.
     * 
     * @return bool True if using WSS protocol
     */
    public function isSecure(): bool
    {
        return $this->getProtocol() === 'wss';
    }

    /**
     * Get the proxy host for WebSocket connections.
     * 
     * @param string $fallbackHost Fallback host if proxy not configured
     * @return string The proxy host
     */
    protected function getProxyHost(string $fallbackHost = ''): string
    {
        // First check websockify public host
        $publicHost = config('websockify.proxy.public_host', '');
        if (!empty($publicHost)) {
            return $publicHost;
        }

        // Then check console proxy host
        $proxyHost = config('services.console.websocket.proxy_host', '');
        if (!empty($proxyHost)) {
            return $proxyHost;
        }

        // Fall back to websockify internal host
        $wsHost = config('websockify.proxy.host', '');
        if (!empty($wsHost) && $wsHost !== '127.0.0.1' && $wsHost !== 'localhost') {
            return $wsHost;
        }

        // Use the target host as fallback
        return $fallbackHost;
    }

    /**
     * Get the proxy port for the specified connection type.
     * 
     * @param string $type Connection type ('vnc' or 'ssh')
     * @return int The proxy port
     */
    protected function getProxyPort(string $type): int
    {
        if ($type === 'vnc') {
            return (int) config('websockify.proxy.vnc_port', 
                config('services.console.websocket.vnc_proxy_port', 6080));
        }

        // SSH uses separate proxy configuration
        return (int) config('websockify.ssh_proxy.port',
            config('websockify.proxy.ssh_port',
                config('services.console.websocket.ssh_proxy_port', 2222)));
    }

    /**
     * Get the proxy host for SSH connections.
     * SSH proxy runs on the app server (Laravel server), not on VPS server.
     * 
     * @return string The SSH proxy host
     */
    protected function getSshProxyHost(): string
    {
        // Check SSH proxy public host first
        $publicHost = config('websockify.ssh_proxy.public_host', '');
        if (!empty($publicHost)) {
            return $publicHost;
        }

        // Fall back to APP_URL host
        return $this->getAppHost();
    }

    /**
     * Get the proxy host for VNC connections.
     * VNC proxy runs on the app server (Laravel server), not on VPS server.
     * 
     * @return string The VNC proxy host
     */
    protected function getVncProxyHost(): string
    {
        // Check VNC proxy public host first
        $publicHost = config('websockify.proxy.public_host', '');
        if (!empty($publicHost)) {
            return $publicHost;
        }

        // Fall back to APP_URL host
        return $this->getAppHost();
    }

    /**
     * Get the app server host from APP_URL.
     * 
     * @return string The app host
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
     * Build the WebSocket path with optional parameters.
     * 
     * @param string $type Connection type
     * @param string $targetHost Target server host
     * @param int $targetPort Target server port
     * @param string|null $token Authentication token
     * @return string The URL path
     */
    protected function buildPath(string $type, string $targetHost, int $targetPort, ?string $token = null): string
    {
        $params = [];

        // Add token if provided (for token-based routing)
        if ($token !== null) {
            $params['token'] = $token;
        }

        if ($type === 'vnc') {
            // VNC uses websockify proxy
            if (config('websockify.target.use_tokens', true) && $token) {
                // Token-based routing - websockify will look up the target
                $path = '/websockify';
            } else {
                // Direct target specification for websockify
                $path = "/websockify/{$targetHost}/{$targetPort}";
            }
        } else {
            // SSH uses different WebSocket proxy
            // Get base path from config
            $basePath = config('websockify.ssh_proxy.base_path', '/ssh');
            $path = $basePath;
            
            // Add connection parameters
            $params['host'] = $targetHost;
            $params['port'] = $targetPort;
        }

        // Append query parameters if any
        if (!empty($params)) {
            $path .= '?' . http_build_query($params);
        }

        return $path;
    }

    /**
     * Validate that the WebSocket URL is properly secured for production.
     * 
     * @param string $url The WebSocket URL to validate
     * @return bool True if the URL is secure or not in production
     * @throws \InvalidArgumentException If URL is insecure in production
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
