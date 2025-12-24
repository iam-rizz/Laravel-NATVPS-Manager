@props(['compact' => false])

@if(config('websockify.proxy.enabled', true))
<div 
    x-data="consoleProxyStatus()"
    x-init="checkStatus()"
    {{ $attributes->merge(['class' => $compact ? '' : 'card']) }}
>
    @if(!$compact)
    <div class="card-header">
        <h3 class="card-title flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            {{ __('app.console_proxy_status') ?? 'Console Proxy' }}
        </h3>
    </div>
    @endif

    <div class="{{ $compact ? '' : 'card-body' }}">
        {{-- Loading State --}}
        <div x-show="loading" class="flex items-center gap-2 text-surface-500">
            <div class="relative">
                <div class="w-4 h-4 border-2 border-surface-300 dark:border-surface-600 rounded-full"></div>
                <div class="absolute inset-0 w-4 h-4 border-2 border-transparent border-t-primary-500 rounded-full animate-spin"></div>
            </div>
            <span class="text-sm">{{ __('app.checking') ?? 'Checking...' }}</span>
        </div>

        {{-- Status Display --}}
        <div x-show="!loading" x-cloak>
            {{-- Compact Mode --}}
            @if($compact)
            <div class="flex items-center gap-2.5">
                {{-- Modern Status Indicator with Pulse --}}
                <div class="relative flex items-center justify-center">
                    <span 
                        class="absolute w-3 h-3 rounded-full opacity-40 animate-ping"
                        :class="{
                            'bg-emerald-400': status === 'online',
                            'bg-red-400': status === 'offline' || status === 'error',
                            'bg-amber-400': status === 'disabled',
                            'bg-gray-400': !status
                        }"
                        x-show="status === 'online'"
                    ></span>
                    <span 
                        class="relative w-2.5 h-2.5 rounded-full shadow-sm"
                        :class="{
                            'bg-gradient-to-br from-emerald-400 to-emerald-600 shadow-emerald-500/50': status === 'online',
                            'bg-gradient-to-br from-red-400 to-red-600 shadow-red-500/50': status === 'offline' || status === 'error',
                            'bg-gradient-to-br from-amber-400 to-amber-600 shadow-amber-500/50': status === 'disabled',
                            'bg-gray-400': !status
                        }"
                    ></span>
                </div>
                <span 
                    class="text-sm font-medium transition-colors duration-200"
                    :class="{
                        'text-emerald-600 dark:text-emerald-400': status === 'online',
                        'text-red-600 dark:text-red-400': status === 'offline' || status === 'error',
                        'text-amber-600 dark:text-amber-400': status === 'disabled'
                    }" 
                    x-text="statusText"
                ></span>
                <button 
                    @click="checkStatus()" 
                    class="p-1.5 rounded-md text-surface-400 hover:text-surface-600 dark:hover:text-surface-300 hover:bg-surface-100 dark:hover:bg-surface-800 transition-all duration-200"
                    :class="{ 'animate-spin': loading }"
                    title="{{ __('app.refresh') ?? 'Refresh' }}"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </button>
            </div>
            @else
            {{-- Full Mode --}}
            <div class="space-y-4">
                {{-- Modern Status Badge --}}
                <div class="flex items-center justify-between">
                    <span class="text-sm text-surface-600 dark:text-surface-400">{{ __('app.status') ?? 'Status' }}</span>
                    <div 
                        class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-sm font-medium transition-all duration-300"
                        :class="{
                            'bg-gradient-to-r from-emerald-50 to-emerald-100 dark:from-emerald-900/40 dark:to-emerald-800/30 text-emerald-700 dark:text-emerald-300 ring-1 ring-emerald-200 dark:ring-emerald-700/50': status === 'online',
                            'bg-gradient-to-r from-red-50 to-red-100 dark:from-red-900/40 dark:to-red-800/30 text-red-700 dark:text-red-300 ring-1 ring-red-200 dark:ring-red-700/50': status === 'offline' || status === 'error',
                            'bg-gradient-to-r from-amber-50 to-amber-100 dark:from-amber-900/40 dark:to-amber-800/30 text-amber-700 dark:text-amber-300 ring-1 ring-amber-200 dark:ring-amber-700/50': status === 'disabled'
                        }"
                    >
                        {{-- Animated Status Dot --}}
                        <div class="relative flex items-center justify-center">
                            <span 
                                class="absolute w-2.5 h-2.5 rounded-full opacity-40 animate-ping"
                                :class="{
                                    'bg-emerald-500': status === 'online',
                                    'bg-red-500': status === 'offline' || status === 'error',
                                    'bg-amber-500': status === 'disabled'
                                }"
                                x-show="status === 'online'"
                            ></span>
                            <span 
                                class="relative w-2 h-2 rounded-full"
                                :class="{
                                    'bg-emerald-500': status === 'online',
                                    'bg-red-500': status === 'offline' || status === 'error',
                                    'bg-amber-500': status === 'disabled'
                                }"
                            ></span>
                        </div>
                        <span x-text="statusText"></span>
                        {{-- Status Icon --}}
                        <template x-if="status === 'online'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </template>
                        <template x-if="status === 'offline' || status === 'error'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </template>
                        <template x-if="status === 'disabled'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                            </svg>
                        </template>
                    </div>
                </div>

                {{-- Host --}}
                <div class="flex items-center justify-between py-2 border-b border-surface-100 dark:border-surface-700/50">
                    <span class="text-sm text-surface-600 dark:text-surface-400 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/>
                        </svg>
                        {{ __('app.host') ?? 'Host' }}
                    </span>
                    <span class="text-sm font-mono px-2 py-1 bg-surface-100 dark:bg-surface-800 rounded text-surface-900 dark:text-surface-100" x-text="host || '-'"></span>
                </div>

                {{-- Services --}}
                <div x-show="services && services.length > 0" class="flex items-center justify-between py-2 border-b border-surface-100 dark:border-surface-700/50">
                    <span class="text-sm text-surface-600 dark:text-surface-400 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                        {{ __('app.services') ?? 'Services' }}
                    </span>
                    <div class="flex gap-1.5">
                        <template x-for="service in services" :key="service">
                            <span class="px-2.5 py-1 bg-gradient-to-r from-primary-50 to-primary-100 dark:from-primary-900/30 dark:to-primary-800/20 text-primary-700 dark:text-primary-400 text-xs font-medium rounded-md ring-1 ring-primary-200 dark:ring-primary-700/50 uppercase" x-text="service"></span>
                        </template>
                    </div>
                </div>

                {{-- Response Time --}}
                <div x-show="responseTime" class="flex items-center justify-between py-2 border-b border-surface-100 dark:border-surface-700/50">
                    <span class="text-sm text-surface-600 dark:text-surface-400 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ __('app.response_time') ?? 'Response Time' }}
                    </span>
                    <span 
                        class="text-sm font-medium px-2 py-1 rounded"
                        :class="{
                            'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400': responseTime < 100,
                            'bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400': responseTime >= 100 && responseTime < 300,
                            'bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400': responseTime >= 300
                        }"
                    >
                        <span x-text="responseTime"></span>ms
                    </span>
                </div>

                {{-- Error Message --}}
                <div 
                    x-show="error" 
                    class="p-3 bg-gradient-to-r from-red-50 to-red-100 dark:from-red-900/30 dark:to-red-800/20 rounded-lg ring-1 ring-red-200 dark:ring-red-700/50"
                >
                    <div class="flex items-start gap-2">
                        <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-sm text-red-600 dark:text-red-400" x-text="error"></span>
                    </div>
                </div>

                {{-- Refresh Button --}}
                <button 
                    @click="checkStatus()" 
                    class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-gradient-to-r from-surface-100 to-surface-200 dark:from-surface-800 dark:to-surface-700 hover:from-surface-200 hover:to-surface-300 dark:hover:from-surface-700 dark:hover:to-surface-600 text-surface-700 dark:text-surface-300 rounded-lg font-medium text-sm transition-all duration-200 ring-1 ring-surface-200 dark:ring-surface-600"
                    :disabled="loading"
                >
                    <svg 
                        class="w-4 h-4 transition-transform duration-300" 
                        :class="{ 'animate-spin': loading }" 
                        fill="none" 
                        stroke="currentColor" 
                        viewBox="0 0 24 24"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    <span>{{ __('app.refresh') ?? 'Refresh' }}</span>
                </button>

                {{-- Last Checked --}}
                <p x-show="checkedAt" class="text-xs text-surface-400 text-center flex items-center justify-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>{{ __('app.last_checked') ?? 'Last checked' }}: <span x-text="checkedAt"></span></span>
                </p>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('consoleProxyStatus', () => ({
        loading: true,
        status: null,
        statusText: '',
        host: null,
        services: [],
        responseTime: null,
        error: null,
        checkedAt: null,
        refreshInterval: {{ config('websockify.health.refresh_interval', 60) }} * 1000,
        intervalId: null,

        async init() {
            await this.checkStatus();
            
            // Auto-refresh if interval > 0
            if (this.refreshInterval > 0) {
                this.intervalId = setInterval(() => this.checkStatus(), this.refreshInterval);
            }
        },

        destroy() {
            if (this.intervalId) {
                clearInterval(this.intervalId);
            }
        },

        async checkStatus() {
            this.loading = true;
            this.error = null;

            try {
                const response = await fetch('{{ route("console.proxy-health") }}');
                const data = await response.json();

                this.status = data.status;
                this.host = data.host;
                this.services = data.services || [];
                this.responseTime = data.response_time_ms;
                this.error = data.error;
                
                // Format status text
                switch (data.status) {
                    case 'online':
                        this.statusText = '{{ __("app.online") ?? "Online" }}';
                        break;
                    case 'offline':
                        this.statusText = '{{ __("app.offline") ?? "Offline" }}';
                        break;
                    case 'disabled':
                        this.statusText = '{{ __("app.disabled") ?? "Disabled" }}';
                        break;
                    case 'error':
                        this.statusText = '{{ __("app.error") ?? "Error" }}';
                        break;
                    default:
                        this.statusText = data.status;
                }

                // Format checked time
                if (data.checked_at) {
                    const date = new Date(data.checked_at);
                    this.checkedAt = date.toLocaleTimeString();
                }

            } catch (err) {
                this.status = 'error';
                this.statusText = '{{ __("app.error") ?? "Error" }}';
                this.error = err.message;
            } finally {
                this.loading = false;
            }
        }
    }));
});
</script>
@endif
