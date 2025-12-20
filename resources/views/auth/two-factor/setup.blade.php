<x-app-layout>
    <x-slot name="header">
        {{ __('app.2fa_setup') }}
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

        <div class="bg-white dark:bg-surface-800 rounded-xl border border-surface-200 dark:border-surface-700 shadow-sm">
            <div class="px-6 py-4 border-b border-surface-200 dark:border-surface-700">
                <h3 class="text-lg font-display font-semibold text-surface-900 dark:text-white">
                    {{ __('app.2fa_setup') }}
                </h3>
                <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                    {{ __('app.2fa_setup_instructions') }}
                </p>
            </div>
            <div class="p-6 space-y-6">
                <!-- QR Code Section -->
                <div>
                    <h4 class="text-sm font-medium text-surface-700 dark:text-surface-300 mb-3">
                        {{ __('app.2fa_scan_qr') }}
                    </h4>
                    <div class="flex justify-center p-6 bg-white rounded-xl border-2 border-dashed border-surface-200 dark:border-surface-700">
                        <img src="{{ $qrCodeUrl }}" alt="QR Code" class="w-48 h-48">
                    </div>
                </div>

                <!-- Manual Entry Code -->
                <div>
                    <h4 class="text-sm font-medium text-surface-700 dark:text-surface-300 mb-3">
                        {{ __('app.2fa_manual_entry') }}
                    </h4>
                    <div class="flex items-center gap-2">
                        <code class="flex-1 p-4 bg-surface-100 dark:bg-surface-700 rounded-xl font-mono text-sm break-all select-all text-surface-900 dark:text-surface-100">
                            {{ $secret }}
                        </code>
                        <button type="button" 
                                onclick="copySecret()"
                                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium rounded-lg bg-surface-100 dark:bg-surface-700 text-surface-700 dark:text-surface-200 hover:bg-surface-200 dark:hover:bg-surface-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-surface-500 transition-all duration-150">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Verification Form -->
                <form method="POST" action="{{ route('two-factor.enable') }}" class="pt-4 border-t border-surface-200 dark:border-surface-700">
                    @csrf
                    
                    <div class="mb-5">
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

                    <div class="flex justify-end">
                        <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium rounded-lg bg-primary-600 text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all duration-150">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            {{ __('app.2fa_enable') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function copySecret() {
            const secret = '{{ $secret }}';
            navigator.clipboard.writeText(secret).then(function() {
                if (typeof window.toast !== 'undefined') {
                    window.toast.success('Copied!');
                }
            });
        }
    </script>
</x-app-layout>
