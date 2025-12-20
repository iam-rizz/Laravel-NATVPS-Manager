<x-guest-layout>
    <div class="p-6 sm:p-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h2 class="text-2xl font-display font-bold text-surface-900 dark:text-white">
                {{ __('app.welcome_back') ?? 'Welcome back' }}
            </h2>
            <p class="mt-2 text-sm text-surface-500 dark:text-surface-400">
                {{ __('app.login_subtitle') ?? 'Sign in to your account to continue' }}
            </p>
        </div>

        <!-- Session Status -->
        @if (session('status'))
            <div class="mb-6 p-4 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800">
                <p class="text-sm text-green-700 dark:text-green-300">{{ session('status') }}</p>
            </div>
        @endif

        <!-- Language Switcher -->
        <div class="flex justify-center mb-6 gap-2">
            <a href="{{ route('language.switch', 'en') }}" 
               class="px-4 py-2 text-sm rounded-lg transition-all duration-150 {{ app()->getLocale() == 'en' ? 'bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300 font-medium' : 'bg-surface-100 dark:bg-surface-700 text-surface-600 dark:text-surface-300 hover:bg-surface-200 dark:hover:bg-surface-600' }}">
                🇺🇸 English
            </a>
            <a href="{{ route('language.switch', 'id') }}" 
               class="px-4 py-2 text-sm rounded-lg transition-all duration-150 {{ app()->getLocale() == 'id' ? 'bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300 font-medium' : 'bg-surface-100 dark:bg-surface-700 text-surface-600 dark:text-surface-300 hover:bg-surface-200 dark:hover:bg-surface-600' }}">
                🇮🇩 Indonesia
            </a>
        </div>

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <!-- Email Address -->
            <div>
                <label for="email" class="form-label">
                    {{ __('app.email') }}
                </label>
                <input id="email" 
                       class="form-input @error('email') error @enderror" 
                       type="email" 
                       name="email" 
                       value="{{ old('email') }}" 
                       required 
                       autofocus 
                       autocomplete="username"
                       placeholder="you@example.com" />
                @error('email')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="form-label">
                    {{ __('app.password') }}
                </label>
                <input id="password" 
                       class="form-input @error('password') error @enderror"
                       type="password"
                       name="password"
                       required 
                       autocomplete="current-password"
                       placeholder="••••••••" />
                @error('password')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <!-- Remember Me -->
            <div class="flex items-center justify-between">
                <label for="remember_me" class="inline-flex items-center cursor-pointer">
                    <input id="remember_me" 
                           type="checkbox" 
                           class="w-4 h-4 rounded border-surface-300 dark:border-surface-600 text-primary-600 focus:ring-primary-500 dark:bg-surface-700 dark:focus:ring-primary-400 dark:focus:ring-offset-surface-800" 
                           name="remember">
                    <span class="ml-2 text-sm text-surface-600 dark:text-surface-400">{{ __('app.remember_me') }}</span>
                </label>
                <a href="{{ route('password.request') }}" class="text-sm text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300">
                    {{ __('app.forgot_password') }}
                </a>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary w-full">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                </svg>
                {{ __('app.login_button') }}
            </button>
        </form>
    </div>
</x-guest-layout>
