<x-app-layout>
    <x-slot name="header">
        {{ __('app.import_vps_json') }}
    </x-slot>

    <div class="space-y-6">
        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-display font-bold text-surface-900 dark:text-white">
                    {{ __('app.import_vps_json') }}
                </h1>
                <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                    {{ __('app.import_vps_json_desc') }}
                </p>
            </div>
            <a href="{{ route('vps.index') }}" 
               class="inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium rounded-lg border border-surface-300 dark:border-surface-600 text-surface-700 dark:text-surface-300 bg-white dark:bg-surface-800 hover:bg-surface-50 dark:hover:bg-surface-700 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                {{ __('app.back') }}
            </a>
        </div>

        <!-- Import Form -->
        <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 shadow-sm">
            <div class="p-6">
                <form action="{{ route('vps.import-json.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <!-- File Upload -->
                    <div>
                        <label for="file" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">
                            {{ __('app.select_json_file') }}
                        </label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-surface-300 dark:border-surface-600 border-dashed rounded-lg hover:border-primary-400 dark:hover:border-primary-500 transition-colors">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-surface-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-surface-600 dark:text-surface-400">
                                    <label for="file" class="relative cursor-pointer rounded-md font-medium text-primary-600 dark:text-primary-400 hover:text-primary-500 focus-within:outline-none">
                                        <span>{{ __('app.upload_file') }}</span>
                                        <input id="file" name="file" type="file" accept=".json" class="sr-only" required>
                                    </label>
                                    <p class="pl-1">{{ __('app.or_drag_drop') }}</p>
                                </div>
                                <p class="text-xs text-surface-500 dark:text-surface-400">JSON up to 2MB</p>
                            </div>
                        </div>
                        <p id="file-name" class="mt-2 text-sm text-surface-600 dark:text-surface-400"></p>
                        @error('file')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Import Mode -->
                    <div>
                        <label class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">
                            {{ __('app.import_mode') }}
                        </label>
                        <div class="space-y-3">
                            <label class="flex items-start p-4 border border-surface-200 dark:border-surface-600 rounded-lg cursor-pointer hover:bg-surface-50 dark:hover:bg-surface-700/50 transition-colors">
                                <input type="radio" name="mode" value="skip" checked class="mt-0.5 h-4 w-4 text-primary-600 border-surface-300 focus:ring-primary-500">
                                <div class="ml-3">
                                    <span class="block text-sm font-medium text-surface-900 dark:text-white">{{ __('app.import_mode_skip') }}</span>
                                    <span class="block text-sm text-surface-500 dark:text-surface-400">{{ __('app.import_mode_skip_vps_desc') }}</span>
                                </div>
                            </label>
                            <label class="flex items-start p-4 border border-surface-200 dark:border-surface-600 rounded-lg cursor-pointer hover:bg-surface-50 dark:hover:bg-surface-700/50 transition-colors">
                                <input type="radio" name="mode" value="update" class="mt-0.5 h-4 w-4 text-primary-600 border-surface-300 focus:ring-primary-500">
                                <div class="ml-3">
                                    <span class="block text-sm font-medium text-surface-900 dark:text-white">{{ __('app.import_mode_update') }}</span>
                                    <span class="block text-sm text-surface-500 dark:text-surface-400">{{ __('app.import_mode_update_vps_desc') }}</span>
                                </div>
                            </label>
                        </div>
                        @error('mode')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Info -->
                    <div class="p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                        <div class="flex">
                            <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">{{ __('app.import_matching_info') }}</h3>
                                <ul class="mt-2 text-sm text-blue-700 dark:text-blue-300 list-disc list-inside space-y-1">
                                    <li>{{ __('app.import_match_server') }}</li>
                                    <li>{{ __('app.import_match_user') }}</li>
                                    <li>{{ __('app.import_match_vps') }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Warning -->
                    <div class="p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg">
                        <div class="flex">
                            <svg class="h-5 w-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-amber-800 dark:text-amber-200">{{ __('app.important') }}</h3>
                                <p class="mt-1 text-sm text-amber-700 dark:text-amber-300">{{ __('app.import_vps_warning') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="flex justify-end gap-3">
                        <a href="{{ route('vps.index') }}" 
                           class="px-4 py-2.5 text-sm font-medium rounded-lg border border-surface-300 dark:border-surface-600 text-surface-700 dark:text-surface-300 bg-white dark:bg-surface-800 hover:bg-surface-50 dark:hover:bg-surface-700 transition-colors">
                            {{ __('app.cancel') }}
                        </a>
                        <button type="submit" 
                                class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium rounded-lg bg-primary-600 text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                            </svg>
                            {{ __('app.import') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- JSON Format Example -->
        <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 shadow-sm">
            <div class="p-6">
                <h3 class="text-lg font-medium text-surface-900 dark:text-white mb-4">{{ __('app.json_format_example') }}</h3>
                <pre class="p-4 bg-surface-900 dark:bg-surface-950 rounded-lg text-sm text-green-400 overflow-x-auto"><code>{
  "exported_at": "2024-01-01T00:00:00+00:00",
  "app_name": "NAT VPS Manager",
  "version": "1.0",
  "nat_vps": [
    {
      "hostname": "vps-001",
      "vps_id": 1,
      "server_name": "Server 1",
      "server_ip": "192.168.1.1",
      "user_email": "user@example.com",
      "ssh_username": "root",
      "ssh_password": "password123",
      "ssh_port": 22
    }
  ]
}</code></pre>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('file').addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name || '';
            document.getElementById('file-name').textContent = fileName ? 'Selected: ' + fileName : '';
        });
    </script>
</x-app-layout>
