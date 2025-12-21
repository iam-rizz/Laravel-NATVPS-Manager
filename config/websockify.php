<?php

/**
 * Websockify Configuration for VNC Proxy
 * 
 * This configuration file defines settings for websockify, which acts as a
 * WebSocket-to-TCP proxy for VNC connections. Websockify allows noVNC clients
 * to connect to VNC servers through WebSocket protocol.
 * 
 * Requirements: 6.1 - Use WSS (WebSocket Secure) protocol
 * 
 * Installation:
 * - Install websockify: pip install websockify
 * - Or use Docker: docker run -d --name websockify -p 6080:6080 novnc/websockify
 * 
 * Usage:
 * websockify --web /usr/share/novnc/ --cert=/path/to/cert.pem --key=/path/to/key.pem 6080
 */

return [

    /*
    |--------------------------------------------------------------------------
    | WebSocket Proxy Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for the websockify proxy server that handles VNC
    | connections over WebSocket protocol.
    |
    */

    'proxy' => [
        // Enable/disable the proxy
        'enabled' => env('WEBSOCKIFY_ENABLED', true),

        // Proxy host (where websockify is running)
        'host' => env('WEBSOCKIFY_HOST', '127.0.0.1'),

        // Proxy port for VNC connections
        'vnc_port' => env('WEBSOCKIFY_VNC_PORT', 6080),

        // Proxy port for SSH connections (if using separate proxy)
        'ssh_port' => env('WEBSOCKIFY_SSH_PORT', 2222),

        // Public host for client connections (may differ from internal host)
        'public_host' => env('WEBSOCKIFY_PUBLIC_HOST', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | SSH WebSocket Proxy Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for SSH-over-WebSocket proxy. This requires a separate
    | SSH proxy server like wetty, ttyd, or a custom Node.js SSH proxy.
    |
    | Options:
    | - wetty: https://github.com/butlerx/wetty
    | - ttyd: https://github.com/tsl0922/ttyd
    | - Custom Node.js with ssh2 library
    |
    */

    'ssh_proxy' => [
        // Enable/disable SSH proxy
        'enabled' => env('SSH_PROXY_ENABLED', true),

        // SSH proxy type: 'wetty', 'ttyd', 'custom'
        'type' => env('SSH_PROXY_TYPE', 'custom'),

        // SSH proxy host
        'host' => env('SSH_PROXY_HOST', '127.0.0.1'),

        // SSH proxy port
        'port' => env('SSH_PROXY_PORT', 2222),

        // Public host for SSH proxy (for client connections)
        'public_host' => env('SSH_PROXY_PUBLIC_HOST', ''),

        // SSH proxy base path
        'base_path' => env('SSH_PROXY_BASE_PATH', '/ssh'),

        // Use SSL for SSH proxy
        'ssl' => env('SSH_PROXY_SSL', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | SSL/TLS Configuration
    |--------------------------------------------------------------------------
    |
    | SSL/TLS settings for secure WebSocket (WSS) connections.
    | Required for production environments per Requirement 6.1.
    |
    */

    'ssl' => [
        // Force WSS in production (Requirement 6.1)
        'enabled' => env('WEBSOCKIFY_SSL_ENABLED', true),

        // Path to SSL certificate file
        'cert' => env('WEBSOCKIFY_SSL_CERT', '/etc/ssl/certs/websockify.crt'),

        // Path to SSL private key file
        'key' => env('WEBSOCKIFY_SSL_KEY', '/etc/ssl/private/websockify.key'),

        // Path to CA certificate chain (optional)
        'ca' => env('WEBSOCKIFY_SSL_CA', ''),

        // Verify SSL certificates
        'verify' => env('WEBSOCKIFY_SSL_VERIFY', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Connection Settings
    |--------------------------------------------------------------------------
    |
    | Settings for WebSocket connection handling.
    |
    */

    'connection' => [
        // Connection timeout in seconds
        'timeout' => env('WEBSOCKIFY_TIMEOUT', 30),

        // Idle timeout before disconnection (seconds)
        'idle_timeout' => env('WEBSOCKIFY_IDLE_TIMEOUT', 1800),

        // Maximum concurrent connections per proxy instance
        'max_connections' => env('WEBSOCKIFY_MAX_CONNECTIONS', 100),

        // Enable heartbeat/ping-pong
        'heartbeat' => env('WEBSOCKIFY_HEARTBEAT', true),

        // Heartbeat interval in seconds
        'heartbeat_interval' => env('WEBSOCKIFY_HEARTBEAT_INTERVAL', 30),
    ],

    /*
    |--------------------------------------------------------------------------
    | Target Mapping
    |--------------------------------------------------------------------------
    |
    | Configuration for mapping WebSocket connections to VNC targets.
    | Websockify can use a token-based system to route connections.
    |
    */

    'target' => [
        // Use token-based target mapping
        'use_tokens' => env('WEBSOCKIFY_USE_TOKENS', true),

        // Token file path (for file-based token mapping)
        'token_file' => env('WEBSOCKIFY_TOKEN_FILE', storage_path('app/websockify-tokens')),

        // Token plugin (for dynamic token resolution)
        'token_plugin' => env('WEBSOCKIFY_TOKEN_PLUGIN', ''),

        // Default target host (if not using tokens)
        'default_host' => env('WEBSOCKIFY_DEFAULT_HOST', ''),

        // Default target port (if not using tokens)
        'default_port' => env('WEBSOCKIFY_DEFAULT_PORT', 5900),
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging
    |--------------------------------------------------------------------------
    |
    | Logging configuration for websockify proxy.
    |
    */

    'logging' => [
        // Enable verbose logging
        'verbose' => env('WEBSOCKIFY_VERBOSE', false),

        // Log file path
        'file' => env('WEBSOCKIFY_LOG_FILE', storage_path('logs/websockify.log')),

        // Log level: debug, info, warning, error
        'level' => env('WEBSOCKIFY_LOG_LEVEL', 'info'),
    ],

];
