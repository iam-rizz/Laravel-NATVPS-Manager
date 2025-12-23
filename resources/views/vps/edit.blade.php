<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('vps.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 mr-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight" style="font-size: 16px;">
                Edit NAT VPS: {{ $natVps->hostname }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('vps.update', $natVps) }}">
                        @csrf
                        @method('PUT')

                        <!-- Server Selection -->
                        <div class="mb-4">
                            <label for="server_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Server</label>
                            <select name="server_id" id="server_id" required
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="">Select a server</option>
                                @foreach($servers as $server)
                                    <option value="{{ $server->id }}" {{ old('server_id', $natVps->server_id) == $server->id ? 'selected' : '' }}>
                                        {{ $server->name }} ({{ $server->ip_address }})
                                    </option>
                                @endforeach
                            </select>
                            @error('server_id')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- VPS ID -->
                        <div class="mb-4">
                            <label for="vps_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Virtualizor VPS ID</label>
                            <input type="number" name="vps_id" id="vps_id" value="{{ old('vps_id', $natVps->vps_id) }}" required min="1"
                                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                   placeholder="Enter VPS ID from Virtualizor">
                            @error('vps_id')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Hostname -->
                        <div class="mb-4">
                            <label for="hostname" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Hostname</label>
                            <input type="text" name="hostname" id="hostname" value="{{ old('hostname', $natVps->hostname) }}" required
                                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                   placeholder="my-vps.example.com">
                            @error('hostname')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- User Assignment -->
                        <div class="mb-4">
                            <label for="user_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Assign to User</label>
                            <select name="user_id" id="user_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="">No user assigned</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id', $natVps->user_id) == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- SSH Credentials Section -->
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-4 mt-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">SSH Credentials</h3>

                            <!-- SSH Username -->
                            <div class="mb-4">
                                <label for="ssh_username" class="block text-sm font-medium text-gray-700 dark:text-gray-300">SSH Username</label>
                                <input type="text" name="ssh_username" id="ssh_username" value=""
                                       class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                       placeholder="Leave blank to keep current username">
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Leave blank to keep the existing username</p>
                                @error('ssh_username')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- SSH Password -->
                            <div class="mb-4">
                                <label for="ssh_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">SSH Password</label>
                                <input type="password" name="ssh_password" id="ssh_password"
                                       class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                       placeholder="Leave blank to keep current password">
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Leave blank to keep the existing password</p>
                                @error('ssh_password')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- SSH Port -->
                            <div class="mb-4">
                                <label for="ssh_port" class="block text-sm font-medium text-gray-700 dark:text-gray-300">SSH Port</label>
                                <input type="number" name="ssh_port" id="ssh_port" value="{{ old('ssh_port', $natVps->ssh_port ?? 22) }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                       min="1" max="65535">
                                @error('ssh_port')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- VPS Info -->
                        <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">VPS Information</h4>
                            <dl class="grid grid-cols-2 gap-2 text-sm">
                                <dt class="text-gray-500 dark:text-gray-400">Created:</dt>
                                <dd class="text-gray-900 dark:text-gray-100">{{ $natVps->created_at->format('M d, Y H:i') }}</dd>
                                <dt class="text-gray-500 dark:text-gray-400">Last Updated:</dt>
                                <dd class="text-gray-900 dark:text-gray-100">{{ $natVps->updated_at->format('M d, Y H:i') }}</dd>
                            </dl>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-3">
                            <a href="{{ route('vps.index') }}" 
                               class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                Update NAT VPS
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
