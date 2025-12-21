<?php

/**
 * Console Proxy Configuration
 * 
 * Unified configuration for VNC and SSH WebSocket proxy.
 * The console proxy handles both VNC (noVNC) and SSH (xterm.js) connections
 * through a single WebSocket server.
 * 
 * Endpoints:
 * - VNC: /websockify/HOST/PORT
 * - SSH: /ssh?host=HOST&port=PORT
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Console Proxy Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for the unified console proxy server that handles both
    | VNC and SSH connections over WebSocket protocol.
    |
    */

    'proxy' => [
        // Enable/disable the console proxy
        'enabled' => env('CONSOLE_PROXY_ENABLED', true),

        // Internal proxy host (where console-proxy Node.js server is running)
        'host' => env('CONSOLE_PROXY_HOST', '127.0.0.1'),

        // Proxy port (single port for both VNC and SSH)
        'port' => env('CONSOLE_PROXY_PORT', 6080),

        // Public host for client WebSocket connections (domain name)
        // If set, WebSocket URL will use this domain without port (via reverse proxy)
        // If empty, will use APP_URL host with port
        'public_host' => env('CONSOLE_PROXY_PUBLIC_HOST', ''),

        // Use SSL/WSS for WebSocket connections
        'ssl' => env('CONSOLE_PROXY_SSL', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Connection Settings
    |--------------------------------------------------------------------------
    */

    'connection' => [
        // Connection timeout in seconds
        'timeout' => env('CONSOLE_CONNECTION_TIMEOUT', 30),

        // Idle timeout before disconnection (seconds)
        'idle_timeout' => env('CONSOLE_IDLE_TIMEOUT', 1800),
    ],

    /*
    |--------------------------------------------------------------------------
    | VNC Settings
    |--------------------------------------------------------------------------
    */

    'vnc' => [
        // VNC quality (1-9, higher = better quality, more bandwidth)
        'quality' => env('CONSOLE_VNC_QUALITY', 6),

        // VNC compression level (0-9)
        'compression' => env('CONSOLE_VNC_COMPRESSION', 2),
    ],

    /*
    |--------------------------------------------------------------------------
    | SSH Settings
    |--------------------------------------------------------------------------
    */

    'ssh' => [
        // Terminal type
        'term' => env('CONSOLE_SSH_TERM', 'xterm-256color'),

        // Default terminal columns
        'cols' => env('CONSOLE_SSH_COLS', 80),

        // Default terminal rows
        'rows' => env('CONSOLE_SSH_ROWS', 24),
    ],

];
