<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('app.audit_logs') }}
            </h2>
            <a href="{{ route('audit-logs.export', request()->query()) }}" 
               class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                {{ __('app.audit_export') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filter Form -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="GET" action="{{ route('audit-logs.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <!-- User Filter -->
                        <div>
                            <label for="user_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                {{ __('app.audit_filter_by_user') }}
                            </label>
                            <select name="user_id" id="user_id" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">{{ __('app.all') }}</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Action Filter -->
                        <div>
                            <label for="action" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                {{ __('app.audit_filter_by_action') }}
                            </label>
                            <select name="action" id="action" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">{{ __('app.all') }}</option>
                                @foreach($actions as $action)
                                    <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                                        {{ __('app.audit_action_' . str_replace('.', '_', $action)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Date From -->
                        <div>
                            <label for="date_from" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                {{ __('app.audit_date_from') }}
                            </label>
                            <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" 
                                   class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <!-- Date To -->
                        <div>
                            <label for="date_to" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                {{ __('app.audit_date_to') }}
                            </label>
                            <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" 
                                   class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <!-- Filter Buttons -->
                        <div class="md:col-span-4 flex gap-2">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                                </svg>
                                {{ __('app.filter') }}
                            </button>
                            <a href="{{ route('audit-logs.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 dark:bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-400 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                {{ __('app.clear') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Audit Logs Table -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if($logs->isEmpty())
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">{{ __('app.audit_no_logs') }}</h3>
                        </div>
                    @else
                        <!-- Desktop Table View -->
                        <div class="hidden md:block overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('app.audit_action') }}</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('app.audit_actor') }}</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('app.audit_subject') }}</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('app.audit_ip_address') }}</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('app.audit_timestamp') }}</th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('app.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($logs as $log)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    @if(str_starts_with($log->action, 'auth.'))
                                                        bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                                    @elseif(str_starts_with($log->action, 'user.'))
                                                        bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200
                                                    @elseif(str_starts_with($log->action, 'vps.'))
                                                        bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                                    @elseif(str_starts_with($log->action, 'server.'))
                                                        bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                                    @else
                                                        bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200
                                                    @endif
                                                ">
                                                    {{ __('app.audit_action_' . str_replace('.', '_', $log->action)) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($log->actor)
                                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $log->actor->name ?? 'N/A' }}</div>
                                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $log->actor->email ?? '' }}</div>
                                                @else
                                                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('app.system') }}</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($log->subject)
                                                    <div class="text-sm text-gray-900 dark:text-gray-100">
                                                        {{ class_basename($log->subject_type) }} #{{ $log->subject_id }}
                                                    </div>
                                                    @if(method_exists($log->subject, 'name') || property_exists($log->subject, 'name'))
                                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $log->subject->name ?? $log->subject->hostname ?? '' }}</div>
                                                    @endif
                                                @elseif($log->subject_id)
                                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                                        {{ class_basename($log->subject_type ?? 'Unknown') }} #{{ $log->subject_id }}
                                                    </div>
                                                @else
                                                    <span class="text-sm text-gray-500 dark:text-gray-400">-</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ $log->ip_address ?? '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ $log->created_at?->format('M d, Y H:i:s') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <a href="{{ route('audit-logs.show', $log) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300" title="{{ __('app.view') }}">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                    </svg>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Mobile Card View -->
                        <div class="md:hidden space-y-4">
                            @foreach($logs as $log)
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                    <div class="flex justify-between items-start mb-3">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            @if(str_starts_with($log->action, 'auth.'))
                                                bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                            @elseif(str_starts_with($log->action, 'user.'))
                                                bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200
                                            @elseif(str_starts_with($log->action, 'vps.'))
                                                bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                            @elseif(str_starts_with($log->action, 'server.'))
                                                bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                            @else
                                                bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-200
                                            @endif
                                        ">
                                            {{ __('app.audit_action_' . str_replace('.', '_', $log->action)) }}
                                        </span>
                                        <a href="{{ route('audit-logs.show', $log) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 text-sm">
                                            {{ __('app.view') }}
                                        </a>
                                    </div>
                                    <div class="text-sm text-gray-900 dark:text-gray-100 mb-1">
                                        <span class="font-medium">{{ __('app.audit_actor') }}:</span>
                                        {{ $log->actor?->name ?? __('app.system') }}
                                    </div>
                                    @if($log->subject_id)
                                        <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">
                                            <span class="font-medium">{{ __('app.audit_subject') }}:</span>
                                            {{ class_basename($log->subject_type ?? 'Unknown') }} #{{ $log->subject_id }}
                                        </div>
                                    @endif
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        <span>{{ $log->ip_address ?? '-' }}</span>
                                        <span class="mx-2">•</span>
                                        <span>{{ $log->created_at?->format('M d, Y H:i') }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6">
                            {{ $logs->withQueryString()->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
