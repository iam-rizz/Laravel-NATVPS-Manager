<x-guest-layout>
    <div class="p-6 sm:p-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="w-16 h-16 mx-auto bg-gradient-to-br from-primary-500 to-primary-700 rounded-2xl flex items-center justify-center shadow-lg shadow-primary-500/30 mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
            <h2 class="text-2xl font-display font-bold text-surface-900 dark:text-white">
                {{ __('app.2fa_challenge_title') }}
            </h2>
            <p class="mt-2 text-sm text-surface-500 dark:text-surface-400">
                {{ __('app.2fa_challenge_desc') }}
            </p>
        </div>

        <!-- TOTP Code Form -->
        <form method="POST" action="{{ route('two-factor.verify') }}" id="totp-form" class="space-y-5">
            @csrf

            <div>
                <label for="code" class="block text-sm font-medium text-surface-700 dark:text-surface-200 mb-2">
                    {{ __('app.2fa_enter_code') }}
                </label>
                <input id="code" 
                       type="text" 
                       name="code" 
                       maxlength="6"
                       pattern="[0-9]{6}"
                       inputmode="numeric"
                       autocomplete="one-time-code"
                       class="w-full px-4 py-3 rounded-xl text-sm bg-surface-50 dark:bg-surface-800/50 border-2 border-surface-200 dark:border-surface-600 text-surface-900 dark:text-surface-100 placeholder-surface-400 dark:placeholder-surface-500 focus:border-primary-500 dark:focus:border-primary-400 focus:ring-4 focus:ring-primary-500/10 dark:focus:ring-primary-400/10 focus:bg-white dark:focus:bg-surface-800 transition-all duration-200 shadow-sm text-center text-2xl tracking-[0.5em] font-mono @error('code') border-red-500 dark:border-red-400 @enderror"
                       placeholder="000000"
                       required 
                       autofocus />
                @error('code')
                    <p class="text-sm text-red-600 dark:text-red-400 mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium rounded-lg bg-primary-600 text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all duration-150">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ __('app.2fa_verify') }}
            </button>
        </form>

        <!-- Divider -->
        <div class="relative my-6">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-surface-200 dark:border-surface-700"></div>
            </div>
            <div class="relative flex justify-center text-sm">
                <span class="px-3 bg-white dark:bg-surface-800 text-surface-500 dark:text-surface-400">
                    {{ __('app.or') }}
                </span>
            </div>
        </div>

        <!-- Recovery Code Toggle -->
        <button type="button" 
                onclick="toggleRecoveryForm()"
                id="toggle-recovery-btn"
                class="w-full text-center text-sm font-medium text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 transition-colors">
            {{ __('app.2fa_use_recovery') }}
        </button>

        <!-- Recovery Code Form (Hidden by default) -->
        <form method="POST" action="{{ route('two-factor.recovery') }}" id="recovery-form" class="hidden space-y-5 mt-4">
            @csrf

            <div>
                <label for="recovery_code" class="block text-sm font-medium text-surface-700 dark:text-surface-200 mb-2">
                    {{ __('app.2fa_enter_recovery') }}
                </label>
                <input id="recovery_code" 
                       type="text" 
                       name="recovery_code" 
                       class="w-full px-4 py-3 rounded-xl text-sm bg-surface-50 dark:bg-surface-800/50 border-2 border-surface-200 dark:border-surface-600 text-surface-900 dark:text-surface-100 placeholder-surface-400 dark:placeholder-surface-500 focus:border-primary-500 dark:focus:border-primary-400 focus:ring-4 focus:ring-primary-500/10 dark:focus:ring-primary-400/10 focus:bg-white dark:focus:bg-surface-800 transition-all duration-200 shadow-sm font-mono tracking-wider @error('recovery_code') border-red-500 dark:border-red-400 @enderror"
                       placeholder="XXXX-XXXX-XXXX"
                       required />
                @error('recovery_code')
                    <p class="text-sm text-red-600 dark:text-red-400 mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium rounded-lg bg-primary-600 text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all duration-150">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ __('app.2fa_verify') }}
            </button>
        </form>

        <!-- Back to TOTP Toggle (Hidden by default) -->
        <button type="button" 
                onclick="toggleRecoveryForm()"
                id="toggle-totp-btn"
                class="hidden w-full text-center text-sm font-medium text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 transition-colors mt-4">
            {{ __('app.2fa_use_totp') }}
        </button>
    </div>

    <script>
        function toggleRecoveryForm() {
            const totpForm = document.getElementById('totp-form');
            const recoveryForm = document.getElementById('recovery-form');
            const toggleRecoveryBtn = document.getElementById('toggle-recovery-btn');
            const toggleTotpBtn = document.getElementById('toggle-totp-btn');

            if (recoveryForm.classList.contains('hidden')) {
                totpForm.classList.add('hidden');
                recoveryForm.classList.remove('hidden');
                toggleRecoveryBtn.classList.add('hidden');
                toggleTotpBtn.classList.remove('hidden');
                document.getElementById('recovery_code').focus();
            } else {
                totpForm.classList.remove('hidden');
                recoveryForm.classList.add('hidden');
                toggleRecoveryBtn.classList.remove('hidden');
                toggleTotpBtn.classList.add('hidden');
                document.getElementById('code').focus();
            }
        }
    </script>
</x-guest-layout>
