<x-app-layout>
    <x-slot name="header">
        {{ __('app.console') }}
    </x-slot>

    <div class="card">
        <div class="card-header">
            <h3 class="text-lg font-display font-semibold text-surface-900 dark:text-white">
                {{ __('app.select_vps_to_connect') }}
            </h3>
            <p class="text-sm text-surface-500 dark:text-surface-400 mt-1">
                {{ __('app.select_vps_description') }}
            </p>
        </div>
        <div class="card-body">
            @if($vpsList->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    @foreach($vpsList as $vps)
                        <a href="{{ route('console.show', $vps) }}" 
                           class="block p-4 bg-surface-50 dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 hover:border-primary-300 dark:hover:border-primary-700 hover:shadow-soft transition-all duration-200 group">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex items-center space-x-2">
                                    <span class="w-2 h-2 rounded-full bg-green-500"></span>
                                    <h4 class="font-medium text-surface-900 dark:text-white group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
                                        {{ $vps->hostname }}
                                    </h4>
                                </div>
                                <svg class="w-4 h-4 text-surface-400 group-hover:text-primary-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>
                            <div class="space-y-1 text-sm">
                                <p class="text-surface-500 dark:text-surface-400">
                                    <span class="text-surface-400 dark:text-surface-500">{{ __('app.server') }}:</span>
                                    {{ $vps->server?->name ?? '-' }}
                                </p>
                                @if(auth()->user()->isAdmin())
                                <p class="text-surface-500 dark:text-surface-400">
                                    <span class="text-surface-400 dark:text-surface-500">{{ __('app.owner') }}:</span>
                                    {{ $vps->user?->name ?? __('app.unassigned') }}
                                </p>
                                @endif
                                <p class="text-surface-500 dark:text-surface-400">
                                    <span class="text-surface-400 dark:text-surface-500">VPS ID:</span>
                                    {{ $vps->vps_id }}
                                </p>
                            </div>
                            <div class="flex items-center space-x-2 mt-3 pt-3 border-t border-surface-200 dark:border-surface-700">
                                <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400 rounded">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    VNC
                                </span>
                                @if($vps->ssh_username && $vps->ssh_password)
                                <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    SSH
                                </span>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="w-16 h-16 text-surface-300 dark:text-surface-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <h3 class="text-lg font-medium text-surface-900 dark:text-white mb-2">{{ __('app.no_vps_available') }}</h3>
                    <p class="text-surface-500 dark:text-surface-400">{{ __('app.no_vps_assigned') }}</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
