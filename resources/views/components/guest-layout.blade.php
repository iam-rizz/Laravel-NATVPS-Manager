<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@php
    $settingService = app(\App\Services\SettingService::class);
    $appName = $settingService->appName();
    $appLogo = $settingService->appLogo();
@endphp
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#ffffff">

    <title>{{ $appName }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Prevent flash of wrong theme -->
    <script>
        (function() {
            const theme = localStorage.getItem('theme') || 
                (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            if (theme === 'dark') {
                document.documentElement.classList.add('dark');
                document.querySelector('meta[name="theme-color"]')?.setAttribute('content', '#0f172a');
            }
        })();
    </script>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-surface-100 via-surface-50 to-primary-50 dark:from-surface-900 dark:via-surface-900 dark:to-surface-800">
        <!-- Theme Toggle -->
        <div class="fixed top-4 right-4">
            <button data-theme-toggle class="theme-toggle bg-white dark:bg-surface-800 shadow-soft rounded-xl">
                <svg data-theme-icon="sun" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                <svg data-theme-icon="moon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                </svg>
            </button>
        </div>

        <!-- Logo -->
        <div class="mb-6">
            <a href="/" class="flex items-center gap-3">
                @if($appLogo)
                    <img src="{{ $appLogo }}" alt="{{ $appName }}" class="w-14 h-14 rounded-2xl object-contain">
                @else
                    <div class="w-14 h-14 bg-gradient-to-br from-primary-500 to-primary-700 rounded-2xl flex items-center justify-center shadow-lg shadow-primary-500/30">
                        <svg class="w-8 h-8 text-white" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>
                        </svg>
                    </div>
                @endif
            </a>
        </div>

        <!-- Card -->
        <div class="w-full sm:max-w-md">
            <div class="card overflow-hidden">
                {{ $slot }}
            </div>
        </div>

        <!-- Footer -->
        <p class="mt-8 text-sm text-surface-500 dark:text-surface-400">
            &copy; {{ date('Y') }} {{ $appName }}. All rights reserved.
        </p>
    </div>

    <!-- Toast Notifications -->
    @if(session('success') || session('error') || session('warning') || session('info'))
    <script>
    (function() {
        var maxRetries = 50;
        var retryCount = 0;
        
        function showToasts() {
            retryCount++;
            
            if (typeof window.toast === 'undefined') {
                if (retryCount < maxRetries) {
                    setTimeout(showToasts, 100);
                }
                return;
            }
            
            @if(session('success'))
                window.toast.success({!! json_encode(session('success')) !!});
            @endif

            @if(session('error'))
                window.toast.error({!! json_encode(session('error')) !!});
            @endif

            @if(session('warning'))
                window.toast.warning({!! json_encode(session('warning')) !!});
            @endif

            @if(session('info'))
                window.toast.info({!! json_encode(session('info')) !!});
            @endif
        }
        
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', showToasts);
        } else {
            setTimeout(showToasts, 50);
        }
    })();
    </script>
    @endif
</body>
</html>
