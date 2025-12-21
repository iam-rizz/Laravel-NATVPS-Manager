<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | VPS Console Access Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for VPS console access including VNC (noVNC) and SSH
    | (xterm.js) WebSocket proxy settings and security configurations.
    |
    */

    'console' => [
        // WebSocket proxy settings
        'websocket' => [
            // Use WSS (WebSocket Secure) in production
            'secure' => env('CONSOLE_WEBSOCKET_SECURE', true),
            // WebSocket proxy host (websockify)
            'proxy_host' => env('CONSOLE_WEBSOCKET_PROXY_HOST', ''),
            // WebSocket proxy port for VNC
            'vnc_proxy_port' => env('CONSOLE_VNC_PROXY_PORT', 6080),
            // WebSocket proxy port for SSH
            'ssh_proxy_port' => env('CONSOLE_SSH_PROXY_PORT', 2222),
        ],

        // Connection timeout settings (in seconds)
        'timeouts' => [
            // Connection establishment timeout
            'connection' => env('CONSOLE_CONNECTION_TIMEOUT', 30),
            // Idle timeout before disconnection
            'idle' => env('CONSOLE_IDLE_TIMEOUT', 1800), // 30 minutes
            // Keep-alive interval
            'keepalive' => env('CONSOLE_KEEPALIVE_INTERVAL', 30),
        ],

        // Security settings
        'security' => [
            // Token TTL in minutes (max 5 minutes per requirements)
            'token_ttl' => env('CONSOLE_TOKEN_TTL', 5),
            // Maximum concurrent console sessions per user
            'max_sessions' => env('CONSOLE_MAX_SESSIONS', 3),
        ],

        // VNC specific settings
        'vnc' => [
            // Default scaling mode: 'fit' or 'actual'
            'default_scaling' => env('CONSOLE_VNC_SCALING', 'fit'),
            // Enable clipboard sharing
            'clipboard' => env('CONSOLE_VNC_CLIPBOARD', true),
            // Quality level (0-9, higher is better)
            'quality' => env('CONSOLE_VNC_QUALITY', 6),
            // Compression level (0-9, higher is more compression)
            'compression' => env('CONSOLE_VNC_COMPRESSION', 2),
        ],

        // SSH specific settings
        'ssh' => [
            // Default terminal type
            'term' => env('CONSOLE_SSH_TERM', 'xterm-256color'),
            // Default terminal columns
            'cols' => env('CONSOLE_SSH_COLS', 80),
            // Default terminal rows
            'rows' => env('CONSOLE_SSH_ROWS', 24),
        ],
    ],

];
