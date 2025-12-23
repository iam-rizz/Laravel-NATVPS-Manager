<x-app-layout>
    <x-slot name="header">
        {{ __('app.export_vps') }}
    </x-slot>

    <div class="space-y-6">
        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-display font-bold text-surface-900 dark:text-white">
                    {{ __('app.export_vps') }}
                </h1>
                <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                    {{ __('app.export_vps_desc') }}
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

        <!-- Warning Card -->
        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-red-800 dark:text-red-200">
                        {{ __('app.security_warning') }}
                    </h3>
                    <div class="mt-2 text-sm text-red-700 dark:text-red-300 space-y-2">
                        <p>{{ __('app.export_security_warning_1') }}</p>
                        <p>{{ __('app.export_security_warning_2') }}</p>
                        <ul class="list-disc list-inside ml-2 space-y-1">
                            <li>{{ __('app.export_security_tip_1') }}</li>
                            <li>{{ __('app.export_security_tip_2') }}</li>
                            <li>{{ __('app.export_security_tip_3') }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Export Info Card -->
        <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 shadow-sm">
            <div class="p-6">
                <h3 class="text-lg font-medium text-surface-900 dark:text-white mb-4">{{ __('app.export_info') }}</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div class="bg-surface-50 dark:bg-surface-700/50 rounded-lg p-4">
                        <div class="flex items-center gap-3">
                            <div class="flex-shrink-0 w-10 h-10 bg-primary-100 dark:bg-primary-900/30 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-surface-500 dark:text-surface-400">{{ __('app.total_vps_to_export') }}</p>
                                <p class="text-2xl font-bold text-surface-900 dark:text-white">{{ $vpsCount }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-surface-50 dark:bg-surface-700/50 rounded-lg p-4">
                        <div class="flex items-center gap-3">
                            <div class="flex-shrink-0 w-10 h-10 bg-amber-100 dark:bg-amber-900/30 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-surface-500 dark:text-surface-400">{{ __('app.data_included') }}</p>
                                <p class="text-sm font-medium text-surface-900 dark:text-white">{{ __('app.ssh_credentials_included') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border-t border-surface-200 dark:border-surface-700 pt-6">
                    <h4 class="text-sm font-medium text-surface-900 dark:text-white mb-3">{{ __('app.data_to_export') }}</h4>
                    <ul class="text-sm text-surface-600 dark:text-surface-400 space-y-1">
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Hostname, VPS ID
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Server name & IP (for matching)
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            User email (for matching)
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <span class="text-red-600 dark:text-red-400 font-medium">SSH Username, Password, Port (PLAIN TEXT)</span>
                        </li>
                    </ul>
                </div>

                <!-- Confirmation -->
                <form action="{{ route('vps.export.download') }}" method="POST" class="mt-6 pt-6 border-t border-surface-200 dark:border-surface-700">
                    @csrf
                    <label class="flex items-start gap-3 mb-6 cursor-pointer">
                        <input type="checkbox" name="confirm" required class="mt-0.5 h-4 w-4 text-primary-600 border-surface-300 rounded focus:ring-primary-500">
                        <span class="text-sm text-surface-700 dark:text-surface-300">
                            {{ __('app.export_confirm_checkbox') }}
                        </span>
                    </label>

                    <div class="flex justify-end gap-3">
                        <a href="{{ route('vps.index') }}" 
                           class="px-4 py-2.5 text-sm font-medium rounded-lg border border-surface-300 dark:border-surface-600 text-surface-700 dark:text-surface-300 bg-white dark:bg-surface-800 hover:bg-surface-50 dark:hover:bg-surface-700 transition-colors">
                            {{ __('app.cancel') }}
                        </a>
                        <button type="submit" 
                                class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium rounded-lg bg-red-600 text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            {{ __('app.download_export') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
