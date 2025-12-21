<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Edit Email Template: {{ $template->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6">
                <a href="{{ route('settings.email-templates.index') }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400">
                    ← Back to Email Templates
                </a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Edit Form -->
                <div class="lg:col-span-2">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <form action="{{ route('settings.email-templates.update', $template) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <!-- Name -->
                                <div class="mb-6">
                                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Template Name
                                    </label>
                                    <input type="text" name="name" id="name" 
                                        value="{{ old('name', $template->name) }}"
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Subject -->
                                <div class="mb-6">
                                    <label for="subject" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Email Subject
                                    </label>
                                    <input type="text" name="subject" id="subject" 
                                        value="{{ old('subject', $template->subject) }}"
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('subject')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Body -->
                                <div class="mb-6">
                                    <label for="body" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Email Body (HTML)
                                    </label>
                                    <textarea name="body" id="body" rows="20"
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-mono text-sm">{{ old('body', $template->body) }}</textarea>
                                    @error('body')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Active -->
                                <div class="mb-6">
                                    <label class="flex items-center">
                                        <input type="checkbox" name="is_active" value="1" 
                                            {{ $template->is_active ? 'checked' : '' }}
                                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Active</span>
                                    </label>
                                </div>

                                <div class="flex justify-between">
                                    <a href="{{ route('settings.email-templates.preview', $template) }}" 
                                        target="_blank"
                                        class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-md">
                                        Preview
                                    </a>
                                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-md">
                                        Save Template
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Variables Sidebar -->
                <div class="lg:col-span-1">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg sticky top-6">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Available Variables</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                Click to copy. Use these variables in subject or body.
                            </p>
                            <div class="space-y-2">
                                @if($template->variables)
                                    @foreach($template->variables as $variable)
                                        <button type="button" 
                                            onclick="copyVariable('{{ $variable }}')"
                                            class="w-full text-left px-3 py-2 bg-gray-100 dark:bg-gray-700 rounded-md text-sm font-mono text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                                            @{{{{ $variable }}}}
                                        </button>
                                    @endforeach
                                @endif
                                <button type="button" 
                                    onclick="copyVariable('app_name')"
                                    class="w-full text-left px-3 py-2 bg-indigo-100 dark:bg-indigo-900 rounded-md text-sm font-mono text-indigo-700 dark:text-indigo-300 hover:bg-indigo-200 dark:hover:bg-indigo-800 transition">
                                    @{{app_name}} (global)
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function copyVariable(variable) {
            const text = '{{' + variable + '}}';
            navigator.clipboard.writeText(text).then(() => {
                if (window.toast) {
                    window.toast.success('Copied: ' + text);
                }
            });
        }
    </script>
</x-app-layout>
