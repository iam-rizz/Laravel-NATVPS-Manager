@props([
    'natVps',
    'vncAvailable' => true,
    'sshAvailable' => true,
])

{{-- 
    Console Modal Component
    
    Provides VNC and SSH console access for VPS management.
    Requirements: 1.1, 1.4, 4.1, 5.2
--}}

<div 
    x-data="consoleModal(@js($natVps->id), @js(csrf_token()))"
    x-show="isOpen"
    x-cloak
    @keydown.escape.window="close()"
    class="fixed inset-0 z-50 overflow-hidden"
    aria-labelledby="console-modal-title"
    role="dialog"
    aria-modal="true"
>
    {{-- Backdrop --}}
    <div 
        x-show="isOpen"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="close()"
        class="fixed inset-0 bg-gray-900/80 backdrop-blur-sm transition-opacity"
    ></div>

    {{-- Modal Content --}}
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div 
            x-show="isOpen"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            :class="isFullscreen ? 'fixed inset-0 rounded-none' : 'w-full max-w-6xl h-[85vh] rounded-lg'"
            class="bg-gray-900 shadow-2xl flex flex-col overflow-hidden transform transition-all"
            @click.stop
        >
            {{-- Header --}}
            <div class="flex items-center justify-between px-4 py-3 bg-gray-800 border-b border-gray-700">
                <div class="flex items-center space-x-4">
                    <h3 id="console-modal-title" class="text-lg font-medium text-white">
                        {{ __('app.console') }}: {{ $natVps->hostname }}
                    </h3>
                    
                    {{-- Connection Status --}}
                    <div class="flex items-center space-x-2">
                        <span 
                            :class="{
                                'bg-green-500': connectionStatus === 'connected',
                                'bg-yellow-500': connectionStatus === 'connecting',
                                'bg-red-500': connectionStatus === 'disconnected' || connectionStatus === 'error',
                                'bg-gray-500': connectionStatus === 'idle'
                            }"
                            class="w-2 h-2 rounded-full"
                        ></span>
                        <span class="text-sm text-gray-400" x-text="statusMessage"></span>
                    </div>
                </div>

                <div class="flex items-center space-x-2">
                    {{-- Fullscreen Toggle --}}
                    <button 
                        @click="toggleFullscreen()"
                        class="p-2 text-gray-400 hover:text-white hover:bg-gray-700 rounded-lg transition-colors"
                        :title="isFullscreen ? '{{ __('app.exit_fullscreen') }}' : '{{ __('app.fullscreen') }}'"
                    >
                        <svg x-show="!isFullscreen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                        </svg>
                        <svg x-show="isFullscreen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9V4.5M9 9H4.5M9 9L3.75 3.75M9 15v4.5M9 15H4.5M9 15l-5.25 5.25M15 9h4.5M15 9V4.5M15 9l5.25-5.25M15 15h4.5M15 15v4.5m0-4.5l5.25 5.25"/>
                        </svg>
                    </button>

                    {{-- Close Button --}}
                    <button 
                        @click="close()"
                        class="p-2 text-gray-400 hover:text-white hover:bg-gray-700 rounded-lg transition-colors"
                        title="{{ __('app.close') }}"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Tabs --}}
            <div class="flex items-center px-4 py-2 bg-gray-800 border-b border-gray-700 space-x-2" x-ref="tabContainer">
                @if($vncAvailable)
                <button 
                    data-console-tab="vnc"
                    @click="switchTab('vnc')"
                    :class="activeTab === 'vnc' ? 'bg-indigo-600 text-white' : 'bg-gray-700 text-gray-300 hover:bg-gray-600'"
                    class="px-4 py-2 text-sm font-medium rounded-lg transition-colors flex items-center space-x-2"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <span>VNC Console</span>
                </button>
                @endif

                @if($sshAvailable)
                <button 
                    data-console-tab="ssh"
                    @click="switchTab('ssh')"
                    :class="activeTab === 'ssh' ? 'bg-indigo-600 text-white' : 'bg-gray-700 text-gray-300 hover:bg-gray-600'"
                    class="px-4 py-2 text-sm font-medium rounded-lg transition-colors flex items-center space-x-2"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span>SSH Terminal</span>
                </button>
                @endif
            </div>

            {{-- Toolbar (VNC specific) --}}
            <div x-show="activeTab === 'vnc'" class="flex items-center px-4 py-2 bg-gray-800 border-b border-gray-700 space-x-2 flex-wrap">
                {{-- Special Keys Dropdown --}}
                <div class="relative" x-data="{ open: false }">
                    <button 
                        @click="open = !open"
                        :disabled="connectionStatus !== 'connected'"
                        class="px-3 py-1.5 text-xs font-medium bg-gray-700 hover:bg-gray-600 disabled:bg-gray-600 disabled:cursor-not-allowed text-gray-300 rounded transition-colors flex items-center space-x-1"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707"/>
                        </svg>
                        <span>Keys</span>
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div 
                        x-show="open" 
                        @click.away="open = false"
                        x-transition
                        class="absolute left-0 mt-1 w-48 bg-gray-800 border border-gray-700 rounded-lg shadow-lg z-50"
                    >
                        <button @click="sendCtrlAltDel(); open = false" class="w-full px-4 py-2 text-left text-sm text-gray-300 hover:bg-gray-700 rounded-t-lg">
                            Ctrl + Alt + Del
                        </button>
                        <button @click="sendKey('Tab'); open = false" class="w-full px-4 py-2 text-left text-sm text-gray-300 hover:bg-gray-700">
                            Tab
                        </button>
                        <button @click="sendKey('Escape'); open = false" class="w-full px-4 py-2 text-left text-sm text-gray-300 hover:bg-gray-700">
                            Escape
                        </button>
                        <button @click="sendKey('CtrlC'); open = false" class="w-full px-4 py-2 text-left text-sm text-gray-300 hover:bg-gray-700">
                            Ctrl + C
                        </button>
                        <button @click="sendKey('CtrlV'); open = false" class="w-full px-4 py-2 text-left text-sm text-gray-300 hover:bg-gray-700 rounded-b-lg">
                            Ctrl + V
                        </button>
                    </div>
                </div>

                <div class="h-6 w-px bg-gray-600"></div>

                {{-- Scaling Options --}}
                <div class="flex items-center space-x-1">
                    <button 
                        @click="setScaling('fit')"
                        :class="scalingMode === 'fit' ? 'bg-indigo-600 text-white' : 'bg-gray-700 text-gray-300 hover:bg-gray-600'"
                        class="p-1.5 rounded transition-colors"
                        title="{{ __('app.fit') }}"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                        </svg>
                    </button>
                    <button 
                        @click="setScaling('actual')"
                        :class="scalingMode === 'actual' ? 'bg-indigo-600 text-white' : 'bg-gray-700 text-gray-300 hover:bg-gray-600'"
                        class="p-1.5 rounded transition-colors"
                        title="{{ __('app.actual_size') }}"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/>
                        </svg>
                    </button>
                    
                    {{-- Zoom Controls (only when actual size) --}}
                    <template x-if="scalingMode === 'actual'">
                        <div class="flex items-center space-x-1 ml-1 pl-1 border-l border-gray-600">
                            <button 
                                @click="zoomOut()"
                                class="p-1 bg-gray-700 hover:bg-gray-600 text-gray-300 rounded transition-colors"
                                title="Zoom Out"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM13 10H7"/>
                                </svg>
                            </button>
                            <span class="text-xs text-gray-400 min-w-[3rem] text-center" x-text="Math.round(zoomLevel * 100) + '%'"></span>
                            <button 
                                @click="zoomIn()"
                                class="p-1 bg-gray-700 hover:bg-gray-600 text-gray-300 rounded transition-colors"
                                title="Zoom In"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/>
                                </svg>
                            </button>
                            <button 
                                @click="resetZoom()"
                                class="p-1 bg-gray-700 hover:bg-gray-600 text-gray-300 rounded transition-colors text-xs"
                                title="Reset Zoom"
                            >
                                100%
                            </button>
                        </div>
                    </template>
                </div>

                <div class="h-6 w-px bg-gray-600"></div>

                {{-- Quality Settings --}}
                <div class="relative" x-data="{ open: false }">
                    <button 
                        @click="open = !open"
                        :disabled="connectionStatus !== 'connected'"
                        class="px-3 py-1.5 text-xs font-medium bg-gray-700 hover:bg-gray-600 disabled:bg-gray-600 disabled:cursor-not-allowed text-gray-300 rounded transition-colors flex items-center space-x-1"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                        </svg>
                        <span>Quality</span>
                    </button>
                    <div 
                        x-show="open" 
                        @click.away="open = false"
                        x-transition
                        class="absolute left-0 mt-1 w-40 bg-gray-800 border border-gray-700 rounded-lg shadow-lg z-50 p-3"
                    >
                        <label class="block text-xs text-gray-400 mb-2">Quality Level</label>
                        <input 
                            type="range" 
                            min="0" 
                            max="9" 
                            x-model="qualityLevel"
                            @change="setQuality()"
                            class="w-full h-2 bg-gray-700 rounded-lg appearance-none cursor-pointer accent-indigo-600"
                        >
                        <div class="flex justify-between text-xs text-gray-500 mt-1">
                            <span>Low</span>
                            <span x-text="qualityLevel"></span>
                            <span>High</span>
                        </div>
                        <label class="block text-xs text-gray-400 mb-2 mt-3">Compression</label>
                        <input 
                            type="range" 
                            min="0" 
                            max="9" 
                            x-model="compressionLevel"
                            @change="setCompression()"
                            class="w-full h-2 bg-gray-700 rounded-lg appearance-none cursor-pointer accent-indigo-600"
                        >
                        <div class="flex justify-between text-xs text-gray-500 mt-1">
                            <span>None</span>
                            <span x-text="compressionLevel"></span>
                            <span>Max</span>
                        </div>
                    </div>
                </div>

                <div class="h-6 w-px bg-gray-600"></div>

                {{-- Clipboard / Type Text --}}
                <div class="relative" x-data="{ open: false, clipboardText: '' }">
                    <button 
                        @click="open = !open"
                        :disabled="connectionStatus !== 'connected'"
                        class="p-1.5 bg-gray-700 hover:bg-gray-600 disabled:bg-gray-600 disabled:cursor-not-allowed text-gray-300 rounded transition-colors"
                        title="Type Text"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </button>
                    <div 
                        x-show="open" 
                        @click.away="open = false"
                        x-transition
                        class="absolute right-0 mt-1 w-64 bg-gray-800 border border-gray-700 rounded-lg shadow-lg z-50 p-3"
                    >
                        <label class="block text-xs text-gray-400 mb-2">Type text to VPS</label>
                        <textarea 
                            x-model="clipboardText"
                            class="w-full h-20 bg-gray-900 border border-gray-700 rounded text-sm text-gray-300 p-2 resize-none focus:outline-none focus:border-indigo-500"
                            placeholder="Text will be typed directly..."
                        ></textarea>
                        <button 
                            @click="typeText(clipboardText); clipboardText = ''; open = false"
                            class="w-full mt-2 px-3 py-1.5 text-xs font-medium bg-indigo-600 hover:bg-indigo-700 text-white rounded transition-colors"
                        >
                            Type to VPS
                        </button>
                    </div>
                </div>

                {{-- Screenshot --}}
                <button 
                    @click="takeScreenshot()"
                    :disabled="connectionStatus !== 'connected'"
                    class="p-1.5 bg-gray-700 hover:bg-gray-600 disabled:bg-gray-600 disabled:cursor-not-allowed text-gray-300 rounded transition-colors"
                    title="{{ __('app.screenshot') }}"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </button>

                {{-- Reconnect --}}
                <button 
                    @click="reconnect()"
                    :disabled="connectionStatus === 'connecting'"
                    class="p-1.5 bg-gray-700 hover:bg-gray-600 disabled:bg-gray-600 disabled:cursor-not-allowed text-gray-300 rounded transition-colors"
                    title="Reconnect"
                >
                    <svg class="w-4 h-4" :class="connectionStatus === 'connecting' ? 'animate-spin' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </button>
            </div>

            {{-- Console Container --}}
            <div class="flex-1 relative bg-gray-900 overflow-hidden">
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
                        <svg class="animate-spin h-10 w-10 text-indigo-500 mx-auto mb-4" fill="none" viewBox="0 0 24 24">
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
                            class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors"
                        >
                            {{ __('app.retry') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Console Modal Alpine.js Component --}}
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('consoleModal', (vpsId, csrfToken) => ({
        isOpen: false,
        isFullscreen: false,
        activeTab: 'vnc',
        connectionStatus: 'idle',
        statusMessage: '',
        scalingMode: 'fit',
        zoomLevel: 1,
        qualityLevel: 6,
        compressionLevel: 2,
        consoleManager: null,

        init() {
            // Listen for open console event
            window.addEventListener('open-console', (e) => {
                if (e.detail && e.detail.vpsId == vpsId) {
                    this.open(e.detail.tab || 'vnc');
                }
            });

            // Handle fullscreen change
            document.addEventListener('fullscreenchange', () => {
                this.isFullscreen = !!document.fullscreenElement;
            });
        },

        async open(tab = 'vnc') {
            this.isOpen = true;
            this.activeTab = tab;
            this.connectionStatus = 'idle';
            this.statusMessage = '';

            // Wait for DOM to update
            await this.$nextTick();

            // Initialize console manager if not already done
            if (!this.consoleManager && window.ConsoleManager) {
                this.consoleManager = new window.ConsoleManager({
                    vncContainer: this.$refs.vncContainer,
                    sshContainer: this.$refs.sshContainer,
                    tabContainer: this.$refs.tabContainer,
                    vpsId: vpsId,
                    csrfToken: csrfToken,
                    baseUrl: '',
                    onStatusChange: (status) => {
                        this.connectionStatus = status.status;
                        this.statusMessage = status.message;
                    },
                    onError: (error) => {
                        this.connectionStatus = 'error';
                        this.statusMessage = error.error?.message || 'Connection error';
                    }
                });

                await this.consoleManager.init();
            }

            // Connect to the selected tab
            this.switchTab(tab);
        },

        close() {
            if (this.consoleManager) {
                this.consoleManager.disconnectAll();
            }
            this.isOpen = false;
            this.connectionStatus = 'idle';
            
            // Exit fullscreen if active
            if (document.fullscreenElement) {
                document.exitFullscreen();
            }
        },

        async switchTab(tab) {
            this.activeTab = tab;
            if (this.consoleManager) {
                await this.consoleManager.switchTab(tab);
            }
        },

        async toggleFullscreen() {
            if (this.consoleManager) {
                await this.consoleManager.toggleFullscreen();
            }
        },

        sendCtrlAltDel() {
            if (this.consoleManager) {
                this.consoleManager.sendCtrlAltDel();
            }
        },

        sendKey(key) {
            if (!this.consoleManager) return;
            
            const rfb = this.consoleManager.getRfb();
            if (!rfb) return;
            
            const KeyTable = {
                Tab: 0xFF09,
                Escape: 0xFF1B,
                Control_L: 0xFFE3,
                c: 0x0063,
                v: 0x0076
            };
            
            switch(key) {
                case 'Tab':
                    rfb.sendKey(KeyTable.Tab, 'Tab');
                    break;
                case 'Escape':
                    rfb.sendKey(KeyTable.Escape, 'Escape');
                    break;
                case 'CtrlC':
                    rfb.sendKey(KeyTable.Control_L, 'ControlLeft', true);
                    rfb.sendKey(KeyTable.c, 'KeyC', true);
                    rfb.sendKey(KeyTable.c, 'KeyC', false);
                    rfb.sendKey(KeyTable.Control_L, 'ControlLeft', false);
                    break;
                case 'CtrlV':
                    rfb.sendKey(KeyTable.Control_L, 'ControlLeft', true);
                    rfb.sendKey(KeyTable.v, 'KeyV', true);
                    rfb.sendKey(KeyTable.v, 'KeyV', false);
                    rfb.sendKey(KeyTable.Control_L, 'ControlLeft', false);
                    break;
            }
        },

        setScaling(mode) {
            this.scalingMode = mode;
            if (mode === 'fit') {
                this.zoomLevel = 1;
                this.applyZoom();
            }
            if (this.consoleManager) {
                this.consoleManager.setVncScaling(mode);
            }
        },

        zoomIn() {
            if (this.zoomLevel < 3) {
                this.zoomLevel = Math.min(3, this.zoomLevel + 0.25);
                this.applyZoom();
            }
        },

        zoomOut() {
            if (this.zoomLevel > 0.25) {
                this.zoomLevel = Math.max(0.25, this.zoomLevel - 0.25);
                this.applyZoom();
            }
        },

        resetZoom() {
            this.zoomLevel = 1;
            this.applyZoom();
        },

        applyZoom() {
            if (this.$refs.vncContainer) {
                const canvas = this.$refs.vncContainer.querySelector('canvas');
                if (canvas) {
                    canvas.style.transform = `scale(${this.zoomLevel})`;
                    canvas.style.transformOrigin = 'top left';
                }
            }
        },

        setQuality() {
            if (this.consoleManager) {
                this.consoleManager.setVncQuality(this.qualityLevel);
            }
        },

        setCompression() {
            if (this.consoleManager) {
                this.consoleManager.setVncCompression(this.compressionLevel);
            }
        },

        sendClipboard(text) {
            if (this.consoleManager && text) {
                this.consoleManager.sendClipboard(text);
            }
        },

        typeText(text) {
            if (this.consoleManager && text) {
                this.consoleManager.typeText(text);
            }
        },

        takeScreenshot() {
            if (this.consoleManager) {
                const dataUrl = this.consoleManager.getVncScreenshot();
                if (dataUrl) {
                    const link = document.createElement('a');
                    link.download = `console-${vpsId}-${Date.now()}.png`;
                    link.href = dataUrl;
                    link.click();
                }
            }
        },

        async reconnect() {
            this.connectionStatus = 'connecting';
            this.statusMessage = 'Reconnecting...';
            
            if (this.consoleManager) {
                if (this.activeTab === 'vnc') {
                    await this.consoleManager.connectVnc();
                } else {
                    await this.consoleManager.connectSsh();
                }
            }
        }
    }));
});
</script>
