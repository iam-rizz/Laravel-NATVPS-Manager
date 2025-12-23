<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <a href="{{ route('vps.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 mr-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight" style="font-size: 16px;">
                    VPS: {{ $natVps->hostname }}
                </h2>
            </div>
            @if($apiOffline)
                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    {{ __('app.api_offline') }}
                </span>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @php
                $cachedSpecs = $natVps->cached_specs;
                $status = $liveInfo?->status ?? $cachedSpecs['status'] ?? null;
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- VPS Specifications Card -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">{{ __('app.specifications') }}</h3>
                        <dl class="space-y-3">
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500 dark:text-gray-400">{{ __('app.hostname') }}</dt>
                                <dd class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $natVps->hostname }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500 dark:text-gray-400">{{ __('app.vps_id') }}</dt>
                                <dd class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $natVps->vps_id }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500 dark:text-gray-400">UUID</dt>
                                <dd class="text-sm font-medium text-gray-900 dark:text-gray-100 font-mono text-xs">
                                    {{ $liveInfo?->uuid ?? $cachedSpecs['uuid'] ?? 'N/A' }}
                                </dd>
                            </div>
                            @php
                                $osName = $liveInfo?->osName ?? $cachedSpecs['os_name'] ?? null;
                                $osIcon = $liveInfo?->getOsIcon() ?? 'linux';
                            @endphp
                            <div class="flex justify-between items-center">
                                <dt class="text-sm text-gray-500 dark:text-gray-400">{{ __('app.os') }}</dt>
                                <dd class="text-sm font-medium text-gray-900 dark:text-gray-100 flex items-center">
                                    @if($osName)
                                        <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/{{ $osIcon }}/{{ $osIcon }}-original.svg" 
                                             alt="{{ $osIcon }}" 
                                             class="w-5 h-5 mr-2"
                                             onerror="this.onerror=null; this.src='https://cdn.jsdelivr.net/gh/devicons/devicon/icons/linux/linux-original.svg';">
                                        {{ $osName }}
                                    @else
                                        -
                                    @endif
                                </dd>
                            </div>
                            @php
                                $serverLocation = $natVps->server?->location_data;
                                $location = $serverLocation ? $natVps->server->getLocationString() : null;
                                $isp = $serverLocation['isp'] ?? $serverLocation['org'] ?? null;
                            @endphp
                            <div class="flex justify-between items-center">
                                <dt class="text-sm text-gray-500 dark:text-gray-400">{{ __('app.region') }}</dt>
                                <dd class="text-sm font-medium text-gray-900 dark:text-gray-100 flex items-center">
                                    @if($location)
                                        <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        {{ $location }}
                                    @else
                                        -
                                    @endif
                                </dd>
                            </div>
                            @if($isp)
                            <div class="flex justify-between items-center">
                                <dt class="text-sm text-gray-500 dark:text-gray-400">ISP</dt>
                                <dd class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $isp }}</dd>
                            </div>
                            @endif
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500 dark:text-gray-400">{{ __('app.cpu') }}</dt>
                                <dd class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ $liveInfo?->cpu ?? $cachedSpecs['cpu'] ?? '-' }} Core(s)
                                </dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500 dark:text-gray-400">{{ __('app.ram') }}</dt>
                                <dd class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ ($liveInfo?->ram ?? $cachedSpecs['ram'] ?? null) ? ($liveInfo?->ram ?? $cachedSpecs['ram']) . ' MB' : '-' }}
                                </dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500 dark:text-gray-400">{{ __('app.disk') }}</dt>
                                <dd class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ ($liveInfo?->disk ?? $cachedSpecs['disk'] ?? null) ? ($liveInfo?->disk ?? $cachedSpecs['disk']) . ' GB' : '-' }}
                                </dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500 dark:text-gray-400">{{ __('app.bandwidth') }}</dt>
                                <dd class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                    @php
                                        $bandwidth = $liveInfo?->bandwidth ?? $cachedSpecs['bandwidth'] ?? null;
                                        $usedBandwidth = $liveInfo?->usedBandwidth ?? $cachedSpecs['used_bandwidth'] ?? null;
                                    @endphp
                                    @if($bandwidth)
                                        {{ $usedBandwidth ?? 0 }} / {{ $bandwidth }} GB
                                    @else
                                        -
                                    @endif
                                </dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500 dark:text-gray-400">{{ __('app.status') }}</dt>
                                <dd>
                                    @if($status === 1)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                            {{ __('app.running') }}
                                        </span>
                                    @elseif($status === 0)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                            {{ __('app.stopped') }}
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                            {{ __('app.unknown') }}
                                        </span>
                                    @endif
                                </dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500 dark:text-gray-400">{{ __('app.server_name') }}</dt>
                                <dd class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ $natVps->server?->name ?? __('app.no_data') }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- SSH Credentials Card -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg" x-data="{ editing: false }">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('app.ssh_credentials') }}</h3>
                            <div class="flex items-center gap-1">
                                <button type="button" title="{{ __('app.console') }}"
                                    @click="$dispatch('open-console', { vpsId: {{ $natVps->id }}, tab: 'vnc' })"
                                    class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700 focus:bg-emerald-700 active:bg-emerald-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </button>
                                <button type="button" 
                                        title="{{ __('app.edit') }}"
                                        @click="editing = !editing"
                                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <svg x-show="!editing" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    <svg x-show="editing" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- View Mode -->
                        <div x-show="!editing">
                            <dl class="space-y-3">
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-500 dark:text-gray-400">{{ __('app.ssh_username') }}</dt>
                                    <dd class="text-sm font-medium text-gray-900 dark:text-gray-100 font-mono">
                                        {{ $natVps->ssh_username ?? __('app.no_data') }}
                                    </dd>
                                </div>
                                <div class="flex justify-between items-center">
                                    <dt class="text-sm text-gray-500 dark:text-gray-400">{{ __('app.ssh_password') }}</dt>
                                    <dd class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                        @if($natVps->ssh_password)
                                            <span x-data="{ show: false }">
                                                <span x-show="!show" class="font-mono">••••••••</span>
                                                <span x-show="show" class="font-mono">{{ $natVps->ssh_password }}</span>
                                                <button type="button" @click="show = !show" class="ml-2 text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 text-xs">
                                                    <span x-show="!show">{{ __('app.show') }}</span>
                                                    <span x-show="show">{{ __('app.hide') }}</span>
                                                </button>
                                            </span>
                                        @else
                                            {{ __('app.no_data') }}
                                        @endif
                                    </dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-500 dark:text-gray-400">{{ __('app.ssh_port') }}</dt>
                                    <dd class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $natVps->ssh_port ?? 22 }}</dd>
                                </div>
                            </dl>

                            @if($natVps->ssh_username && $natVps->server)
                                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">{{ __('app.ssh_command') }}:</p>
                                    <code class="block p-2 bg-gray-100 dark:bg-gray-700 rounded text-sm font-mono text-gray-800 dark:text-gray-200 break-all">
                                        ssh {{ $natVps->ssh_username . '@' . $natVps->server->ip_address }} -p {{ $natVps->ssh_port ?? 22 }}
                                    </code>
                                </div>
                            @endif
                        </div>

                        <!-- Edit Mode -->
                        <form x-show="editing" action="{{ route('vps.update-ssh', $natVps) }}" method="POST" class="space-y-4">
                            @csrf
                            @method('PUT')
                            
                            <div>
                                <label for="ssh_username" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    {{ __('app.ssh_username') }}
                                </label>
                                <input type="text" 
                                       name="ssh_username" 
                                       id="ssh_username" 
                                       value="{{ old('ssh_username', $natVps->ssh_username) }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                       placeholder="root">
                                @error('ssh_username')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div x-data="{ showPassword: false }">
                                <label for="ssh_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    {{ __('app.ssh_password') }}
                                </label>
                                <div class="mt-1 relative">
                                    <input :type="showPassword ? 'text' : 'password'" 
                                           name="ssh_password" 
                                           id="ssh_password" 
                                           value="{{ old('ssh_password', $natVps->ssh_password) }}"
                                           class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm pr-10">
                                    <button type="button" 
                                            @click="showPassword = !showPassword"
                                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                                        <svg x-show="!showPassword" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        <svg x-show="showPassword" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                        </svg>
                                    </button>
                                </div>
                                @error('ssh_password')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="ssh_port" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    {{ __('app.ssh_port') }}
                                </label>
                                <input type="number" 
                                       name="ssh_port" 
                                       id="ssh_port" 
                                       value="{{ old('ssh_port', $natVps->ssh_port ?? 22) }}"
                                       min="1"
                                       max="65535"
                                       class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                @error('ssh_port')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex justify-end pt-2">
                                <button type="submit" 
                                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    {{ __('app.save') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Resource Usage Card (loaded via AJAX) -->
                <x-resource-usage :apiEndpoint="route('vps.resource-usage', $natVps)" :apiOffline="$apiOffline" />

                <!-- Power Actions Card -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg md:col-span-2">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">{{ __('app.power_actions') }}</h3>
                        
                        @if($apiOffline)
                            <div class="p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg mb-4">
                                <p class="text-sm text-yellow-700 dark:text-yellow-300">
                                    {{ __('app.power_actions_disabled') }}
                                </p>
                            </div>
                        @endif

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4" x-data="{ confirmAction: null }">
                            <!-- Start Button -->
                            <div>
                                <button type="button" 
                                        @click="confirmAction = 'start'"
                                        @if($apiOffline) disabled @endif
                                        class="w-full inline-flex justify-center items-center px-4 py-3 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest transition ease-in-out duration-150
                                               {{ $apiOffline ? 'bg-gray-400 cursor-not-allowed' : 'bg-green-600 hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2' }}">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    {{ __('app.start') }}
                                </button>
                            </div>

                            <!-- Stop Button -->
                            <div>
                                <button type="button" 
                                        @click="confirmAction = 'stop'"
                                        @if($apiOffline) disabled @endif
                                        class="w-full inline-flex justify-center items-center px-4 py-3 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest transition ease-in-out duration-150
                                               {{ $apiOffline ? 'bg-gray-400 cursor-not-allowed' : 'bg-yellow-600 hover:bg-yellow-700 focus:bg-yellow-700 active:bg-yellow-900 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2' }}">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"/>
                                    </svg>
                                    {{ __('app.stop') }}
                                </button>
                            </div>

                            <!-- Restart Button -->
                            <div>
                                <button type="button" 
                                        @click="confirmAction = 'restart'"
                                        @if($apiOffline) disabled @endif
                                        class="w-full inline-flex justify-center items-center px-4 py-3 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest transition ease-in-out duration-150
                                               {{ $apiOffline ? 'bg-gray-400 cursor-not-allowed' : 'bg-blue-600 hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2' }}">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                    {{ __('app.restart') }}
                                </button>
                            </div>

                            <!-- Power Off Button -->
                            <div>
                                <button type="button" 
                                        @click="confirmAction = 'poweroff'"
                                        @if($apiOffline) disabled @endif
                                        class="w-full inline-flex justify-center items-center px-4 py-3 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest transition ease-in-out duration-150
                                               {{ $apiOffline ? 'bg-gray-400 cursor-not-allowed' : 'bg-red-600 hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2' }}">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                    </svg>
                                    {{ __('app.power_off') }}
                                </button>
                            </div>

                            <!-- Confirmation Modal -->
                            <div x-show="confirmAction" 
                                 x-cloak
                                 class="fixed inset-0 z-50 overflow-y-auto" 
                                 aria-labelledby="modal-title" 
                                 role="dialog" 
                                 aria-modal="true">
                                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                    <div x-show="confirmAction" 
                                         x-transition:enter="ease-out duration-300"
                                         x-transition:enter-start="opacity-0"
                                         x-transition:enter-end="opacity-100"
                                         x-transition:leave="ease-in duration-200"
                                         x-transition:leave-start="opacity-100"
                                         x-transition:leave-end="opacity-0"
                                         class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
                                         @click="confirmAction = null"></div>

                                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                                    <div x-show="confirmAction"
                                         x-transition:enter="ease-out duration-300"
                                         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                         x-transition:leave="ease-in duration-200"
                                         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                         class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                                        <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                            <div class="sm:flex sm:items-start">
                                                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100 dark:bg-yellow-900 sm:mx-0 sm:h-10 sm:w-10">
                                                    <svg class="h-6 w-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                                    </svg>
                                                </div>
                                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100" id="modal-title">
                                                        {{ __('app.confirm_action') }}
                                                    </h3>
                                                    <div class="mt-2">
                                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                                            {{ __('app.confirm_power_action') }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                            <form x-bind:action="'{{ route('vps.show', $natVps) }}/' + confirmAction" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" 
                                                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                                                    {{ __('app.confirm') }}
                                                </button>
                                            </form>
                                            <button type="button" 
                                                    @click="confirmAction = null"
                                                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                                {{ __('app.cancel') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Domain Forwardings Card -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg md:col-span-2">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('app.domain_forwarding') }}</h3>
                            <a href="{{ route('vps.domain-forwarding.index', $natVps) }}" 
                               class="inline-flex items-center px-3 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                {{ __('app.edit') }}
                            </a>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            {{ __('app.domain_forwarding_desc') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Console Modal Component --}}
    <x-console-modal :natVps="$natVps" :vncAvailable="!$apiOffline" :sshAvailable="!empty($natVps->ssh_username) && !empty($natVps->ssh_password)" />
</x-app-layout>
