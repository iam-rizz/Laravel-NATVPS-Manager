<x-app-layout>
    <x-slot name="header">
        {{ __('app.servers') }}
    </x-slot>

    <div class="space-y-6">
        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-display font-bold text-surface-900 dark:text-white">
                    {{ __('app.servers') }}
                </h1>
                <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">{{ __('app.manage_virtualizor_servers') }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('servers.export') }}" 
                   class="inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium rounded-lg border border-surface-300 dark:border-surface-600 text-surface-700 dark:text-surface-300 bg-white dark:bg-surface-800 hover:bg-surface-50 dark:hover:bg-surface-700 transition-all duration-150">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    {{ __('app.export') }}
                </a>
                <a href="{{ route('servers.import') }}" 
                   class="inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium rounded-lg bg-green-600 text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-150">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    {{ __('app.import') }}
                </a>
                <a href="{{ route('servers.create') }}" 
                   class="inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium rounded-lg bg-primary-600 text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all duration-150">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    {{ __('app.add_server') }}
                </a>
            </div>
        </div>

        <!-- Content Card -->
        <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 shadow-sm">
            <div class="p-6">
                @if($servers->isEmpty())
                    <div class="text-center py-8">
                        <div class="w-16 h-16 mx-auto bg-surface-100 dark:bg-surface-700 rounded-2xl flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-surface-400 dark:text-surface-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-surface-900 dark:text-white mb-2">{{ __('app.no_servers') }}</h3>
                        <p class="text-surface-500 dark:text-surface-400 mb-6">{{ __('app.get_started_add_server') }}</p>
                        <a href="{{ route('servers.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium rounded-lg bg-primary-600 text-white hover:bg-primary-700 transition-all duration-150">
                            {{ __('app.add_server') }}
                        </a>
                    </div>
                @else
                    <!-- Desktop Table View -->
                    <div class="hidden md:block overflow-x-auto">
                        <table class="min-w-full divide-y divide-surface-200 dark:divide-surface-700">
                            <thead class="bg-surface-50 dark:bg-surface-800">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-surface-500 dark:text-surface-400 uppercase tracking-wider">{{ __('app.name') }}</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-surface-500 dark:text-surface-400 uppercase tracking-wider">{{ __('app.ip_address') }}</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-surface-500 dark:text-surface-400 uppercase tracking-wider">{{ __('app.port') }}</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-surface-500 dark:text-surface-400 uppercase tracking-wider">{{ __('app.nat_vps') }}</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-surface-500 dark:text-surface-400 uppercase tracking-wider">{{ __('app.status') }}</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-surface-500 dark:text-surface-400 uppercase tracking-wider">{{ __('app.last_checked') }}</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-surface-500 dark:text-surface-400 uppercase tracking-wider">{{ __('app.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-surface-800 divide-y divide-surface-200 dark:divide-surface-700">
                                @foreach($servers as $server)
                                    <tr class="hover:bg-surface-50 dark:hover:bg-surface-700/50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="text-sm font-medium text-surface-900 dark:text-white">{{ $server->name }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="text-sm text-surface-500 dark:text-surface-400">{{ $server->ip_address }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="text-sm text-surface-500 dark:text-surface-400">{{ $server->port }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300">
                                                {{ $server->nat_vps_count }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($server->is_active)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300">
                                                    {{ __('app.active') }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-surface-100 dark:bg-surface-700 text-surface-600 dark:text-surface-400">
                                                    {{ __('app.inactive') }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="text-sm text-surface-500 dark:text-surface-400">
                                                {{ $server->last_checked ? $server->last_checked->diffForHumans() : __('app.never') }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex justify-end gap-2">
                                                <button type="button" 
                                                        onclick="testConnection({{ $server->id }})"
                                                        class="p-2 text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors"
                                                        title="{{ __('app.test_connection') }}">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                                    </svg>
                                                </button>
                                                <a href="{{ route('servers.edit', $server) }}" 
                                                   class="p-2 text-primary-600 hover:text-primary-800 dark:text-primary-400 dark:hover:text-primary-300 hover:bg-primary-50 dark:hover:bg-primary-900/20 rounded-lg transition-colors"
                                                   title="{{ __('app.edit') }}">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                    </svg>
                                                </a>
                                    <form action="{{ route('servers.destroy', $server) }}" method="POST" class="inline" onsubmit="return confirm({{ json_encode(__('app.confirm_delete')) }})">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="p-2 text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
                                                            title="{{ __('app.delete') }}">
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
                        @foreach($servers as $server)
                            <div class="bg-surface-50 dark:bg-surface-700/50 rounded-xl p-4 border border-surface-200 dark:border-surface-600">
                                <div class="flex justify-between items-start mb-3">
                                    <div>
                                        <h3 class="text-sm font-medium text-surface-900 dark:text-white">{{ $server->name }}</h3>
                                        <p class="text-sm text-surface-500 dark:text-surface-400">{{ $server->ip_address }}:{{ $server->port }}</p>
                                    </div>
                                    @if($server->is_active)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300">
                                            {{ __('app.active') }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-surface-100 dark:bg-surface-600 text-surface-600 dark:text-surface-300">
                                            {{ __('app.inactive') }}
                                        </span>
                                    @endif
                                </div>
                                <div class="text-sm text-surface-500 dark:text-surface-400 mb-3">
                                    <span>{{ $server->nat_vps_count }} VPS</span>
                                    <span class="mx-2">•</span>
                                    <span>{{ $server->last_checked ? $server->last_checked->diffForHumans() : __('app.never') }}</span>
                                </div>
                                <div class="flex gap-2">
                                    <button type="button" 
                                            onclick="testConnection({{ $server->id }})"
                                            class="flex-1 inline-flex justify-center items-center px-3 py-2 text-sm font-medium rounded-lg border border-surface-300 dark:border-surface-600 text-surface-700 dark:text-surface-300 bg-white dark:bg-surface-800 hover:bg-surface-50 dark:hover:bg-surface-700 transition-colors">
                                        {{ __('app.test') }}
                                    </button>
                                    <a href="{{ route('servers.edit', $server) }}" 
                                       class="flex-1 inline-flex justify-center items-center px-3 py-2 text-sm font-medium rounded-lg border border-surface-300 dark:border-surface-600 text-surface-700 dark:text-surface-300 bg-white dark:bg-surface-800 hover:bg-surface-50 dark:hover:bg-surface-700 transition-colors">
                                        {{ __('app.edit') }}
                                    </a>
                                    <form action="{{ route('servers.destroy', $server) }}" method="POST" class="flex-1" onsubmit="return confirm({{ json_encode(__('app.confirm_delete')) }})">
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


    <!-- Connection Test Modal -->
    <div id="connectionModal" class="fixed inset-0 bg-surface-900/50 backdrop-blur-sm overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto max-w-md">
            <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 shadow-xl mx-4">
                <div class="p-6 text-center">
                    <div id="modalIcon" class="mx-auto flex items-center justify-center h-14 w-14 rounded-2xl mb-4">
                        <!-- Icon will be inserted here -->
                    </div>
                    <h3 id="modalTitle" class="text-lg font-display font-semibold text-surface-900 dark:text-white mb-2"></h3>
                    <p id="modalMessage" class="text-surface-500 dark:text-surface-400 mb-6"></p>
                    <button onclick="closeModal()" class="w-full inline-flex justify-center items-center px-4 py-2.5 text-sm font-medium rounded-lg bg-primary-600 text-white hover:bg-primary-700 transition-all duration-150">
                        {{ __('app.close') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function testConnection(serverId) {
            const modal = document.getElementById('connectionModal');
            const modalIcon = document.getElementById('modalIcon');
            const modalTitle = document.getElementById('modalTitle');
            const modalMessage = document.getElementById('modalMessage');
            
            modal.classList.remove('hidden');
            modalIcon.innerHTML = '<div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>';
            modalIcon.className = 'mx-auto flex items-center justify-center h-14 w-14';
            modalTitle.textContent = '{{ __("app.testing_connection") }}';
            modalMessage.textContent = '{{ __("app.please_wait") }}';
            
            fetch(`/servers/${serverId}/test-connection`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    modalIcon.innerHTML = '<svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';
                    modalIcon.className = 'mx-auto flex items-center justify-center h-14 w-14 rounded-2xl bg-gradient-to-br from-green-500 to-green-600';
                    modalTitle.textContent = '{{ __("app.connection_successful") }}';
                    let message = data.message;
                    if (data.data && data.data.vps_count !== undefined) {
                        message += ` (${data.data.vps_count} VPS found)`;
                    }
                    modalMessage.textContent = message;
                } else {
                    modalIcon.innerHTML = '<svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>';
                    modalIcon.className = 'mx-auto flex items-center justify-center h-14 w-14 rounded-2xl bg-gradient-to-br from-red-500 to-red-600';
                    modalTitle.textContent = '{{ __("app.connection_failed") }}';
                    modalMessage.textContent = data.message;
                }
            })
            .catch(error => {
                modalIcon.innerHTML = '<svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>';
                modalIcon.className = 'mx-auto flex items-center justify-center h-14 w-14 rounded-2xl bg-gradient-to-br from-amber-500 to-amber-600';
                modalTitle.textContent = '{{ __("app.error") }}';
                modalMessage.textContent = '{{ __("app.unexpected_error") }}';
            });
        }
        
        function closeModal() {
            document.getElementById('connectionModal').classList.add('hidden');
            window.location.reload();
        }
    </script>
</x-app-layout>
