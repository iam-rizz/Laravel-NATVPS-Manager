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
            <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-sm">{{ __('app.checking') ?? 'Checking...' }}</span>
        </div>

        {{-- Status Display --}}
        <div x-show="!loading" x-cloak>
            {{-- Compact Mode --}}
            @if($compact)
            <div class="flex items-center gap-2">
                <span 
                    class="w-2 h-2 rounded-full"
                    :class="{
                        'bg-green-500': status === 'online',
                        'bg-red-500': status === 'offline' || status === 'error',
                        'bg-yellow-500': status === 'disabled',
                        'bg-gray-400': !status
                    }"
                ></span>
                <span class="text-sm" :class="{
                    'text-green-600 dark:text-green-400': status === 'online',
                    'text-red-600 dark:text-red-400': status === 'offline' || status === 'error',
                    'text-yellow-600 dark:text-yellow-400': status === 'disabled'
                }" x-text="statusText"></span>
                <button 
                    @click="checkStatus()" 
                    class="p-1 text-surface-400 hover:text-surface-600 dark:hover:text-surface-300"
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
            <div class="space-y-3">
                {{-- Status Badge --}}
                <div class="flex items-center justify-between">
                    <span class="text-sm text-surface-600 dark:text-surface-400">{{ __('app.status') ?? 'Status' }}</span>
                    <span 
                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium"
                        :class="{
                            'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400': status === 'online',
                            'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400': status === 'offline' || status === 'error',
                            'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400': status === 'disabled'
                        }"
                    >
                        <span 
                            class="w-1.5 h-1.5 rounded-full"
                            :class="{
                                'bg-green-500': status === 'online',
                                'bg-red-500': status === 'offline' || status === 'error',
                                'bg-yellow-500': status === 'disabled'
                            }"
                        ></span>
                        <span x-text="statusText"></span>
                    </span>
                </div>

                {{-- Host --}}
                <div class="flex items-center justify-between">
                    <span class="text-sm text-surface-600 dark:text-surface-400">{{ __('app.host') ?? 'Host' }}</span>
                    <span class="text-sm font-mono text-surface-900 dark:text-surface-100" x-text="host || '-'"></span>
                </div>

                {{-- Services --}}
                <div x-show="services && services.length > 0" class="flex items-center justify-between">
                    <span class="text-sm text-surface-600 dark:text-surface-400">{{ __('app.services') ?? 'Services' }}</span>
                    <div class="flex gap-1.5">
                        <template x-for="service in services" :key="service">
                            <span class="px-2 py-0.5 bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-400 text-xs rounded uppercase" x-text="service"></span>
                        </template>
                    </div>
                </div>

                {{-- Response Time --}}
                <div x-show="responseTime" class="flex items-center justify-between">
                    <span class="text-sm text-surface-600 dark:text-surface-400">{{ __('app.response_time') ?? 'Response Time' }}</span>
                    <span class="text-sm text-surface-900 dark:text-surface-100" x-text="responseTime + 'ms'"></span>
                </div>

                {{-- Error Message --}}
                <div x-show="error" class="p-2 bg-red-50 dark:bg-red-900/20 rounded text-sm text-red-600 dark:text-red-400" x-text="error"></div>

                {{-- Refresh Button --}}
                <button 
                    @click="checkStatus()" 
                    class="w-full btn btn-sm btn-secondary flex items-center justify-center gap-2"
                    :disabled="loading"
                >
                    <svg class="w-4 h-4" :class="{ 'animate-spin': loading }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    <span>{{ __('app.refresh') ?? 'Refresh' }}</span>
                </button>

                {{-- Last Checked --}}
                <p x-show="checkedAt" class="text-xs text-surface-400 text-center" x-text="'{{ __('app.last_checked') ?? 'Last checked' }}: ' + checkedAt"></p>
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
