<style>
    .text-xxs {
        font-size: 10pt !important;
    }

    @media only screen and (max-width: 767px) {
        .text-xxs {
            font-size: 8pt !important;
        }
    }
</style>

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <a href="{{ route('vps.index') }}"
                    class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    NAT VPS: {{ $natVps->hostname }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @php
                $cachedSpecs = $natVps->cached_specs;
                $status = $liveInfo?->status ?? ($cachedSpecs['status'] ?? null);
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- VPS Specifications Card -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">VPS Specifications
                            </h3>

                            @if ($apiOffline)
                                <span
                                    class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    API Offline
                                </span>
                            @endif
                        </div>
                        <dl class="space-y-3">
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500 dark:text-gray-400">Hostname</dt>
                                <dd class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $natVps->hostname }}
                                </dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500 dark:text-gray-400">VPS ID</dt>
                                <dd class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $natVps->vps_id }}
                                </dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500 dark:text-gray-400">UUID</dt>
                                <dd class="text-sm font-medium text-gray-900 dark:text-gray-100 font-mono text-xs">
                                    {{ $liveInfo?->uuid ?? ($cachedSpecs['uuid'] ?? 'N/A') }}
                                </dd>
                            </div>
                            @php
                                $osName = $liveInfo?->osName ?? ($cachedSpecs['os_name'] ?? null);
                                $osIcon = $liveInfo?->getOsIcon() ?? 'linux';
                            @endphp
                            <div class="flex justify-between items-center">
                                <dt class="text-sm text-gray-500 dark:text-gray-400">Operating System</dt>
                                <dd class="text-sm font-medium text-gray-900 dark:text-gray-100 flex items-center">
                                    @if ($osName)
                                        <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/{{ $osIcon }}/{{ $osIcon }}-original.svg"
                                            alt="{{ $osIcon }}" class="w-5 h-5 mr-2"
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
                                $isp = $serverLocation['isp'] ?? ($serverLocation['org'] ?? null);
                            @endphp
                            <div class="flex justify-between items-center">
                                <dt class="text-sm text-gray-500 dark:text-gray-400">Region</dt>
                                <dd class="text-sm font-medium text-gray-900 dark:text-gray-100 flex items-center">
                                    @if ($location)
                                        <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        {{ $location }}
                                    @else
                                        -
                                    @endif
                                </dd>
                            </div>
                            @if ($isp)
                                <div class="flex justify-between items-center">
                                    <dt class="text-sm text-gray-500 dark:text-gray-400">ISP</dt>
                                    <dd class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $isp }}</dd>
                                </div>
                            @endif
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500 dark:text-gray-400">CPU</dt>
                                <dd class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ $liveInfo?->cpu ?? ($cachedSpecs['cpu'] ?? '-') }} Core(s)
                                </dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500 dark:text-gray-400">RAM</dt>
                                <dd class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ $liveInfo?->ram ?? ($cachedSpecs['ram'] ?? null) ? ($liveInfo?->ram ?? $cachedSpecs['ram']) . ' MB' : '-' }}
                                </dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500 dark:text-gray-400">Disk</dt>
                                <dd class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ $liveInfo?->disk ?? ($cachedSpecs['disk'] ?? null) ? ($liveInfo?->disk ?? $cachedSpecs['disk']) . ' GB' : '-' }}
                                </dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500 dark:text-gray-400">Bandwidth</dt>
                                <dd class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                    @php
                                        $bandwidth = $liveInfo?->bandwidth ?? ($cachedSpecs['bandwidth'] ?? null);
                                        $usedBandwidth =
                                            $liveInfo?->usedBandwidth ?? ($cachedSpecs['used_bandwidth'] ?? null);
                                    @endphp
                                    @if ($bandwidth)
                                        {{ $usedBandwidth ?? 0 }} / {{ $bandwidth }} GB
                                    @else
                                        -
                                    @endif
                                </dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500 dark:text-gray-400">Status</dt>
                                <dd>
                                    @if ($status === 1)
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                            Running
                                        </span>
                                    @elseif($status === 0)
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                            Stopped
                                        </span>
                                    @else
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                            Unknown
                                        </span>
                                    @endif
                                </dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500 dark:text-gray-400">Server</dt>
                                <dd class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ $natVps->server?->name ?? 'No Server' }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- SSH Credentials Card -->
                <div>
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">SSH Credentials</h3>

                                <div class="flex items-center gap-1">
                                    <div>
                                        <button type="button" title="Console"
                                            @click="$dispatch('open-console', { vpsId: {{ $natVps->id }}, tab: 'vnc' })"
                                            class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700 focus:bg-emerald-700 active:bg-emerald-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                            </svg>
                                            {{-- Console --}}
                                        </button>
                                    </div>
                                    <div>
                                        <a href="{{ route('vps.edit', $natVps) }}" title="Edit"
                                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            {{-- Edit --}}
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <dl class="space-y-3">
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-500 dark:text-gray-400">Username</dt>
                                    <dd class="text-sm font-medium text-gray-900 dark:text-gray-100 font-mono">
                                        {{ $natVps->ssh_username ?? 'Not set' }}
                                    </dd>
                                </div>
                                <div class="flex justify-between items-center">
                                    <dt class="text-sm text-gray-500 dark:text-gray-400">Password</dt>
                                    <dd class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                        @if ($natVps->ssh_password)
                                            <span x-data="{ show: false }">
                                                <span x-show="!show" class="font-mono">••••••••</span>
                                                <span x-show="show"
                                                    class="font-mono">{{ $natVps->ssh_password }}</span>
                                                <button type="button" @click="show = !show"
                                                    class="ml-2 text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 text-xs">
                                                    <span x-show="!show">Show</span>
                                                    <span x-show="show">Hide</span>
                                                </button>
                                            </span>
                                        @else
                                            Not set
                                        @endif
                                    </dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-500 dark:text-gray-400">SSH Port</dt>
                                    <dd class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $natVps->ssh_port ?? 22 }}</dd>
                                </div>
                            </dl>

                            @if ($natVps->ssh_username && $natVps->server)
                                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">SSH Command:</p>
                                    <code
                                        class="block p-2 bg-gray-100 dark:bg-gray-700 rounded text-sm font-mono text-gray-800 dark:text-gray-200 break-all">
                                        ssh {{ $natVps->ssh_username . '@' . $natVps->server->ip_address }} -p
                                        {{ $natVps->ssh_port ?? 22 }}
                                    </code>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Resource Usage Card (loaded via AJAX) -->
                <x-resource-usage :apiEndpoint="route('vps.resource-usage', $natVps)" :apiOffline="$apiOffline" />

                <!-- Power Actions Card -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg md:col-span-2">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Power Actions</h3>

                        @if ($apiOffline)
                            <div class="p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg mb-4">
                                <p class="text-sm text-yellow-700 dark:text-yellow-300">
                                    Power actions are disabled while the API is offline.
                                </p>
                            </div>
                        @endif

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4" x-data="{ confirmAction: null }">
                            <!-- Start Button -->
                            <div>
                                <button type="button" @click="confirmAction = 'start'"
                                    @if ($apiOffline) disabled @endif
                                    class="w-full inline-flex justify-center items-center px-0 py-3 border border-transparent rounded-md font-semibold text-xxs text-white uppercase tracking-widest transition ease-in-out duration-150
                                               {{ $apiOffline ? 'bg-gray-400 cursor-not-allowed' : 'bg-green-600 hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2' }}">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Start
                                </button>
                            </div>

                            <!-- Stop Button -->
                            <div>
                                <button type="button" @click="confirmAction = 'stop'"
                                    @if ($apiOffline) disabled @endif
                                    class="w-full inline-flex justify-center items-center px-0 py-3 border border-transparent rounded-md font-semibold text-xxs text-white uppercase tracking-widest transition ease-in-out duration-150
                                               {{ $apiOffline ? 'bg-gray-400 cursor-not-allowed' : 'bg-yellow-600 hover:bg-yellow-700 focus:bg-yellow-700 active:bg-yellow-900 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2' }}">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z" />
                                    </svg>
                                    Stop
                                </button>
                            </div>

                            <!-- Restart Button -->
                            <div>
                                <button type="button" @click="confirmAction = 'restart'"
                                    @if ($apiOffline) disabled @endif
                                    class="w-full inline-flex justify-center items-center px-0 py-3 border border-transparent rounded-md font-semibold text-xxs text-white uppercase tracking-widest transition ease-in-out duration-150
                                               {{ $apiOffline ? 'bg-gray-400 cursor-not-allowed' : 'bg-blue-600 hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2' }}">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                    Restart
                                </button>
                            </div>

                            <!-- Power Off Button -->
                            <div>
                                <button type="button" @click="confirmAction = 'poweroff'"
                                    @if ($apiOffline) disabled @endif
                                    class="w-full inline-flex justify-center items-center px-0 py-3 border border-transparent rounded-md font-semibold text-xxs text-white uppercase tracking-widest transition ease-in-out duration-150
                                               {{ $apiOffline ? 'bg-gray-400 cursor-not-allowed' : 'bg-red-600 hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2' }}">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                    </svg>
                                    Power Off
                                </button>
                            </div>

                            <!-- Confirmation Modal -->
                            <div x-show="confirmAction" x-cloak class="fixed inset-0 z-50 overflow-y-auto"
                                aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                <div
                                    class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                    <div x-show="confirmAction" x-transition:enter="ease-out duration-300"
                                        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                                        x-transition:leave="ease-in duration-200"
                                        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                                        class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                                        @click="confirmAction = null"></div>

                                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen"
                                        aria-hidden="true">&#8203;</span>

                                    <div x-show="confirmAction" x-transition:enter="ease-out duration-300"
                                        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                        x-transition:leave="ease-in duration-200"
                                        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                        class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                                        <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                            <div class="sm:flex sm:items-start">
                                                <div
                                                    class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100 dark:bg-yellow-900 sm:mx-0 sm:h-10 sm:w-10">
                                                    <svg class="h-6 w-6 text-yellow-600 dark:text-yellow-400"
                                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                    </svg>
                                                </div>
                                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100"
                                                        id="modal-title">
                                                        Confirm Action
                                                    </h3>
                                                    <div class="mt-2">
                                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                                            Are you sure you want to <span x-text="confirmAction"
                                                                class="font-semibold"></span> this VPS?
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div
                                            class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                            <form x-bind:action="'/vps/{{ $natVps->id }}/' + confirmAction"
                                                method="POST" class="inline">
                                                @csrf
                                                <button type="submit"
                                                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                                                    Confirm
                                                </button>
                                            </form>
                                            <button type="button" @click="confirmAction = null"
                                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                                Cancel
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
                            <div class="flex items-center">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Domain Forwardings
                                </h3>
                                <span
                                    class="ml-2 px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                    {{ $vdfCount ?? 0 }} rules
                                </span>
                            </div>
                            <a href="{{ route('vps.domain-forwarding.index', $natVps) }}"
                                class="inline-flex items-center px-3 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                Manage
                            </a>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Configure port forwarding (TCP) and domain forwarding (HTTP/HTTPS) rules for this VPS.
                        </p>
                    </div>
                </div>

                <!-- User Assignment Card -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg md:col-span-2">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">User Assignment</h3>

                        @if ($natVps->user)
                            <div
                                class="flex items-center justify-between p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <svg class="h-10 w-10 text-green-600 dark:text-green-400" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $natVps->user->name }}</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $natVps->user->email }}
                                        </p>
                                    </div>
                                </div>
                                <form action="{{ route('vps.unassign', $natVps) }}" method="POST"
                                    onsubmit="return confirm('Are you sure you want to remove this user assignment?')">
                                    @csrf
                                    <button type="submit"
                                        class="inline-flex items-center px-3 py-2 border border-red-300 dark:border-red-600 rounded-md text-sm font-medium text-red-700 dark:text-red-400 bg-white dark:bg-gray-800 hover:bg-red-50 dark:hover:bg-gray-700">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 7a4 4 0 11-8 0 4 4 0 018 0zM9 14a6 6 0 00-6 6v1h12v-1a6 6 0 00-6-6zM21 12h-6" />
                                        </svg>
                                        Remove Assignment
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">This VPS is not assigned to
                                    any user. Only administrators can access it.</p>
                                <form action="{{ route('vps.assign', $natVps) }}" method="POST"
                                    class="flex items-end space-x-4">
                                    @csrf
                                    <div class="flex-1">
                                        <label for="user_id"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Select
                                            User</label>
                                        <select name="user_id" id="user_id" required
                                            class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                            <option value="">Choose a user...</option>
                                            @foreach (\App\Models\User::where('role', \App\Enums\UserRole::User)->orderBy('name')->get() as $user)
                                                <option value="{{ $user->id }}">{{ $user->name }}
                                                    ({{ $user->email }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <button type="submit"
                                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                        </svg>
                                        Assign User
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Console Modal Component - Requirements: 1.1, 1.4, 4.1, 5.2 --}}
    <x-console-modal :natVps="$natVps" :vncAvailable="!$apiOffline" :sshAvailable="!empty($natVps->ssh_username) && !empty($natVps->ssh_password)" />
</x-app-layout>
