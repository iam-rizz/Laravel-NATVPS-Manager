@php
    $recoveryCodes = $recoveryCodes ?? [];
    $remainingCount = $remainingCount ?? 8;
    $codesCount = is_array($recoveryCodes) || $recoveryCodes instanceof Countable ? count($recoveryCodes) : 0;
    $hasNewCodes = $codesCount > 0;
@endphp

<x-app-layout>
    <x-slot name="header">
        {{ __('app.2fa_recovery_codes') }}
    </x-slot>

    <div class="max-w-2xl mx-auto space-y-6">
        <!-- Back Navigation -->
        <div>
            <a href="{{ route('profile.edit') }}" class="inline-flex items-center gap-2 text-sm text-surface-600 dark:text-surface-400 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                {{ __('app.back_to_profile') }}
            </a>
        </div>

        <!-- Status Card -->
        <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 shadow-sm">
            <div class="p-6">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-green-500 to-green-600 flex items-center justify-center shadow-lg shadow-green-500/25">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-display font-semibold text-surface-900 dark:text-white">
                            {{ __('app.2fa_enabled') }}
                        </h3>
                        <p class="text-sm text-surface-500 dark:text-surface-400">
                            {{ __('app.2fa_codes_remaining', ['count' => $remainingCount]) }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recovery Codes Card -->
        <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 shadow-sm">
            <div class="px-6 py-4 border-b border-surface-200 dark:border-surface-700">
                <h3 class="text-lg font-display font-semibold text-surface-900 dark:text-white">
                    {{ __('app.2fa_recovery_codes') }}
                </h3>
                <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                    {{ __('app.2fa_recovery_codes_desc') }}
                </p>
            </div>
            <div class="p-6">
                @if($hasNewCodes)
                    <!-- New codes available - show them -->
                    <div class="p-4 rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 mb-6">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-green-600 dark:text-green-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-sm text-green-700 dark:text-green-300">
                                {{ __('app.2fa_save_codes_warning') ?? 'Save these codes now! They will not be shown again after you leave this page.' }}
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 mb-6" id="recovery-codes-grid">
                        @foreach($recoveryCodes as $index => $code)
                            <div class="p-3 bg-surface-100 dark:bg-surface-700 rounded-xl font-mono text-sm text-center text-surface-700 dark:text-surface-300 select-all">
                                {{ $index + 1 }}. {{ $code }}
                            </div>
                        @endforeach
                    </div>

                    <button type="button" onclick="copyAllCodes()" class="w-full mb-4 inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium rounded-lg bg-primary-600 text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all duration-150">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                        {{ __('app.2fa_copy_codes') ?? 'Copy All Codes' }}
                    </button>
                @else
                    <!-- No new codes - show masked placeholder -->
                    <div class="grid grid-cols-2 gap-3 mb-6">
                        @for($i = 0; $i < $remainingCount; $i++)
                            <div class="p-3 bg-surface-100 dark:bg-surface-700 rounded-xl font-mono text-sm text-center text-surface-500 dark:text-surface-400">
                                {{ $i + 1 }}. ••••-••••-••••
                            </div>
                        @endfor
                    </div>

                    <div class="p-4 rounded-xl bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 mb-6">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-sm text-blue-700 dark:text-blue-300">
                                {{ __('app.2fa_codes_hidden_info') ?? 'Recovery codes are hidden for security. If you need to see them again, regenerate new codes below.' }}
                            </p>
                        </div>
                    </div>
                @endif
                    
                @if($remainingCount <= 3 && $remainingCount > 0)
                    <div class="p-4 rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 mb-6">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <p class="text-sm text-amber-700 dark:text-amber-300">
                                {{ __('app.2fa_codes_warning') }}
                            </p>
                        </div>
                    </div>
                @endif

                <!-- Regenerate Codes Section -->
                <div class="pt-4 border-t border-surface-200 dark:border-surface-700">
                    <h4 class="text-sm font-medium text-surface-900 dark:text-white mb-2">
                        {{ __('app.2fa_regenerate_codes') ?? 'Regenerate Recovery Codes' }}
                    </h4>
                    <p class="text-sm text-surface-500 dark:text-surface-400 mb-4">
                        {{ __('app.2fa_regenerate_codes_desc') ?? 'Generate new recovery codes. This will invalidate all existing codes.' }}
                    </p>
                    
                    <form method="POST" action="{{ route('two-factor.recovery-codes.regenerate') }}" id="regenerate-form">
                        @csrf
                        <div class="mb-4">
                            <label for="password" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">
                                {{ __('app.password') }}
                            </label>
                            <input type="password" name="password" id="password" required
                                class="w-full px-4 py-2.5 rounded-lg border border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-700 text-surface-900 dark:text-white placeholder-surface-400 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors"
                                placeholder="{{ __('app.enter_password') ?? 'Enter your password' }}">
                        </div>
                        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium rounded-lg bg-amber-600 text-white hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition-all duration-150">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            {{ __('app.2fa_regenerate_codes') ?? 'Regenerate Codes' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Disable 2FA Card -->
        <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 shadow-sm">
            <div class="px-6 py-4 border-b border-surface-200 dark:border-surface-700">
                <h3 class="text-lg font-display font-semibold text-red-600 dark:text-red-400">
                    {{ __('app.2fa_disable') ?? 'Disable Two-Factor Authentication' }}
                </h3>
                <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                    {{ __('app.2fa_disable_desc') ?? 'Remove the extra layer of security from your account.' }}
                </p>
            </div>
            <div class="p-6">
                <form method="POST" action="{{ route('two-factor.disable') }}" id="disable-form">
                    @csrf
                    <div class="mb-4">
                        <label for="disable_code" class="block text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">
                            {{ __('app.2fa_code') ?? 'Authentication Code' }}
                        </label>
                        <input type="text" name="code" id="disable_code" required
                            inputmode="numeric" pattern="[0-9]*" maxlength="6" autocomplete="one-time-code"
                            class="w-full px-4 py-2.5 rounded-lg border border-surface-300 dark:border-surface-600 bg-white dark:bg-surface-700 text-surface-900 dark:text-white placeholder-surface-400 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors text-center font-mono text-lg tracking-widest"
                            placeholder="000000">
                        <p class="mt-2 text-xs text-surface-500 dark:text-surface-400">
                            {{ __('app.2fa_enter_code_from_app') ?? 'Enter the 6-digit code from your authenticator app' }}
                        </p>
                    </div>
                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium rounded-lg bg-red-600 text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-150">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                        </svg>
                        {{ __('app.2fa_disable') ?? 'Disable 2FA' }}
                    </button>
                </form>
            </div>
        </div>
    </div>


    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" 
            class="fixed bottom-4 right-4 p-4 rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 shadow-lg">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm text-green-700 dark:text-green-300">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" 
            class="fixed bottom-4 right-4 p-4 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 shadow-lg">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm text-red-700 dark:text-red-300">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    @push('scripts')
    <script>
        function copyAllCodes() {
            const codesGrid = document.getElementById('recovery-codes-grid');
            if (!codesGrid) return;
            
            const codes = [];
            codesGrid.querySelectorAll('div').forEach(div => {
                codes.push(div.textContent.trim());
            });
            
            const codesText = codes.join('\n');
            
            navigator.clipboard.writeText(codesText).then(() => {
                // Show success notification
                const notification = document.createElement('div');
                notification.className = 'fixed bottom-4 right-4 p-4 rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 shadow-lg z-50';
                notification.innerHTML = `
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-sm text-green-700 dark:text-green-300">{{ __('app.2fa_codes_copied') ?? 'Recovery codes copied to clipboard!' }}</p>
                    </div>
                `;
                document.body.appendChild(notification);
                
                setTimeout(() => {
                    notification.remove();
                }, 3000);
            }).catch(err => {
                console.error('Failed to copy codes:', err);
            });
        }
    </script>
    @endpush
</x-app-layout>
