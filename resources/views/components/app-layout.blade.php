<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@php
    $settingService = app(\App\Services\SettingService::class);
    $appName = $settingService->appName();
    $appFavicon = $settingService->appFavicon();
@endphp
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#ffffff">

    <title>{{ $appName }}</title>

    <!-- Favicon -->
    @if($appFavicon)
        <link rel="icon" type="image/png" href="{{ $appFavicon }}">
        <link rel="shortcut icon" href="{{ $appFavicon }}">
    @endif

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
    <div x-data="{ sidebarOpen: false }" class="min-h-screen bg-surface-50 dark:bg-surface-900">
        @auth
            <!-- Mobile Sidebar Backdrop -->
            <div 
                x-show="sidebarOpen" 
                x-transition:enter="transition-opacity ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                @click="sidebarOpen = false"
                class="fixed inset-0 bg-surface-900/50 backdrop-blur-sm z-30 lg:hidden"
            ></div>

            <!-- Desktop Sidebar -->
            <x-sidebar />

            <!-- Mobile Sidebar -->
            <x-sidebar :mobile="true" />

            <!-- Main Content -->
            <div class="main-content">
                <!-- Top Header Bar -->
                <header class="sticky top-0 z-20 bg-white/80 dark:bg-surface-900/80 backdrop-blur-md border-b border-surface-200 dark:border-surface-700">
                    <div class="flex items-center justify-between h-16 px-4">
                        <!-- Mobile Menu Button -->
                        <button 
                            @click="sidebarOpen = true" 
                            class="lg:hidden p-2 -ml-2 text-surface-500 hover:text-surface-700 dark:text-surface-400 dark:hover:text-surface-200 rounded-lg hover:bg-surface-100 dark:hover:bg-surface-800"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>

                        <!-- Page Title -->
                        @isset($header)
                            <div class="flex-1 lg:flex-none">
                                <h1 class="text-lg font-display font-semibold text-surface-900 dark:text-white truncate">
                                    {{ $header }}
                                </h1>
                            </div>
                        @endisset

                        <!-- Right Side Actions -->
                        <div class="flex items-center gap-2">
                            <!-- Theme Toggle (Desktop) -->
                            <button data-theme-toggle class="hidden sm:flex theme-toggle">
                                <svg data-theme-icon="sun" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                                <svg data-theme-icon="moon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                                </svg>
                            </button>

                            <!-- User Avatar (Mobile) -->
                            <div class="lg:hidden">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white text-sm font-medium">
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Page Content -->
                <main class="p-4 sm:p-6 lg:p-8">
                    {{ $slot }}
                </main>
            </div>
        @else
            <!-- Guest Content -->
            <main>
                {{ $slot }}
            </main>
        @endauth
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
