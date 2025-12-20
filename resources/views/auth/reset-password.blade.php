<x-guest-layout>
    <div class="p-6 sm:p-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h2 class="text-2xl font-display font-bold text-surface-900 dark:text-white">
                {{ __('app.reset_password_title') }}
            </h2>
            <p class="mt-2 text-sm text-surface-500 dark:text-surface-400">
                {{ __('app.reset_password_desc') }}
            </p>
        </div>

        <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">

            <!-- Email Address -->
            <div>
                <label for="email" class="form-label">{{ __('app.email') }}</label>
                <input id="email" 
                       class="form-input @error('email') error @enderror" 
                       type="email" 
                       name="email" 
                       value="{{ old('email', $email) }}" 
                       required 
                       autofocus />
                @error('email')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="form-label">{{ __('app.new_password') }}</label>
                <input id="password" 
                       class="form-input @error('password') error @enderror"
                       type="password"
                       name="password"
                       required
                       placeholder="••••••••" />
                @error('password')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div>
                <label for="password_confirmation" class="form-label">{{ __('app.confirm_password') }}</label>
                <input id="password_confirmation" 
                       class="form-input"
                       type="password"
                       name="password_confirmation"
                       required
                       placeholder="••••••••" />
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary w-full">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                </svg>
                {{ __('app.reset_password') }}
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
