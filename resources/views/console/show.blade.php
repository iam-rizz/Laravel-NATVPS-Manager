<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-2">
            <a href="{{ route('console.index') }}" class="text-surface-500 hover:text-surface-700 dark:text-surface-400 dark:hover:text-surface-200">
                {{ __('app.console') }}
            </a>
            <svg class="w-4 h-4 text-surface-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span>{{ $natVps->hostname }}</span>
        </div>
    </x-slot>

    <div 
        x-data="consoleEmbed(@js($natVps->id), @js(csrf_token()))"
        class="space-y-4"
    >
        {{-- VPS Info Bar --}}
        <div class="card">
            <div class="card-body py-3">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center space-x-2">
                            <span 
                                :class="{
                                    'bg-green-500': connectionStatus === 'connected',
                                    'bg-yellow-500': connectionStatus === 'connecting',
                                    'bg-red-500': connectionStatus === 'disconnected' || connectionStatus === 'error',
                                    'bg-gray-400': connectionStatus === 'idle'
                                }"
                                class="w-2 h-2 rounded-full"
                            ></span>
                            <span class="text-sm text-surface-600 dark:text-surface-400" x-text="statusMessage || '{{ __('app.ready') }}'"></span>
                        </div>
                        <span class="text-surface-300 dark:text-surface-600">|</span>
                        <span class="text-sm text-surface-500 dark:text-surface-400">
                            {{ __('app.server') }}: {{ $natVps->server?->name ?? '-' }}
                        </span>
                        @if(auth()->user()->isAdmin() && $natVps->user)
                        <span class="text-surface-300 dark:text-surface-600">|</span>
                        <span class="text-sm text-surface-500 dark:text-surface-400">
                            {{ __('app.owner') }}: {{ $natVps->user->name }}
                        </span>
                        @endif
                    </div>
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('vps.show', $natVps) }}" class="btn btn-sm btn-secondary">
                            {{ __('app.vps_details') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Console Tabs & Toolbar --}}
        <div class="card overflow-hidden">
            {{-- Tabs --}}
            <div class="flex flex-wrap items-center px-4 py-2 bg-surface-100 dark:bg-surface-800 border-b border-surface-200 dark:border-surface-700 gap-2" x-ref="tabContainer">
                <button 
                    data-console-tab="vnc"
                    @click="switchTab('vnc')"
                    :class="activeTab === 'vnc' ? 'bg-primary-600 text-white' : 'bg-surface-200 dark:bg-surface-700 text-surface-700 dark:text-surface-300 hover:bg-surface-300 dark:hover:bg-surface-600'"
                    class="px-4 py-2 text-sm font-medium rounded-lg transition-colors flex items-center space-x-2"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <span>VNC</span>
                </button>

                @if(!empty($natVps->ssh_username) && !empty($natVps->ssh_password))
                <button 
                    data-console-tab="ssh"
                    @click="switchTab('ssh')"
                    :class="activeTab === 'ssh' ? 'bg-primary-600 text-white' : 'bg-surface-200 dark:bg-surface-700 text-surface-700 dark:text-surface-300 hover:bg-surface-300 dark:hover:bg-surface-600'"
                    class="px-4 py-2 text-sm font-medium rounded-lg transition-colors flex items-center space-x-2"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span>SSH</span>
                </button>
                @endif

                {{-- VNC Toolbar --}}
                <div x-show="activeTab === 'vnc'" class="flex items-center ml-auto space-x-2 flex-wrap">
                    @include('components.console-toolbar')
                </div>
            </div>

            {{-- Console Container --}}
            <div class="relative bg-gray-900" style="height: 600px;">
                {{-- VNC Container --}}
                <div 
                    x-ref="vncContainer"
                    x-show="activeTab === 'vnc'"
                    class="absolute inset-0"
                ></div>

                {{-- SSH Container --}}
                <div 
                    x-ref="sshContainer"
                    x-show="activeTab === 'ssh'"
                    class="absolute inset-0 p-2"
                ></div>

                {{-- Loading Overlay --}}
                <div 
                    x-show="connectionStatus === 'connecting'"
                    class="absolute inset-0 flex items-center justify-center bg-gray-900/80"
                >
                    <div class="text-center">
                        <svg class="animate-spin h-10 w-10 text-primary-500 mx-auto mb-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <p class="text-gray-400" x-text="statusMessage"></p>
                    </div>
                </div>

                {{-- Error Overlay --}}
                <div 
                    x-show="connectionStatus === 'error'"
                    class="absolute inset-0 flex items-center justify-center bg-gray-900/80"
                >
                    <div class="text-center max-w-md px-4">
                        <svg class="h-12 w-12 text-red-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <p class="text-red-400 mb-4" x-text="statusMessage"></p>
                        <button 
                            @click="reconnect()"
                            class="btn btn-primary"
                        >
                            {{ __('app.retry') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('components.console-scripts')
</x-app-layout>
