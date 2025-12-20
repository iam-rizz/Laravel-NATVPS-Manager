<x-app-layout>
    <x-slot name="header">
        {{ __('app.nat_vps') }}
    </x-slot>

    <div class="space-y-6">
        <!-- Page Header with Actions -->
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-display font-bold text-surface-900 dark:text-white">
                    NAT VPS Instances
                </h1>
                <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                    {{ __('app.manage_nat_vps_desc') ?? 'Manage all NAT VPS instances across your servers' }}
                </p>
            </div>
            <div class="flex flex-col sm:flex-row gap-2">
                <a href="{{ route('admin.nat-vps.import') }}" 
                   class="inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium rounded-lg bg-green-600 text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-150">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    {{ __('app.import_from_virtualizor') ?? 'Import from Virtualizor' }}
                </a>
                <a href="{{ route('admin.nat-vps.create') }}" 
                   class="inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium rounded-lg bg-primary-600 text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all duration-150">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    {{ __('app.add_nat_vps') ?? 'Add NAT VPS' }}
                </a>
            </div>
        </div>

        <!-- Content Card -->
        <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 shadow-sm">
            <div class="p-6">
                @if($natVpsList->isEmpty())
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-surface-400 dark:text-surface-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-surface-900 dark:text-surface-100">{{ __('app.no_nat_vps') ?? 'No NAT VPS instances' }}</h3>
                            <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">{{ __('app.no_nat_vps_desc') ?? 'Get started by adding a new NAT VPS.' }}</p>
                            <div class="mt-6">
                                <a href="{{ route('admin.nat-vps.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium rounded-lg bg-primary-600 text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all duration-150">
                                    {{ __('app.add_nat_vps') ?? 'Add NAT VPS' }}
                                </a>
                            </div>
                        </div>
                    @else
                        <!-- Desktop Table View -->
                        <div class="hidden md:block overflow-x-auto">
                            <table class="min-w-full divide-y divide-surface-200 dark:divide-surface-700">
                                <thead class="bg-surface-50 dark:bg-surface-800">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-surface-500 dark:text-surface-400 uppercase tracking-wider">{{ __('app.hostname') }}</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-surface-500 dark:text-surface-400 uppercase tracking-wider">VPS ID</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-surface-500 dark:text-surface-400 uppercase tracking-wider">{{ __('app.server') ?? 'Server' }}</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-surface-500 dark:text-surface-400 uppercase tracking-wider">{{ __('app.assigned_user') ?? 'Assigned User' }}</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-surface-500 dark:text-surface-400 uppercase tracking-wider">SSH Port</th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-surface-500 dark:text-surface-400 uppercase tracking-wider">{{ __('app.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-surface-800 divide-y divide-surface-200 dark:divide-surface-700">
                                    @foreach($natVpsList as $natVps)
                                        <tr class="hover:bg-surface-50 dark:hover:bg-surface-700/50 transition-colors">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-surface-900 dark:text-surface-100">{{ $natVps->hostname }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-surface-500 dark:text-surface-400">{{ $natVps->vps_id }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-surface-500 dark:text-surface-400">
                                                    {{ $natVps->server ? $natVps->server->name : 'No Server' }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($natVps->user)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300">
                                                        {{ $natVps->user->name }}
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-surface-100 dark:bg-surface-700 text-surface-600 dark:text-surface-400">
                                                        {{ __('app.unassigned') ?? 'Unassigned' }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-surface-500 dark:text-surface-400">{{ $natVps->ssh_port ?? 22 }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <div class="flex justify-end gap-2">
                                                    <a href="{{ route('admin.nat-vps.show', $natVps) }}" class="p-2 text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors" title="{{ __('app.view') }}">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                        </svg>
                                                    </a>
                                                    <a href="{{ route('admin.nat-vps.edit', $natVps) }}" class="p-2 text-primary-600 hover:text-primary-800 dark:text-primary-400 dark:hover:text-primary-300 hover:bg-primary-50 dark:hover:bg-primary-900/20 rounded-lg transition-colors" title="{{ __('app.edit') }}">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                        </svg>
                                                    </a>
                                                    <form action="{{ route('admin.nat-vps.destroy', $natVps) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('app.confirm_delete') }}')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="p-2 text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors" title="{{ __('app.delete') }}">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Mobile Card View -->
                        <div class="md:hidden space-y-4">
                            @foreach($natVpsList as $natVps)
                                <div class="bg-surface-50 dark:bg-surface-700/50 rounded-xl p-4 border border-surface-200 dark:border-surface-600">
                                    <div class="flex justify-between items-start mb-3">
                                        <div>
                                            <h3 class="text-sm font-medium text-surface-900 dark:text-surface-100">{{ $natVps->hostname }}</h3>
                                            <p class="text-sm text-surface-500 dark:text-surface-400">VPS ID: {{ $natVps->vps_id }}</p>
                                        </div>
                                        @if($natVps->user)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300">
                                                {{ $natVps->user->name }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-surface-100 dark:bg-surface-600 text-surface-600 dark:text-surface-300">
                                                {{ __('app.unassigned') ?? 'Unassigned' }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-sm text-surface-500 dark:text-surface-400 mb-3">
                                        <span>{{ __('app.server') ?? 'Server' }}: {{ $natVps->server ? $natVps->server->name : 'None' }}</span>
                                        <span class="mx-2">•</span>
                                        <span>SSH Port: {{ $natVps->ssh_port ?? 22 }}</span>
                                    </div>
                                    <div class="flex gap-2">
                                        <a href="{{ route('admin.nat-vps.show', $natVps) }}" 
                                           class="flex-1 inline-flex justify-center items-center px-3 py-2 text-sm font-medium rounded-lg border border-blue-300 dark:border-blue-600 text-blue-700 dark:text-blue-400 bg-white dark:bg-surface-800 hover:bg-blue-50 dark:hover:bg-surface-700 transition-colors">
                                            {{ __('app.view') }}
                                        </a>
                                        <a href="{{ route('admin.nat-vps.edit', $natVps) }}" 
                                           class="flex-1 inline-flex justify-center items-center px-3 py-2 text-sm font-medium rounded-lg border border-surface-300 dark:border-surface-600 text-surface-700 dark:text-surface-300 bg-white dark:bg-surface-800 hover:bg-surface-50 dark:hover:bg-surface-700 transition-colors">
                                            {{ __('app.edit') }}
                                        </a>
                                        <form action="{{ route('admin.nat-vps.destroy', $natVps) }}" method="POST" class="flex-1" onsubmit="return confirm('{{ __('app.confirm_delete') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-full inline-flex justify-center items-center px-3 py-2 text-sm font-medium rounded-lg border border-red-300 dark:border-red-600 text-red-700 dark:text-red-400 bg-white dark:bg-surface-800 hover:bg-red-50 dark:hover:bg-surface-700 transition-colors">
                                                {{ __('app.delete') }}
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
</x-app-layout>
