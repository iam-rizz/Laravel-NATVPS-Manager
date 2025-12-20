@php
    $recoveryCodes = $recoveryCodes ?? [];
    $codesCount = is_array($recoveryCodes) || $recoveryCodes instanceof Countable ? count($recoveryCodes) : 0;
@endphp

<x-app-layout>
    <x-slot name="header">
        {{ __('app.2fa_recovery_codes') }}
    </x-slot>

    <div class="max-w-2xl mx-auto space-y-6">
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
                            {{ __('app.2fa_codes_remaining', ['count' => $codesCount]) }}
                        </p>
                    </div>
                    <form method="POST" action="{{ route('two-factor.disable') }}" onsubmit="return confirm('{{ __('app.2fa_disable_confirm') }}')">
                        @csrf
                        <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium rounded-lg bg-red-600 text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-150">
                            {{ __('app.2fa_disable') }}
                        </button>
                    </form>
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
                @if($codesCount > 0)
                    <!-- Hidden codes (default view) -->
                    <div id="hidden-codes" class="grid grid-cols-2 gap-3 mb-6">
                        @foreach($recoveryCodes as $index => $code)
                            <div class="p-3 bg-surface-100 dark:bg-surface-700 rounded-xl font-mono text-sm text-center text-surface-700 dark:text-surface-300">
                                {{ $index + 1 }}. ••••••••
                            </div>
                        @endforeach
                    </div>

                    <!-- Visible codes (shown after password confirmation) -->
                    <div id="visible-codes" class="hidden grid grid-cols-2 gap-3 mb-6">
                        @foreach($recoveryCodes as $index => $code)
                            <div class="p-3 bg-surface-100 dark:bg-surface-700 rounded-xl font-mono text-sm text-center text-surface-700 dark:text-surface-300 select-all">
                                {{ $index + 1 }}. {{ $code }}
                            </div>
                        @endforeach
                    </div>

                    <!-- View Codes Button -->
                    <div id="view-codes-section" class="mb-6">
                        <button type="button" 
                                onclick="document.getElementById('view-codes-modal').classList.remove('hidden')"
                                class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium rounded-lg bg-primary-600 text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all duration-150">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            {{ __('app.2fa_view_codes') ?? 'View Recovery Codes' }}
                        </button>
                    </div>

                    <!-- Hide Codes Button (shown when codes are visible) -->
                    <div id="hide-codes-section" class="hidden mb-6">
                        <button type="button" 
                                onclick="hideCodes()"
                                class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium rounded-lg bg-surface-100 dark:bg-surface-700 text-surface-700 dark:text-surface-200 hover:bg-surface-200 dark:hover:bg-surface-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-surface-500 transition-all duration-150">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                            {{ __('app.2fa_hide_codes') ?? 'Hide Recovery Codes' }}
                        </button>
                    </div>
                    
                    @if($codesCount <= 3)
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
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-surface-400 dark:text-surface-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <p class="mt-2 text-sm text-surface-500 dark:text-surface-400">
                            {{ __('app.2fa_no_codes') ?? 'No recovery codes available' }}
                        </p>
                    </div>
                @endif

                <!-- Regenerate Form -->
                <div class="pt-4 border-t border-surface-200 dark:border-surface-700">
                    <h4 class="text-sm font-medium text-surface-700 dark:text-surface-300 mb-2">
                        {{ __('app.2fa_regenerate_codes') }}
                    </h4>
                    <p class="text-sm text-surface-500 dark:text-surface-400 mb-4">
                        {{ __('app.2fa_regenerate_desc') }}
                    </p>
                    
                    <form method="POST" action="{{ route('two-factor.recovery-codes.regenerate') }}">
                        @csrf
                        
                        <div class="mb-4">
                            <label for="password" class="block text-sm font-medium text-surface-700 dark:text-surface-200 mb-2">
                                {{ __('app.2fa_confirm_password') }}
                            </label>
                            <input id="password" 
                                   type="password" 
                                   name="password" 
                                   class="w-full px-4 py-3 rounded-xl text-sm bg-surface-50 dark:bg-surface-800/50 border-2 border-surface-200 dark:border-surface-600 text-surface-900 dark:text-surface-100 placeholder-surface-400 dark:placeholder-surface-500 focus:border-primary-500 dark:focus:border-primary-400 focus:ring-4 focus:ring-primary-500/10 dark:focus:ring-primary-400/10 focus:bg-white dark:focus:bg-surface-800 transition-all duration-200 shadow-sm @error('password') border-red-500 dark:border-red-400 @enderror"
                                   required />
                            @error('password')
                                <p class="text-sm text-red-600 dark:text-red-400 mt-1.5">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium rounded-lg bg-surface-100 dark:bg-surface-700 text-surface-700 dark:text-surface-200 hover:bg-surface-200 dark:hover:bg-surface-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-surface-500 transition-all duration-150">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            {{ __('app.2fa_regenerate_codes') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Back Link -->
        <div class="text-center">
            <a href="{{ route('profile.edit') }}" class="text-sm text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300">
                ← {{ __('app.back') }} {{ __('app.profile') }}
            </a>
        </div>
    </div>

    <!-- View Codes Modal -->
    <div id="view-codes-modal" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-surface-900/75 transition-opacity" onclick="document.getElementById('view-codes-modal').classList.add('hidden')"></div>
            
            <!-- Modal -->
            <div class="relative bg-white dark:bg-surface-800 rounded-xl shadow-xl transform transition-all sm:max-w-md sm:w-full mx-auto border border-surface-200 dark:border-surface-700">
                <div class="px-6 py-4 border-b border-surface-200 dark:border-surface-700">
                    <h3 class="text-lg font-display font-semibold text-surface-900 dark:text-white">
                        {{ __('app.2fa_confirm_view') ?? 'Confirm Password' }}
                    </h3>
                    <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                        {{ __('app.2fa_confirm_view_desc') ?? 'Enter your password to view recovery codes' }}
                    </p>
                </div>
                <form id="view-codes-form" method="POST" action="{{ route('two-factor.recovery-codes.view') }}">
                    @csrf
                    <div class="p-6">
                        <div class="mb-4">
                            <label for="view_password" class="block text-sm font-medium text-surface-700 dark:text-surface-200 mb-2">
                                {{ __('app.password') }}
                            </label>
                            <input id="view_password" 
                                   type="password" 
                                   name="password" 
                                   class="w-full px-4 py-3 rounded-xl text-sm bg-surface-50 dark:bg-surface-800/50 border-2 border-surface-200 dark:border-surface-600 text-surface-900 dark:text-surface-100 placeholder-surface-400 dark:placeholder-surface-500 focus:border-primary-500 dark:focus:border-primary-400 focus:ring-4 focus:ring-primary-500/10 dark:focus:ring-primary-400/10 focus:bg-white dark:focus:bg-surface-800 transition-all duration-200 shadow-sm"
                                   placeholder="••••••••"
                                   required />
                            <p id="view-password-error" class="hidden text-sm text-red-600 dark:text-red-400 mt-1.5"></p>
                        </div>
                    </div>
                    <div class="px-6 py-4 border-t border-surface-200 dark:border-surface-700 flex justify-end gap-3">
                        <button type="button" 
                                onclick="document.getElementById('view-codes-modal').classList.add('hidden')"
                                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium rounded-lg bg-surface-100 dark:bg-surface-700 text-surface-700 dark:text-surface-200 hover:bg-surface-200 dark:hover:bg-surface-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-surface-500 transition-all duration-150">
                            {{ __('app.cancel') ?? 'Cancel' }}
                        </button>
                        <button type="submit" 
                                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium rounded-lg bg-primary-600 text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all duration-150">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            {{ __('app.2fa_view_codes') ?? 'View Codes' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Handle form submission via AJAX
        document.getElementById('view-codes-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const form = this;
            const password = document.getElementById('view_password').value;
            const errorEl = document.getElementById('view-password-error');
            
            fetch(form.action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ password: password })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show codes
                    document.getElementById('hidden-codes').classList.add('hidden');
                    document.getElementById('visible-codes').classList.remove('hidden');
                    document.getElementById('view-codes-section').classList.add('hidden');
                    document.getElementById('hide-codes-section').classList.remove('hidden');
                    document.getElementById('view-codes-modal').classList.add('hidden');
                    document.getElementById('view_password').value = '';
                    errorEl.classList.add('hidden');
                } else {
                    errorEl.textContent = data.message || 'Invalid password';
                    errorEl.classList.remove('hidden');
                }
            })
            .catch(error => {
                errorEl.textContent = 'An error occurred. Please try again.';
                errorEl.classList.remove('hidden');
            });
        });

        function hideCodes() {
            document.getElementById('hidden-codes').classList.remove('hidden');
            document.getElementById('visible-codes').classList.add('hidden');
            document.getElementById('view-codes-section').classList.remove('hidden');
            document.getElementById('hide-codes-section').classList.add('hidden');
        }
    </script>
</x-app-layout>
