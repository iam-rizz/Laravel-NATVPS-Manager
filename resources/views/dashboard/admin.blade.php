<x-app-layout>
    <x-slot name="header">
        {{ __('app.admin_dashboard') }}
    </x-slot>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-8">
        <!-- Total Servers -->
        <div class="stat-card">
            <div class="flex items-center gap-4">
                <div class="stat-card-icon bg-gradient-to-br from-indigo-500 to-indigo-600 shadow-lg shadow-indigo-500/25">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/>
                    </svg>
                </div>
                <div>
                    <p class="stat-card-label">{{ __('app.total_servers') }}</p>
                    <div class="flex items-baseline gap-2">
                        <p class="stat-card-value">{{ $totalServers }}</p>
                        <span class="text-sm text-surface-500 dark:text-surface-400">({{ $activeServers }} {{ __('app.active') }})</span>
                    </div>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-surface-200 dark:border-surface-700">
                <a href="{{ route('servers.index') }}" class="text-sm font-medium text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 inline-flex items-center gap-1">
                    {{ __('app.view_all') }}
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>

        <!-- Total VPS -->
        <div class="stat-card">
            <div class="flex items-center gap-4">
                <div class="stat-card-icon bg-gradient-to-br from-emerald-500 to-emerald-600 shadow-lg shadow-emerald-500/25">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
                    </svg>
                </div>
                <div>
                    <p class="stat-card-label">{{ __('app.total_vps') }}</p>
                    <div class="flex items-baseline gap-2">
                        <p class="stat-card-value">{{ $totalNatVps }}</p>
                        <span class="text-sm text-surface-500 dark:text-surface-400">({{ $assignedVpsCount }} {{ __('app.assigned') }})</span>
                    </div>
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

        <!-- Total Users -->
        <div class="stat-card">
            <div class="flex items-center gap-4">
                <div class="stat-card-icon bg-gradient-to-br from-blue-500 to-blue-600 shadow-lg shadow-blue-500/25">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="stat-card-label">{{ __('app.total_users') }}</p>
                    <p class="stat-card-value">{{ $totalUsers }}</p>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-surface-200 dark:border-surface-700">
                <a href="{{ route('users.index') }}" class="text-sm font-medium text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 inline-flex items-center gap-1">
                    {{ __('app.view_all') }}
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>

        <!-- Unassigned VPS -->
        <div class="stat-card">
            <div class="flex items-center gap-4">
                <div class="stat-card-icon bg-gradient-to-br from-amber-500 to-amber-600 shadow-lg shadow-amber-500/25">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div>
                    <p class="stat-card-label">{{ __('app.unassigned_vps') }}</p>
                    <p class="stat-card-value">{{ $unassignedVpsCount }}</p>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-surface-200 dark:border-surface-700">
                <span class="text-sm text-surface-500 dark:text-surface-400">{{ __('app.awaiting_assignment') }}</span>
            </div>
        </div>
    </div>

    <!-- Server Issues -->
    @if(count($serversWithIssues) > 0)
    <div class="card mb-8">
        <div class="card-header">
            <h3 class="text-lg font-display font-semibold text-surface-900 dark:text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                {{ __('app.server_issues') }}
            </h3>
        </div>
        <div class="card-body space-y-3">
            @foreach($serversWithIssues as $issue)
            <div class="flex items-center justify-between p-4 rounded-xl {{ $issue['severity'] === 'error' ? 'bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800' : 'bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800' }}">
                <div class="flex items-center gap-3">
                    <span class="flex-shrink-0 w-2.5 h-2.5 {{ $issue['severity'] === 'error' ? 'bg-red-500' : 'bg-amber-500' }} rounded-full"></span>
                    <div>
                        <p class="font-medium text-surface-900 dark:text-white">{{ $issue['server']->name }}</p>
                        <p class="text-sm text-surface-600 dark:text-surface-400">{{ $issue['issue'] }}</p>
                    </div>
                </div>
                <a href="{{ route('servers.edit', $issue['server']) }}" class="btn btn-sm btn-ghost">
                    {{ __('app.view') }}
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Recent Activity -->
    <div class="card">
        <div class="card-header">
            <h3 class="text-lg font-display font-semibold text-surface-900 dark:text-white">{{ __('app.recent_activity') }}</h3>
        </div>
        <div class="card-body">
            @if(count($recentActivity) > 0)
            <div class="flow-root">
                <ul role="list" class="-mb-8">
                    @foreach($recentActivity as $index => $activity)
                    <li>
                        <div class="relative pb-8">
                            @if($index < count($recentActivity) - 1)
                            <span class="absolute top-5 left-5 -ml-px h-full w-0.5 bg-surface-200 dark:bg-surface-700"></span>
                            @endif
                            <div class="relative flex items-start space-x-4">
                                <div>
                                    <span class="h-10 w-10 rounded-xl {{ $activity['type'] === 'vps_created' ? 'bg-gradient-to-br from-emerald-500 to-emerald-600' : ($activity['type'] === 'user_created' ? 'bg-gradient-to-br from-blue-500 to-blue-600' : 'bg-gradient-to-br from-indigo-500 to-indigo-600') }} flex items-center justify-center shadow-lg">
                                        <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            @if($activity['type'] === 'vps_created')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
                                            @elseif($activity['type'] === 'user_created')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            @else
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                            @endif
                                        </svg>
                                    </span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-surface-900 dark:text-white">{{ $activity['message'] }}</p>
                                    @if($activity['details'])
                                    <p class="text-sm text-surface-500 dark:text-surface-400">{{ $activity['details'] }}</p>
                                    @endif
                                </div>
                                <div class="flex-shrink-0 text-sm text-surface-500 dark:text-surface-400">
                                    {{ $activity['timestamp']->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>
            @else
            <div class="text-center py-8">
                <svg class="mx-auto h-12 w-12 text-surface-400 dark:text-surface-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="mt-2 text-sm text-surface-500 dark:text-surface-400">{{ __('app.no_recent_activity') }}</p>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
