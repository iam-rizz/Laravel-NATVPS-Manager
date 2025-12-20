<x-guest-layout>
    <div class="p-6 sm:p-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h2 class="text-2xl font-display font-bold text-surface-900 dark:text-white">
                {{ __('app.forgot_password_title') }}
            </h2>
            <p class="mt-2 text-sm text-surface-500 dark:text-surface-400">
                {{ __('app.forgot_password_desc') }}
            </p>
        </div>

        <!-- Status Message -->
        @if (session('status'))
            <div class="mb-6 p-4 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800">
                <p class="text-sm text-green-700 dark:text-green-300">{{ session('status') }}</p>
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
            @csrf

            <!-- Email Address -->
            <div>
                <label for="email" class="form-label">{{ __('app.email') }}</label>
                <input id="email" 
                       class="form-input @error('email') error @enderror" 
                       type="email" 
                       name="email" 
                       value="{{ old('email') }}" 
                       required 
                       autofocus
                       placeholder="you@example.com" />
                @error('email')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary w-full">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                {{ __('app.send_reset_link') }}
            </button>

            <!-- Back to Login -->
            <div class="text-center">
                <a href="{{ route('login') }}" class="text-sm text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300">
                    ← {{ __('app.back_to_login') }}
                </a>
            </div>
        </form>
    </div>
</x-guest-layout>
