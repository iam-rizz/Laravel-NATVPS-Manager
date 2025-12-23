<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight" style="font-size: 16px;">
            Notification Settings
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @include('admin.settings.partials.nav')

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                        Configure which email notifications should be sent to users. Make sure email is enabled in Mail Settings.
                    </p>

                    <form action="{{ route('settings.notifications.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">VPS Notifications</h3>
                        <div class="space-y-4 mb-8">
                            <!-- VPS Assigned -->
                            <div class="flex items-start p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <div class="flex items-center h-5">
                                    <input type="checkbox" name="notify_vps_assigned" value="1" 
                                        {{ ($settings['notify_vps_assigned']->value ?? '0') == '1' ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                </div>
                                <div class="ml-3">
                                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">VPS Assigned</label>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Send email when a VPS is assigned to a user</p>
                                </div>
                            </div>

                            <!-- VPS Unassigned -->
                            <div class="flex items-start p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <div class="flex items-center h-5">
                                    <input type="checkbox" name="notify_vps_unassigned" value="1" 
                                        {{ ($settings['notify_vps_unassigned']->value ?? '0') == '1' ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                </div>
                                <div class="ml-3">
                                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">VPS Unassigned</label>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Send email when a VPS is removed from a user</p>
                                </div>
                            </div>

                            <!-- VPS Power Actions -->
                            <div class="flex items-start p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <div class="flex items-center h-5">
                                    <input type="checkbox" name="notify_vps_power_action" value="1" 
                                        {{ ($settings['notify_vps_power_action']->value ?? '0') == '1' ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                </div>
                                <div class="ml-3">
                                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">VPS Power Actions</label>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Send email when VPS is started, stopped, or restarted</p>
                                </div>
                            </div>
                        </div>

                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Resource Monitoring</h3>
                        <div class="space-y-4 mb-8">
                            <!-- Enable Monitoring -->
                            <div class="flex items-start p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                                <div class="flex items-center h-5">
                                    <input type="checkbox" name="resource_monitor_enabled" value="1" 
                                        {{ ($settings['resource_monitor_enabled']->value ?? '0') == '1' ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                </div>
                                <div class="ml-3">
                                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Enable Automatic Monitoring</label>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Automatically check VPS resource usage on schedule</p>
                                    <p class="text-xs text-blue-600 dark:text-blue-400 mt-1">
                                        ⓘ Requires Laravel scheduler: <code class="bg-blue-100 dark:bg-blue-900 px-1 rounded">* * * * * php artisan schedule:run</code>
                                    </p>
                                </div>
                            </div>

                            <!-- Resource Warning -->
                            <div class="flex items-start p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <div class="flex items-center h-5">
                                    <input type="checkbox" name="notify_resource_warning" value="1" 
                                        {{ ($settings['notify_resource_warning']->value ?? '0') == '1' ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                </div>
                                <div class="ml-3">
                                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Send Resource Warning Emails</label>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Send email to users when VPS resource usage exceeds threshold</p>
                                </div>
                            </div>

                            <!-- Monitoring Interval & Cooldown -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <div>
                                    <label for="resource_monitor_interval" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Check Interval (minutes)
                                    </label>
                                    <select name="resource_monitor_interval" id="resource_monitor_interval"
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="5" {{ ($settings['resource_monitor_interval']->value ?? '15') == '5' ? 'selected' : '' }}>Every 5 minutes</option>
                                        <option value="10" {{ ($settings['resource_monitor_interval']->value ?? '15') == '10' ? 'selected' : '' }}>Every 10 minutes</option>
                                        <option value="15" {{ ($settings['resource_monitor_interval']->value ?? '15') == '15' ? 'selected' : '' }}>Every 15 minutes</option>
                                        <option value="30" {{ ($settings['resource_monitor_interval']->value ?? '15') == '30' ? 'selected' : '' }}>Every 30 minutes</option>
                                        <option value="60" {{ ($settings['resource_monitor_interval']->value ?? '15') == '60' ? 'selected' : '' }}>Every 60 minutes</option>
                                    </select>
                                    <p class="text-xs text-gray-500 mt-1">How often to check resource usage</p>
                                </div>
                                <div>
                                    <label for="resource_warning_cooldown" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Warning Cooldown
                                    </label>
                                    <select name="resource_warning_cooldown" id="resource_warning_cooldown"
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="15" {{ ($settings['resource_warning_cooldown']->value ?? '60') == '15' ? 'selected' : '' }}>15 minutes</option>
                                        <option value="30" {{ ($settings['resource_warning_cooldown']->value ?? '60') == '30' ? 'selected' : '' }}>30 minutes</option>
                                        <option value="45" {{ ($settings['resource_warning_cooldown']->value ?? '60') == '45' ? 'selected' : '' }}>45 minutes</option>
                                        <option value="60" {{ ($settings['resource_warning_cooldown']->value ?? '60') == '60' ? 'selected' : '' }}>1 hour</option>
                                        <option value="90" {{ ($settings['resource_warning_cooldown']->value ?? '60') == '90' ? 'selected' : '' }}>1.5 hours</option>
                                        <option value="120" {{ ($settings['resource_warning_cooldown']->value ?? '60') == '120' ? 'selected' : '' }}>2 hours</option>
                                        <option value="180" {{ ($settings['resource_warning_cooldown']->value ?? '60') == '180' ? 'selected' : '' }}>3 hours</option>
                                        <option value="240" {{ ($settings['resource_warning_cooldown']->value ?? '60') == '240' ? 'selected' : '' }}>4 hours</option>
                                        <option value="360" {{ ($settings['resource_warning_cooldown']->value ?? '60') == '360' ? 'selected' : '' }}>6 hours</option>
                                        <option value="720" {{ ($settings['resource_warning_cooldown']->value ?? '60') == '720' ? 'selected' : '' }}>12 hours</option>
                                        <option value="1440" {{ ($settings['resource_warning_cooldown']->value ?? '60') == '1440' ? 'selected' : '' }}>24 hours</option>
                                    </select>
                                    <p class="text-xs text-gray-500 mt-1">Minimum time between warning emails for same VPS</p>
                                </div>
                            </div>

                            <!-- Threshold Settings -->
                            <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Warning Thresholds</h4>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label for="resource_warning_cpu_threshold" class="block text-sm text-gray-600 dark:text-gray-400 mb-2">
                                            CPU Threshold (%)
                                        </label>
                                        <input type="number" name="resource_warning_cpu_threshold" id="resource_warning_cpu_threshold" 
                                            value="{{ $settings['resource_warning_cpu_threshold']->value ?? '90' }}"
                                            min="1" max="100"
                                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>
                                    <div>
                                        <label for="resource_warning_ram_threshold" class="block text-sm text-gray-600 dark:text-gray-400 mb-2">
                                            RAM Threshold (%)
                                        </label>
                                        <input type="number" name="resource_warning_ram_threshold" id="resource_warning_ram_threshold" 
                                            value="{{ $settings['resource_warning_ram_threshold']->value ?? '90' }}"
                                            min="1" max="100"
                                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>
                                    <div>
                                        <label for="resource_warning_disk_threshold" class="block text-sm text-gray-600 dark:text-gray-400 mb-2">
                                            Disk Threshold (%)
                                        </label>
                                        <input type="number" name="resource_warning_disk_threshold" id="resource_warning_disk_threshold" 
                                            value="{{ $settings['resource_warning_disk_threshold']->value ?? '90' }}"
                                            min="1" max="100"
                                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">User Notifications</h3>
                        <div class="space-y-4">
                            <!-- Welcome Email -->
                            <div class="flex items-start p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <div class="flex items-center h-5">
                                    <input type="checkbox" name="notify_welcome" value="1" 
                                        {{ ($settings['notify_welcome']->value ?? '0') == '1' ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                </div>
                                <div class="ml-3">
                                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Welcome Email</label>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Send welcome email to newly created users</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end mt-6">
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-md">
                                Save Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
