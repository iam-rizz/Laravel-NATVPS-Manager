<x-app-layout>
    <x-slot name="header">
        {{ __('app.profile') }}
    </x-slot>

    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Two-Factor Authentication -->
        <div class="card">
            <div class="card-header">
                <h3 class="text-lg font-display font-semibold text-surface-900 dark:text-white">
                    {{ __('app.2fa_setup') }}
                </h3>
                <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                    {{ __('app.2fa_profile_desc') }}
                </p>
            </div>
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center {{ $user->hasTwoFactorEnabled() ? 'bg-green-100 dark:bg-green-900/30' : 'bg-surface-100 dark:bg-surface-800' }}">
                            <svg class="w-6 h-6 {{ $user->hasTwoFactorEnabled() ? 'text-green-600 dark:text-green-400' : 'text-surface-400 dark:text-surface-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-surface-900 dark:text-white">
                                {{ $user->hasTwoFactorEnabled() ? __('app.2fa_enabled') : __('app.2fa_disabled') }}
                            </p>
                            <p class="text-sm text-surface-500 dark:text-surface-400">
                                {{ $user->hasTwoFactorEnabled() ? __('app.2fa_enabled_desc') : __('app.2fa_disabled_desc') }}
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('two-factor.setup') }}" class="btn {{ $user->hasTwoFactorEnabled() ? 'btn-secondary' : 'btn-primary' }}">
                        {{ $user->hasTwoFactorEnabled() ? __('app.manage') : __('app.2fa_enable') }}
                    </a>
                </div>
            </div>
        </div>
        <!-- Profile Information -->
        <div class="card">
            <div class="card-header">
                <h3 class="text-lg font-display font-semibold text-surface-900 dark:text-white">
                    {{ __('app.profile_information') }}
                </h3>
                <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                    {{ __('app.profile_information_desc') }}
                </p>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('profile.update') }}" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="name" class="form-label">{{ __('app.name') }}</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" 
                               class="form-input @error('name') error @enderror" required>
                        @error('name')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label">{{ __('app.email') }}</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" 
                               class="form-input @error('email') error @enderror" required>
                        @error('email')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="btn btn-primary">
                            {{ __('app.save') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Change Password -->
        <div class="card">
            <div class="card-header">
                <h3 class="text-lg font-display font-semibold text-surface-900 dark:text-white">
                    {{ __('app.change_password') }}
                </h3>
                <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                    {{ __('app.change_password_desc') }}
                </p>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('profile.password') }}" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="current_password" class="form-label">{{ __('app.current_password') }}</label>
                        <input type="password" id="current_password" name="current_password" 
                               class="form-input @error('current_password') error @enderror" required>
                        @error('current_password')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">{{ __('app.new_password') }}</label>
                        <input type="password" id="password" name="password" 
                               class="form-input @error('password') error @enderror" required>
                        @error('password')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation" class="form-label">{{ __('app.confirm_password') }}</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" 
                               class="form-input" required>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="btn btn-primary">
                            {{ __('app.update_password') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
