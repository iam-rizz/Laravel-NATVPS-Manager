<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Mail Settings
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @include('admin.settings.partials.nav')

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('settings.mail.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Enable Mail -->
                        <div class="mb-6">
                            <label class="flex items-center">
                                <input type="checkbox" name="mail_enabled" value="1" 
                                    {{ ($settings['mail_enabled']->value ?? '0') == '1' ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Enable Email Notifications</span>
                            </label>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- SMTP Host -->
                            <div>
                                <label for="mail_host" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    SMTP Host
                                </label>
                                <input type="text" name="mail_host" id="mail_host" 
                                    value="{{ old('mail_host', $settings['mail_host']->value ?? 'smtp.gmail.com') }}"
                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('mail_host')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- SMTP Port -->
                            <div>
                                <label for="mail_port" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    SMTP Port
                                </label>
                                <input type="number" name="mail_port" id="mail_port" 
                                    value="{{ old('mail_port', $settings['mail_port']->value ?? '587') }}"
                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('mail_port')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- SMTP Username -->
                            <div>
                                <label for="mail_username" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    SMTP Username
                                </label>
                                <input type="text" name="mail_username" id="mail_username" 
                                    value="{{ old('mail_username', $settings['mail_username']->value ?? '') }}"
                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('mail_username')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- SMTP Password -->
                            <div>
                                <label for="mail_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    SMTP Password
                                </label>
                                <input type="password" name="mail_password" id="mail_password" 
                                    placeholder="Leave blank to keep current password"
                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('mail_password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Encryption -->
                            <div>
                                <label for="mail_encryption" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Encryption
                                </label>
                                <select name="mail_encryption" id="mail_encryption"
                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="tls" {{ ($settings['mail_encryption']->value ?? 'tls') == 'tls' ? 'selected' : '' }}>TLS</option>
                                    <option value="ssl" {{ ($settings['mail_encryption']->value ?? '') == 'ssl' ? 'selected' : '' }}>SSL</option>
                                    <option value="null" {{ ($settings['mail_encryption']->value ?? '') == 'null' ? 'selected' : '' }}>None</option>
                                </select>
                                @error('mail_encryption')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- From Address -->
                            <div>
                                <label for="mail_from_address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    From Address
                                </label>
                                <input type="email" name="mail_from_address" id="mail_from_address" 
                                    value="{{ old('mail_from_address', $settings['mail_from_address']->value ?? '') }}"
                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('mail_from_address')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- From Name -->
                            <div>
                                <label for="mail_from_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    From Name
                                </label>
                                <input type="text" name="mail_from_name" id="mail_from_name" 
                                    value="{{ old('mail_from_name', $settings['mail_from_name']->value ?? '') }}"
                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('mail_from_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="flex justify-end mt-6">
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-md">
                                Save Settings
                            </button>
                        </div>
                    </form>

                    <!-- Test Email Section -->
                    <div class="mt-8 pt-8 border-t border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Test Email Configuration</h3>
                        <form action="{{ route('settings.mail.test') }}" method="POST" class="flex gap-4">
                            @csrf
                            <input type="email" name="test_email" placeholder="Enter email address" required
                                class="flex-1 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-md">
                                Send Test Email
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
