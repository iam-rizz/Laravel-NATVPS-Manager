<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('servers.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 mr-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight" style="font-size: 16px;">
                Edit Server: {{ $server->name }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('servers.update', $server) }}">
                        @csrf
                        @method('PUT')

                        <!-- Name -->
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Server Name</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $server->name) }}" required
                                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                   placeholder="My Virtualizor Server">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- IP Address -->
                        <div class="mb-4">
                            <label for="ip_address" class="block text-sm font-medium text-gray-700 dark:text-gray-300">IP Address / Hostname</label>
                            <input type="text" name="ip_address" id="ip_address" value="{{ old('ip_address', $server->ip_address) }}" required
                                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                   placeholder="192.168.1.100 or server.example.com">
                            @error('ip_address')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Port -->
                        <div class="mb-4">
                            <label for="port" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Port</label>
                            <input type="number" name="port" id="port" value="{{ old('port', $server->port) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                   min="1" max="65535">
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Default Virtualizor port is 4083</p>
                            @error('port')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- API Key -->
                        <div class="mb-4">
                            <label for="api_key" class="block text-sm font-medium text-gray-700 dark:text-gray-300">API Key</label>
                            <input type="text" name="api_key" id="api_key" value=""
                                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm font-mono"
                                   placeholder="Leave blank to keep current key">
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Leave blank to keep the existing API key</p>
                            @error('api_key')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- API Pass -->
                        <div class="mb-4">
                            <label for="api_pass" class="block text-sm font-medium text-gray-700 dark:text-gray-300">API Password</label>
                            <input type="password" name="api_pass" id="api_pass"
                                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                   placeholder="Leave blank to keep current password">
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Leave blank to keep the existing API password</p>
                            @error('api_pass')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Is Active -->
                        <div class="mb-6">
                            <label class="flex items-center">
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $server->is_active) ? 'checked' : '' }}
                                       class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:bg-gray-700">
                                <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Server is active</span>
                            </label>
                        </div>

                        <!-- Server Info -->
                        <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Server Information</h4>
                            <dl class="grid grid-cols-2 gap-2 text-sm">
                                <dt class="text-gray-500 dark:text-gray-400">Created:</dt>
                                <dd class="text-gray-900 dark:text-gray-100">{{ $server->created_at->format('M d, Y H:i') }}</dd>
                                <dt class="text-gray-500 dark:text-gray-400">Last Checked:</dt>
                                <dd class="text-gray-900 dark:text-gray-100">{{ $server->last_checked ? $server->last_checked->format('M d, Y H:i') : 'Never' }}</dd>
                                <dt class="text-gray-500 dark:text-gray-400">NAT VPS Count:</dt>
                                <dd class="text-gray-900 dark:text-gray-100">{{ $server->natVps()->count() }}</dd>
                            </dl>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <button type="button" 
                                    onclick="testConnection({{ $server->id }})"
                                    class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                Test Connection
                            </button>
                            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 sm:space-x-3">
                                <a href="{{ route('servers.index') }}" 
                                   class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                    Cancel
                                </a>
                                <button type="submit" 
                                        class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                    Update Server
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Connection Test Result Modal -->
    <div id="connectionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="mt-3 text-center">
                <div id="modalIcon" class="mx-auto flex items-center justify-center h-12 w-12 rounded-full"></div>
                <h3 id="modalTitle" class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100 mt-4"></h3>
                <div class="mt-2 px-7 py-3">
                    <p id="modalMessage" class="text-sm text-gray-500 dark:text-gray-400"></p>
                </div>
                <div class="items-center px-4 py-3">
                    <button onclick="closeModal()" class="px-4 py-2 bg-indigo-600 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        Close
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
            modalIcon.innerHTML = '<svg class="animate-spin h-8 w-8 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
            modalIcon.className = 'mx-auto flex items-center justify-center h-12 w-12';
            modalTitle.textContent = 'Testing Connection...';
            modalMessage.textContent = 'Please wait while we test the connection to the server.';
            
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
                    modalIcon.innerHTML = '<svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';
                    modalIcon.className = 'mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 dark:bg-green-900';
                    modalTitle.textContent = 'Connection Successful';
                    let message = data.message;
                    if (data.data && data.data.vps_count !== undefined) {
                        message += ` (${data.data.vps_count} VPS found)`;
                    }
                    modalMessage.textContent = message;
                } else {
                    modalIcon.innerHTML = '<svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>';
                    modalIcon.className = 'mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900';
                    modalTitle.textContent = 'Connection Failed';
                    modalMessage.textContent = data.message;
                }
            })
            .catch(error => {
                modalIcon.innerHTML = '<svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>';
                modalIcon.className = 'mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900';
                modalTitle.textContent = 'Error';
                modalMessage.textContent = 'An unexpected error occurred while testing the connection.';
            });
        }
        
        function closeModal() {
            document.getElementById('connectionModal').classList.add('hidden');
        }
    </script>
</x-app-layout>
