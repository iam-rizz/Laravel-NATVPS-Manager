<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight" style="font-size: 16px;">
            {{ __('app.my_vps') }}
        </h2>
    </x-slot>

    <div class="space-y-6">
        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-display font-bold text-surface-900 dark:text-white">
                    {{ __('app.my_vps') }}
                </h1>
                <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                    {{ __('app.my_vps_desc') }}
                </p>
            </div>
        </div>

        <!-- Content Card -->
        <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 shadow-sm">
            <div class="p-6">
                @if(empty($vpsWithSpecs))
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-surface-400 dark:text-surface-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-surface-900 dark:text-surface-100">{{ __('app.no_data') }}</h3>
                        <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">{{ __('app.no_vps_assigned') }}</p>
                    </div>
                @else
                    <!-- Desktop Table View -->
                    <div class="hidden md:block overflow-x-auto">
                        <table class="min-w-full divide-y divide-surface-200 dark:divide-surface-700">
                            <thead class="bg-surface-50 dark:bg-surface-800">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-surface-500 dark:text-surface-400 uppercase tracking-wider">{{ __('app.hostname') }}</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-surface-500 dark:text-surface-400 uppercase tracking-wider">{{ __('app.vps_id') }}</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-surface-500 dark:text-surface-400 uppercase tracking-wider">{{ __('app.cpu') }}</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-surface-500 dark:text-surface-400 uppercase tracking-wider">{{ __('app.ram') }}</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-surface-500 dark:text-surface-400 uppercase tracking-wider">{{ __('app.disk') }}</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-surface-500 dark:text-surface-400 uppercase tracking-wider">{{ __('app.bandwidth') }}</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-surface-500 dark:text-surface-400 uppercase tracking-wider">{{ __('app.status') }}</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-surface-500 dark:text-surface-400 uppercase tracking-wider">{{ __('app.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-surface-800 divide-y divide-surface-200 dark:divide-surface-700">
                                @foreach($vpsWithSpecs as $vpsData)
                                    @php
                                        $natVps = $vpsData['natVps'];
                                        $liveInfo = $vpsData['liveInfo'];
                                        $apiOffline = $vpsData['apiOffline'];
                                        $cachedSpecs = $natVps->cached_specs;
                                    @endphp
                                    <tr class="hover:bg-surface-50 dark:hover:bg-surface-700/50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="text-sm font-medium text-surface-900 dark:text-surface-100">{{ $natVps->hostname }}</div>
                                                @if($apiOffline)
                                                    <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200" title="{{ __('app.api_offline') }}">
                                                        Cached
                                                    </span>
                                                @endif
                                            </div>
                                            @if($liveInfo && $liveInfo->uuid)
                                                <div class="text-xs text-surface-500 dark:text-surface-400 font-mono">{{ $liveInfo->uuid }}</div>
                                            @elseif($cachedSpecs && isset($cachedSpecs['uuid']))
                                                <div class="text-xs text-surface-500 dark:text-surface-400 font-mono">{{ $cachedSpecs['uuid'] }}</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-surface-500 dark:text-surface-400">{{ $natVps->vps_id }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-surface-500 dark:text-surface-400">
                                                {{ $liveInfo?->cpu ?? $cachedSpecs['cpu'] ?? '-' }} {{ ($liveInfo?->cpu ?? $cachedSpecs['cpu'] ?? null) ? 'Core(s)' : '' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-surface-500 dark:text-surface-400">
                                                @php $ram = $liveInfo?->ram ?? $cachedSpecs['ram'] ?? null; @endphp
                                                {{ $ram ? $ram . ' MB' : '-' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-surface-500 dark:text-surface-400">
                                                @php $disk = $liveInfo?->disk ?? $cachedSpecs['disk'] ?? null; @endphp
                                                {{ $disk ? $disk . ' GB' : '-' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-surface-500 dark:text-surface-400">
                                                @php
                                                    $bandwidth = $liveInfo?->bandwidth ?? $cachedSpecs['bandwidth'] ?? null;
                                                    $usedBandwidth = $liveInfo?->usedBandwidth ?? $cachedSpecs['used_bandwidth'] ?? null;
                                                @endphp
                                                @if($bandwidth)
                                                    {{ $usedBandwidth ?? 0 }} / {{ $bandwidth }} GB
                                                @else
                                                    -
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php $status = $liveInfo?->status ?? $cachedSpecs['status'] ?? null; @endphp
                                            @if($status === 1)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300">
                                                    {{ __('app.running') }}
                                                </span>
                                            @elseif($status === 0)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300">
                                                    {{ __('app.stopped') }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-surface-100 dark:bg-surface-700 text-surface-600 dark:text-surface-400">
                                                    {{ __('app.unknown') }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex justify-end gap-2">
                                                <a href="{{ route('vps.show', $natVps) }}" class="p-2 text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors" title="{{ __('app.view') }}">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                    </svg>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Card View -->
                    <div class="md:hidden space-y-4">
                        @foreach($vpsWithSpecs as $vpsData)
                            @php
                                $natVps = $vpsData['natVps'];
                                $liveInfo = $vpsData['liveInfo'];
                                $apiOffline = $vpsData['apiOffline'];
                                $cachedSpecs = $natVps->cached_specs;
                                $status = $liveInfo?->status ?? $cachedSpecs['status'] ?? null;
                            @endphp
                            <div class="bg-surface-50 dark:bg-surface-700/50 rounded-xl p-4 border border-surface-200 dark:border-surface-600">
                                <div class="flex justify-between items-start mb-3">
                                    <div>
                                        <h3 class="text-sm font-medium text-surface-900 dark:text-surface-100">{{ $natVps->hostname }}</h3>
                                        <p class="text-sm text-surface-500 dark:text-surface-400">{{ __('app.vps_id') }}: {{ $natVps->vps_id }}</p>
                                        @if($liveInfo && $liveInfo->uuid)
                                            <p class="text-xs text-surface-500 dark:text-surface-400 font-mono">{{ $liveInfo->uuid }}</p>
                                        @endif
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        @if($apiOffline)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300">
                                                Cached
                                            </span>
                                        @endif
                                        @if($status === 1)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300">
                                                {{ __('app.running') }}
                                            </span>
                                        @elseif($status === 0)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300">
                                                {{ __('app.stopped') }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-surface-100 dark:bg-surface-600 text-surface-600 dark:text-surface-300">
                                                {{ __('app.unknown') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-2 text-sm text-surface-500 dark:text-surface-400 mb-3">
                                    <div>{{ __('app.cpu') }}: {{ $liveInfo?->cpu ?? $cachedSpecs['cpu'] ?? '-' }} Core(s)</div>
                                    <div>{{ __('app.ram') }}: {{ ($liveInfo?->ram ?? $cachedSpecs['ram'] ?? null) ? ($liveInfo?->ram ?? $cachedSpecs['ram']) . ' MB' : '-' }}</div>
                                    <div>{{ __('app.disk') }}: {{ ($liveInfo?->disk ?? $cachedSpecs['disk'] ?? null) ? ($liveInfo?->disk ?? $cachedSpecs['disk']) . ' GB' : '-' }}</div>
                                    <div>BW: {{ ($liveInfo?->bandwidth ?? $cachedSpecs['bandwidth'] ?? null) ? (($liveInfo?->usedBandwidth ?? $cachedSpecs['used_bandwidth'] ?? 0) . '/' . ($liveInfo?->bandwidth ?? $cachedSpecs['bandwidth']) . ' GB') : '-' }}</div>
                                </div>
                                <a href="{{ route('vps.show', $natVps) }}" 
                                   class="w-full inline-flex justify-center items-center px-4 py-2.5 text-sm font-medium rounded-lg bg-primary-600 text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all duration-150">
                                    {{ __('app.vps_details') }}
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
