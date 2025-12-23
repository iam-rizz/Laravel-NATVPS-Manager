<x-app-layout>
    <x-slot name="header">
        {{ __('app.dashboard') }}
    </x-slot>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6 mb-8">
        <!-- Total VPS -->
        <div class="stat-card">
            <div class="flex items-center gap-4">
                <div class="stat-card-icon bg-gradient-to-br from-emerald-500 to-emerald-600 shadow-lg shadow-emerald-500/25">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
                    </svg>
                </div>
                <div>
                    <p class="stat-card-label">{{ __('app.your_vps_instances') }}</p>
                    <p class="stat-card-value">{{ $assignedVpsCount }}</p>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-surface-200 dark:border-surface-700">
                <a href="{{ route('vps.index') }}" class="text-sm font-medium text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 inline-flex items-center gap-1">
                    {{ __('app.view_all') }}
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>

        <!-- Console Access -->
        <div class="stat-card">
            <div class="flex items-center gap-4">
                <div class="stat-card-icon bg-gradient-to-br from-indigo-500 to-indigo-600 shadow-lg shadow-indigo-500/25">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <p class="stat-card-label">{{ __('app.console') }}</p>
                    <p class="stat-card-value text-lg">VNC & SSH</p>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-surface-200 dark:border-surface-700">
                <a href="{{ route('console.index') }}" class="text-sm font-medium text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 inline-flex items-center gap-1">
                    {{ __('app.open_console') }}
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>

    <!-- Quick Access Links -->
    @if($assignedVps->count() > 0)
        <div class="card">
            <div class="card-header flex justify-between items-center">
                <h3 class="text-lg font-display font-semibold text-surface-900 dark:text-white">{{ __('app.quick_access') }}</h3>
                <a href="{{ route('vps.index') }}" class="text-sm font-medium text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 inline-flex items-center gap-1">
                    {{ __('app.view_all') }}
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
            <div class="card-body">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($assignedVps as $vps)
                        <div class="block p-4 bg-surface-50 dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 hover:border-primary-300 dark:hover:border-primary-700 hover:shadow-soft transition-all duration-200">
                            <div class="flex items-center justify-between mb-3">
                                <div>
                                    <h4 class="font-medium text-surface-900 dark:text-white">{{ $vps->hostname }}</h4>
                                    <p class="text-sm text-surface-500 dark:text-surface-400">
                                        {{ __('app.vps_id') }}: {{ $vps->vps_id }}
                                    </p>
                                    @if($vps->server)
                                        <p class="text-xs text-surface-400 dark:text-surface-500 mt-1">
                                            {{ $vps->server->name }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <a href="{{ route('vps.show', $vps) }}" 
                                   class="flex-1 inline-flex justify-center items-center px-3 py-2 text-sm font-medium rounded-lg bg-primary-600 text-white hover:bg-primary-700 transition-colors">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    {{ __('app.view') }}
                                </a>
                                <a href="{{ route('console.show', $vps) }}" 
                                   class="flex-1 inline-flex justify-center items-center px-3 py-2 text-sm font-medium rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 transition-colors">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    {{ __('app.console') }}
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-body text-center py-12">
                <div class="w-16 h-16 mx-auto bg-surface-100 dark:bg-surface-800 rounded-2xl flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-surface-400 dark:text-surface-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-surface-900 dark:text-white mb-2">{{ __('app.no_data') }}</h3>
                <p class="text-surface-500 dark:text-surface-400 mb-2">{{ __('app.no_vps_assigned_desc') }}</p>
                <p class="text-surface-500 dark:text-surface-400">{{ __('app.contact_admin') }}</p>
            </div>
        </div>
    @endif
</x-app-layout>
