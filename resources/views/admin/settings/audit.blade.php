<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Audit Log Settings
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @include('admin.settings.partials.nav')

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                        Configure audit log retention and cleanup settings.
                    </p>

                    <form action="{{ route('settings.audit.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Retention Policy</h3>
                        <div class="space-y-4 mb-8">
                            <!-- Retention Days -->
                            <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <label for="audit_log_retention_days" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Retention Period (days)
                                </label>
                                <select name="audit_log_retention_days" id="audit_log_retention_days"
                                    class="w-full md:w-1/2 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="0" {{ ($settings['audit_log_retention_days']->value ?? '90') == '0' ? 'selected' : '' }}>Keep forever</option>
                                    <option value="30" {{ ($settings['audit_log_retention_days']->value ?? '90') == '30' ? 'selected' : '' }}>30 days</option>
                                    <option value="60" {{ ($settings['audit_log_retention_days']->value ?? '90') == '60' ? 'selected' : '' }}>60 days</option>
                                    <option value="90" {{ ($settings['audit_log_retention_days']->value ?? '90') == '90' ? 'selected' : '' }}>90 days</option>
                                    <option value="180" {{ ($settings['audit_log_retention_days']->value ?? '90') == '180' ? 'selected' : '' }}>180 days</option>
                                    <option value="365" {{ ($settings['audit_log_retention_days']->value ?? '90') == '365' ? 'selected' : '' }}>1 year</option>
                                    <option value="730" {{ ($settings['audit_log_retention_days']->value ?? '90') == '730' ? 'selected' : '' }}>2 years</option>
                                </select>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                                    Audit logs older than this period will be automatically deleted during daily cleanup.
                                </p>
                                @error('audit_log_retention_days')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Info Box -->
                            <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h4 class="text-sm font-medium text-blue-800 dark:text-blue-200">Automatic Cleanup</h4>
                                        <p class="text-sm text-blue-700 dark:text-blue-300 mt-1">
                                            The cleanup runs daily at midnight via the Laravel scheduler. Make sure the scheduler is running:
                                        </p>
                                        <code class="block mt-2 text-xs bg-blue-100 dark:bg-blue-900 px-2 py-1 rounded text-blue-800 dark:text-blue-200">
                                            * * * * * php artisan schedule:run
                                        </code>
                                    </div>
                                </div>
                            </div>

                            <!-- Manual Cleanup -->
                            <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Manual Cleanup</h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">
                                    You can also run the cleanup manually via command line:
                                </p>
                                <code class="block text-xs bg-gray-200 dark:bg-gray-600 px-2 py-1 rounded text-gray-800 dark:text-gray-200">
                                    php artisan audit:cleanup
                                </code>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                    Or with a custom retention period: <code class="bg-gray-200 dark:bg-gray-600 px-1 rounded">php artisan audit:cleanup --days=30</code>
                                </p>
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
</x-app-layout>
