<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight" style="font-size: 16px;">
            Health Check Settings
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @include('admin.settings.partials.nav')

            <!-- Cron Status Alert -->
            @if($cronStatus['is_warning'])
                <div class="mb-6 p-4 rounded-lg {{ $cronStatus['status'] === 'never' ? 'bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800' : 'bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800' }}">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            @if($cronStatus['status'] === 'never')
                                <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                            @else
                                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                            @endif
                        </div>
                        <div class="ml-3 flex-1">
                            <h3 class="text-sm font-medium {{ $cronStatus['status'] === 'never' ? 'text-yellow-800 dark:text-yellow-200' : 'text-red-800 dark:text-red-200' }}">
                                {{ $cronStatus['message'] }}
                            </h3>
                            <p class="mt-1 text-sm {{ $cronStatus['status'] === 'never' ? 'text-yellow-700 dark:text-yellow-300' : 'text-red-700 dark:text-red-300' }}">
                                @if($cronStatus['status'] === 'never')
                                    The scheduler has never been executed. Please configure the cron job below.
                                @else
                                    Last run: {{ $cronStatus['last_run'] }}. The scheduler may have stopped working.
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('settings.health-check.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Health Check Settings Section -->
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Server Health Check</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                            Configure automatic server connection testing to monitor your Virtualizor servers.
                        </p>

                        <div class="space-y-4 mb-8">
                            <!-- Enable Health Check -->
                            <div class="flex items-start p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                                <div class="flex items-center h-5">
                                    <input type="checkbox" name="server_health_check_enabled" value="1" 
                                        {{ ($settings['server_health_check_enabled']->value ?? '0') == '1' ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                </div>
                                <div class="ml-3">
                                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Enable Automatic Health Check</label>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Automatically test connection to all active servers on schedule</p>
                                </div>
                            </div>

                            <!-- Check Interval -->
                            <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <label for="server_health_check_interval" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Check Interval
                                </label>
                                <select name="server_health_check_interval" id="server_health_check_interval"
                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="1" {{ ($settings['server_health_check_interval']->value ?? '5') == '1' ? 'selected' : '' }}>Every 1 minute</option>
                                    <option value="5" {{ ($settings['server_health_check_interval']->value ?? '5') == '5' ? 'selected' : '' }}>Every 5 minutes</option>
                                    <option value="10" {{ ($settings['server_health_check_interval']->value ?? '5') == '10' ? 'selected' : '' }}>Every 10 minutes</option>
                                    <option value="30" {{ ($settings['server_health_check_interval']->value ?? '5') == '30' ? 'selected' : '' }}>Every 30 minutes</option>
                                    <option value="60" {{ ($settings['server_health_check_interval']->value ?? '5') == '60' ? 'selected' : '' }}>Every 60 minutes</option>
                                </select>
                                <p class="text-xs text-gray-500 mt-1">How often to test server connections</p>
                                @error('server_health_check_interval')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Email Notification -->
                            <div class="flex items-start p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <div class="flex items-center h-5">
                                    <input type="checkbox" name="notify_server_connection_failed" value="1" 
                                        {{ ($settings['notify_server_connection_failed']->value ?? '1') == '1' ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                </div>
                                <div class="ml-3">
                                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Email Notification on Failure</label>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Send email to all admin users when a server connection fails</p>
                                </div>
                            </div>
                        </div>

                        <!-- Cronjob Setup Section -->
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Cronjob Setup</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                            The Laravel scheduler must be running for health checks to work. Configure the cron job based on your server environment.
                        </p>

                        <div class="space-y-4 mb-8">
                            <!-- Cron Status -->
                            <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Scheduler Status</span>
                                    @if($cronStatus['status'] === 'running')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                            <svg class="w-2 h-2 mr-1" fill="currentColor" viewBox="0 0 8 8">
                                                <circle cx="4" cy="4" r="3"/>
                                            </svg>
                                            Running
                                        </span>
                                    @elseif($cronStatus['status'] === 'stopped')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                            <svg class="w-2 h-2 mr-1" fill="currentColor" viewBox="0 0 8 8">
                                                <circle cx="4" cy="4" r="3"/>
                                            </svg>
                                            Stopped
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-200">
                                            <svg class="w-2 h-2 mr-1" fill="currentColor" viewBox="0 0 8 8">
                                                <circle cx="4" cy="4" r="3"/>
                                            </svg>
                                            Never Run
                                        </span>
                                    @endif
                                </div>
                                @if($cronStatus['last_run'])
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        Last run: {{ $cronStatus['last_run'] }}
                                    </p>
                                @endif
                            </div>

                            <!-- Environment Toggle -->
                            <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Server Environment
                                </label>
                                <div class="flex space-x-4">
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="cron_environment" value="vps" 
                                            {{ ($settings['cron_environment']->value ?? 'vps') == 'vps' ? 'checked' : '' }}
                                            class="form-radio text-indigo-600 border-gray-300 focus:ring-indigo-500"
                                            onchange="updateCronCommand()">
                                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">VPS / Dedicated Server</span>
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="cron_environment" value="hosting" 
                                            {{ ($settings['cron_environment']->value ?? 'vps') == 'hosting' ? 'checked' : '' }}
                                            class="form-radio text-indigo-600 border-gray-300 focus:ring-indigo-500"
                                            onchange="updateCronCommand()">
                                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Shared Hosting (cPanel/Plesk)</span>
                                    </label>
                                </div>
                                <p class="text-xs text-gray-500 mt-2">Select your server environment to get the correct cron command format</p>
                            </div>

                            <!-- Cron Command -->
                            <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Cron Command
                                </label>
                                <div class="relative">
                                    <code id="cronCommand" class="block w-full p-3 pr-12 bg-gray-900 text-green-400 text-sm rounded-md font-mono overflow-x-auto whitespace-nowrap">{{ $cronCommand }}</code>
                                    <button type="button" onclick="copyCronCommand()" 
                                        class="absolute right-2 top-1/2 -translate-y-1/2 p-2 text-gray-400 hover:text-white transition-colors"
                                        title="Copy to clipboard">
                                        <svg id="copyIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                        </svg>
                                        <svg id="checkIcon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </button>
                                </div>
                                <div id="vpsInstructions" class="{{ ($settings['cron_environment']->value ?? 'vps') == 'vps' ? '' : 'hidden' }}">
                                    <p class="text-xs text-gray-500 mt-2">
                                        Add this line to your crontab. Run <code class="bg-gray-200 dark:bg-gray-600 px-1 rounded">crontab -e</code> to edit.
                                    </p>
                                </div>
                                <div id="hostingInstructions" class="{{ ($settings['cron_environment']->value ?? 'vps') == 'hosting' ? '' : 'hidden' }}">
                                    <p class="text-xs text-gray-500 mt-2">
                                        Add this command in your cPanel/Plesk cron job settings. Set the schedule to run every minute (<code class="bg-gray-200 dark:bg-gray-600 px-1 rounded">* * * * *</code>).
                                    </p>
                                </div>
                            </div>

                            <!-- Path Information -->
                            <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Detected Paths
                                </label>
                                <div class="space-y-2 text-sm">
                                    <div class="flex" style="overflow-wrap:anywhere;">
                                        <span class="text-gray-500 dark:text-gray-400 w-24">Base Path:</span>
                                        <code class="text-gray-700 dark:text-gray-300 font-mono">{{ $paths['base_path'] }}</code>
                                    </div>
                                    <div class="flex" style="overflow-wrap:anywhere;">
                                        <span class="text-gray-500 dark:text-gray-400 w-24">PHP Binary:</span>
                                        <code class="text-gray-700 dark:text-gray-300 font-mono">{{ $paths['php_binary'] }}</code>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-md">
                                Save Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        const cronCommands = {
            vps: {!! json_encode(sprintf('* * * * * cd %s && %s artisan schedule:run >> /dev/null 2>&1', $paths['base_path'], $paths['php_binary'])) !!},
            hosting: {!! json_encode(sprintf('%s %s/artisan schedule:run >> /dev/null 2>&1', $paths['php_binary'], $paths['base_path'])) !!}
        };

        function updateCronCommand() {
            const environment = document.querySelector('input[name="cron_environment"]:checked').value;
            document.getElementById('cronCommand').textContent = cronCommands[environment];
            
            document.getElementById('vpsInstructions').classList.toggle('hidden', environment !== 'vps');
            document.getElementById('hostingInstructions').classList.toggle('hidden', environment !== 'hosting');
        }

        function copyCronCommand() {
            const command = document.getElementById('cronCommand').textContent;
            navigator.clipboard.writeText(command).then(() => {
                const copyIcon = document.getElementById('copyIcon');
                const checkIcon = document.getElementById('checkIcon');
                
                copyIcon.classList.add('hidden');
                checkIcon.classList.remove('hidden');
                
                setTimeout(() => {
                    copyIcon.classList.remove('hidden');
                    checkIcon.classList.add('hidden');
                }, 2000);
            });
        }
    </script>
</x-app-layout>
