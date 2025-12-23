<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight" style="font-size: 16px;">
            General Settings
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Settings Navigation -->
            @include('admin.settings.partials.nav')

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('settings.general.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- App Name -->
                        <div class="mb-6">
                            <label for="app_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Application Name
                            </label>
                            <input type="text" name="app_name" id="app_name" 
                                value="{{ old('app_name', $settings['app_name']->value ?? '') }}"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('app_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Timezone -->
                        <div class="mb-6">
                            <label for="app_timezone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Timezone
                            </label>
                            <select name="app_timezone" id="app_timezone"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @foreach(timezone_identifiers_list() as $tz)
                                    <option value="{{ $tz }}" {{ ($settings['app_timezone']->value ?? 'Asia/Jakarta') == $tz ? 'selected' : '' }}>
                                        {{ $tz }}
                                    </option>
                                @endforeach
                            </select>
                            @error('app_timezone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- App Logo -->
                        <div class="mb-6">
                            <label for="app_logo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Application Logo
                            </label>
                            @if($settings['app_logo']->value ?? false)
                                <div class="mb-2">
                                    <img src="{{ Storage::url($settings['app_logo']->value) }}" alt="Current Logo" class="h-16">
                                </div>
                            @endif
                            <input type="file" name="app_logo" id="app_logo" accept="image/*"
                                class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            <p class="mt-1 text-sm text-gray-500">PNG, JPG, JPEG, or SVG. Max 2MB.</p>
                            @error('app_logo')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Favicon -->
                        <div class="mb-6">
                            <label for="app_favicon" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Favicon
                            </label>
                            @if($settings['app_favicon']->value ?? false)
                                <div class="mb-2">
                                    <img src="{{ Storage::url($settings['app_favicon']->value) }}" alt="Current Favicon" class="h-8">
                                </div>
                            @endif
                            <input type="file" name="app_favicon" id="app_favicon" accept=".png,.ico"
                                class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            <p class="mt-1 text-sm text-gray-500">PNG or ICO. Max 1MB.</p>
                            @error('app_favicon')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-md">
                                Save Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
