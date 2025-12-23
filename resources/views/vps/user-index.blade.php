<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight" style="font-size: 16px;">
            {{ __('app.my_vps') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if(empty($vpsWithSpecs))
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">{{ __('app.no_data') }}</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('app.no_vps_assigned') }}</p>
                        </div>
                    @else
                        <!-- Desktop Table View -->
                        <div class="hidden md:block overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('app.hostname') }}</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('app.vps_id') }}</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('app.cpu') }}</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('app.ram') }}</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('app.disk') }}</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('app.bandwidth') }}</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('app.status') }}</th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('app.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($vpsWithSpecs as $vpsData)
                                        @php
                                            $natVps = $vpsData['natVps'];
                                            $liveInfo = $vpsData['liveInfo'];
                                            $apiOffline = $vpsData['apiOffline'];
                                            $cachedSpecs = $natVps->cached_specs;
                                        @endphp
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $natVps->hostname }}</div>
                                                    @if($apiOffline)
                                                        <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200" title="{{ __('app.api_offline') }}">
                                                            Cached
                                                        </span>
                                                    @endif
                                                </div>
                                                @if($liveInfo && $liveInfo->uuid)
                                                    <div class="text-xs text-gray-500 dark:text-gray-400 font-mono">{{ $liveInfo->uuid }}</div>
                                                @elseif($cachedSpecs && isset($cachedSpecs['uuid']))
                                                    <div class="text-xs text-gray-500 dark:text-gray-400 font-mono">{{ $cachedSpecs['uuid'] }}</div>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $natVps->vps_id }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $liveInfo?->cpu ?? $cachedSpecs['cpu'] ?? '-' }} {{ ($liveInfo?->cpu ?? $cachedSpecs['cpu'] ?? null) ? 'Core(s)' : '' }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                                    @php
                                                        $ram = $liveInfo?->ram ?? $cachedSpecs['ram'] ?? null;
                                                    @endphp
                                                    {{ $ram ? $ram . ' MB' : '-' }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                                    @php
                                                        $disk = $liveInfo?->disk ?? $cachedSpecs['disk'] ?? null;
                                                    @endphp
                                                    {{ $disk ? $disk . ' GB' : '-' }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-500 dark:text-gray-400">
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
                                                @php
                                                    $status = $liveInfo?->status ?? $cachedSpecs['status'] ?? null;
                                                @endphp
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
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <a href="{{ route('vps.show', $natVps) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                                    {{ __('app.vps_details') }}
                                                </a>
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
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                    <div class="flex justify-between items-start mb-3">
                                        <div>
                                            <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $natVps->hostname }}</h3>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('app.vps_id') }}: {{ $natVps->vps_id }}</p>
                                            @if($liveInfo && $liveInfo->uuid)
                                                <p class="text-xs text-gray-500 dark:text-gray-400 font-mono">{{ $liveInfo->uuid }}</p>
                                            @endif
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            @if($apiOffline)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                                    Cached
                                                </span>
                                            @endif
                                            @if($status === 1)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                    {{ __('app.running') }}
                                                </span>
                                            @elseif($status === 0)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                                    {{ __('app.stopped') }}
                                                </span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-300">
                                                    {{ __('app.unknown') }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-2 text-sm text-gray-500 dark:text-gray-400 mb-3">
                                        <div>{{ __('app.cpu') }}: {{ $liveInfo?->cpu ?? $cachedSpecs['cpu'] ?? '-' }} Core(s)</div>
                                        <div>{{ __('app.ram') }}: {{ ($liveInfo?->ram ?? $cachedSpecs['ram'] ?? null) ? ($liveInfo?->ram ?? $cachedSpecs['ram']) . ' MB' : '-' }}</div>
                                        <div>{{ __('app.disk') }}: {{ ($liveInfo?->disk ?? $cachedSpecs['disk'] ?? null) ? ($liveInfo?->disk ?? $cachedSpecs['disk']) . ' GB' : '-' }}</div>
                                        <div>BW: {{ ($liveInfo?->bandwidth ?? $cachedSpecs['bandwidth'] ?? null) ? (($liveInfo?->usedBandwidth ?? $cachedSpecs['used_bandwidth'] ?? 0) . '/' . ($liveInfo?->bandwidth ?? $cachedSpecs['bandwidth']) . ' GB') : '-' }}</div>
                                    </div>
                                    <a href="{{ route('vps.show', $natVps) }}" 
                                       class="w-full inline-flex justify-center items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                        {{ __('app.vps_details') }}
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
