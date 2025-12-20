<x-app-layout>
    <x-slot name="header">
        {{ __('app.servers') }}
    </x-slot>

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <p class="text-surface-500 dark:text-surface-400">{{ __('app.manage_virtualizor_servers') ?? 'Manage your Virtualizor servers' }}</p>
        </div>
        <a href="{{ route('admin.servers.create') }}" class="btn btn-primary">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            {{ __('app.add_server') ?? 'Add Server' }}
        </a>
    </div>

    @if($servers->isEmpty())
        <!-- Empty State -->
        <div class="card">
            <div class="card-body text-center py-12">
                <div class="w-16 h-16 mx-auto bg-surface-100 dark:bg-surface-800 rounded-2xl flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-surface-400 dark:text-surface-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-surface-900 dark:text-white mb-2">{{ __('app.no_servers') ?? 'No servers' }}</h3>
                <p class="text-surface-500 dark:text-surface-400 mb-6">{{ __('app.get_started_add_server') ?? 'Get started by adding a new Virtualizor server.' }}</p>
                <a href="{{ route('admin.servers.create') }}" class="btn btn-primary">
                    {{ __('app.add_server') ?? 'Add Server' }}
                </a>
            </div>
        </div>
    @else
        <!-- Desktop Table View -->
        <div class="hidden md:block">
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>{{ __('app.name') ?? 'Name' }}</th>
                            <th>{{ __('app.ip_address') ?? 'IP Address' }}</th>
                            <th>{{ __('app.port') ?? 'Port' }}</th>
                            <th>{{ __('app.nat_vps') }}</th>
                            <th>{{ __('app.status') ?? 'Status' }}</th>
                            <th>{{ __('app.last_checked') ?? 'Last Checked' }}</th>
                            <th class="text-right">{{ __('app.actions') ?? 'Actions' }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($servers as $server)
                            <tr>
                                <td>
                                    <span class="font-medium text-surface-900 dark:text-white">{{ $server->name }}</span>
                                </td>
                                <td>{{ $server->ip_address }}</td>
                                <td>{{ $server->port }}</td>
                                <td>
                                    <span class="badge badge-info">{{ $server->nat_vps_count }}</span>
                                </td>
                                <td>
                                    @if($server->is_active)
                                        <span class="badge badge-success">{{ __('app.active') ?? 'Active' }}</span>
                                    @else
                                        <span class="badge badge-secondary">{{ __('app.inactive') ?? 'Inactive' }}</span>
                                    @endif
                                </td>
                                <td class="text-surface-500 dark:text-surface-400">
                                    {{ $server->last_checked ? $server->last_checked->diffForHumans() : __('app.never') ?? 'Never' }}
                                </td>
                                <td>
                                    <div class="flex justify-end gap-2">
                                        <button type="button" 
                                                onclick="testConnection({{ $server->id }})"
                                                class="btn btn-sm btn-ghost text-blue-600 dark:text-blue-400"
                                                title="{{ __('app.test_connection') ?? 'Test Connection' }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                            </svg>
                                        </button>
                                        <a href="{{ route('admin.servers.edit', $server) }}" 
                                           class="btn btn-sm btn-ghost"
                                           title="{{ __('app.edit') ?? 'Edit' }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                        <form action="{{ route('admin.servers.destroy', $server) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('app.confirm_delete_server') ?? 'Are you sure you want to delete this server?' }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-sm btn-ghost text-red-600 dark:text-red-400"
                                                    title="{{ __('app.delete') ?? 'Delete' }}">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
        </div>

        <!-- Mobile Card View -->
        <div class="md:hidden space-y-4">
            @foreach($servers as $server)
                <div class="card">
                    <div class="card-body">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h3 class="font-medium text-surface-900 dark:text-white">{{ $server->name }}</h3>
                                <p class="text-sm text-surface-500 dark:text-surface-400">{{ $server->ip_address }}:{{ $server->port }}</p>
                            </div>
                            @if($server->is_active)
                                <span class="badge badge-success">{{ __('app.active') ?? 'Active' }}</span>
                            @else
                                <span class="badge badge-secondary">{{ __('app.inactive') ?? 'Inactive' }}</span>
                            @endif
                        </div>
                        <div class="flex items-center gap-4 text-sm text-surface-500 dark:text-surface-400 mb-4">
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
                                </svg>
                                {{ $server->nat_vps_count }} VPS
                            </span>
                            <span>{{ $server->last_checked ? $server->last_checked->diffForHumans() : __('app.never') ?? 'Never' }}</span>
                        </div>
                        <div class="flex gap-2">
                            <button type="button" 
                                    onclick="testConnection({{ $server->id }})"
                                    class="btn btn-sm btn-secondary flex-1">
                                {{ __('app.test') ?? 'Test' }}
                            </button>
                            <a href="{{ route('admin.servers.edit', $server) }}" class="btn btn-sm btn-secondary flex-1">
                                {{ __('app.edit') ?? 'Edit' }}
                            </a>
                            <form action="{{ route('admin.servers.destroy', $server) }}" method="POST" class="flex-1" onsubmit="return confirm('{{ __('app.confirm_delete_server') ?? 'Are you sure?' }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger w-full">
                                    {{ __('app.delete') ?? 'Delete' }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Connection Test Modal -->
    <div id="connectionModal" class="fixed inset-0 bg-surface-900/50 backdrop-blur-sm overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto max-w-md">
            <div class="card mx-4">
                <div class="card-body text-center">
                    <div id="modalIcon" class="mx-auto flex items-center justify-center h-14 w-14 rounded-2xl mb-4">
                        <!-- Icon will be inserted here -->
                    </div>
                    <h3 id="modalTitle" class="text-lg font-display font-semibold text-surface-900 dark:text-white mb-2"></h3>
                    <p id="modalMessage" class="text-surface-500 dark:text-surface-400 mb-6"></p>
                    <button onclick="closeModal()" class="btn btn-primary w-full">
                        {{ __('app.close') ?? 'Close' }}
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
            modalIcon.innerHTML = '<div class="spinner spinner-lg"></div>';
            modalIcon.className = 'mx-auto flex items-center justify-center h-14 w-14';
            modalTitle.textContent = '{{ __("app.testing_connection") ?? "Testing Connection..." }}';
            modalMessage.textContent = '{{ __("app.please_wait") ?? "Please wait while we test the connection." }}';
            
            fetch(`/admin/servers/${serverId}/test-connection`, {
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
                    modalTitle.textContent = '{{ __("app.connection_successful") ?? "Connection Successful" }}';
                    let message = data.message;
                    if (data.data && data.data.vps_count !== undefined) {
                        message += ` (${data.data.vps_count} VPS found)`;
                    }
                    modalMessage.textContent = message;
                } else {
                    modalIcon.innerHTML = '<svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>';
                    modalIcon.className = 'mx-auto flex items-center justify-center h-14 w-14 rounded-2xl bg-gradient-to-br from-red-500 to-red-600';
                    modalTitle.textContent = '{{ __("app.connection_failed") ?? "Connection Failed" }}';
                    modalMessage.textContent = data.message;
                }
            })
            .catch(error => {
                modalIcon.innerHTML = '<svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>';
                modalIcon.className = 'mx-auto flex items-center justify-center h-14 w-14 rounded-2xl bg-gradient-to-br from-amber-500 to-amber-600';
                modalTitle.textContent = '{{ __("app.error") ?? "Error" }}';
                modalMessage.textContent = '{{ __("app.unexpected_error") ?? "An unexpected error occurred." }}';
            });
        }
        
        function closeModal() {
            document.getElementById('connectionModal').classList.add('hidden');
            window.location.reload();
        }
    </script>
</x-app-layout>
