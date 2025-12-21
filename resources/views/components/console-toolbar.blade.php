{{-- Console Toolbar Component --}}
{{-- Keys Dropdown --}}
<div class="relative" x-data="{ open: false }">
    <button 
        @click="open = !open"
        :disabled="connectionStatus !== 'connected'"
        class="px-3 py-1.5 text-xs font-medium bg-surface-200 dark:bg-surface-700 hover:bg-surface-300 dark:hover:bg-surface-600 disabled:opacity-50 disabled:cursor-not-allowed text-surface-700 dark:text-surface-300 rounded transition-colors flex items-center space-x-1"
    >
        <span>Keys</span>
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>
    <div 
        x-show="open" 
        @click.away="open = false"
        x-transition
        class="absolute right-0 mt-1 w-48 bg-white dark:bg-surface-800 border border-surface-200 dark:border-surface-700 rounded-lg shadow-lg z-50"
    >
        <button @click="sendCtrlAltDel(); open = false" class="w-full px-4 py-2 text-left text-sm text-surface-700 dark:text-surface-300 hover:bg-surface-100 dark:hover:bg-surface-700 rounded-t-lg">
            Ctrl + Alt + Del
        </button>
        <button @click="sendKey('Tab'); open = false" class="w-full px-4 py-2 text-left text-sm text-surface-700 dark:text-surface-300 hover:bg-surface-100 dark:hover:bg-surface-700">
            Tab
        </button>
        <button @click="sendKey('Escape'); open = false" class="w-full px-4 py-2 text-left text-sm text-surface-700 dark:text-surface-300 hover:bg-surface-100 dark:hover:bg-surface-700">
            Escape
        </button>
        <button @click="sendKey('CtrlC'); open = false" class="w-full px-4 py-2 text-left text-sm text-surface-700 dark:text-surface-300 hover:bg-surface-100 dark:hover:bg-surface-700">
            Ctrl + C
        </button>
        <button @click="sendKey('CtrlV'); open = false" class="w-full px-4 py-2 text-left text-sm text-surface-700 dark:text-surface-300 hover:bg-surface-100 dark:hover:bg-surface-700 rounded-b-lg">
            Ctrl + V
        </button>
    </div>
</div>

<div class="h-6 w-px bg-surface-300 dark:bg-surface-600"></div>

{{-- Scaling --}}
<div class="flex items-center space-x-1">
    <button 
        @click="setScaling('fit')"
        :class="scalingMode === 'fit' ? 'bg-primary-600 text-white' : 'bg-surface-200 dark:bg-surface-700 text-surface-700 dark:text-surface-300 hover:bg-surface-300 dark:hover:bg-surface-600'"
        class="p-1.5 rounded transition-colors"
        title="{{ __('app.fit') }}"
    >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
        </svg>
    </button>
    <button 
        @click="setScaling('actual')"
        :class="scalingMode === 'actual' ? 'bg-primary-600 text-white' : 'bg-surface-200 dark:bg-surface-700 text-surface-700 dark:text-surface-300 hover:bg-surface-300 dark:hover:bg-surface-600'"
        class="p-1.5 rounded transition-colors"
        title="{{ __('app.actual_size') }}"
    >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/>
        </svg>
    </button>
</div>

<div class="h-6 w-px bg-surface-300 dark:bg-surface-600"></div>

{{-- Type Text --}}
<div class="relative" x-data="{ open: false, clipboardText: '' }">
    <button 
        @click="open = !open"
        :disabled="connectionStatus !== 'connected'"
        class="p-1.5 bg-surface-200 dark:bg-surface-700 hover:bg-surface-300 dark:hover:bg-surface-600 disabled:opacity-50 disabled:cursor-not-allowed text-surface-700 dark:text-surface-300 rounded transition-colors"
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
        class="absolute right-0 mt-1 w-64 bg-white dark:bg-surface-800 border border-surface-200 dark:border-surface-700 rounded-lg shadow-lg z-50 p-3"
    >
        <label class="block text-xs text-surface-500 dark:text-surface-400 mb-2">Type text to VPS</label>
        <textarea 
            x-model="clipboardText"
            class="w-full h-20 bg-surface-50 dark:bg-surface-900 border border-surface-200 dark:border-surface-700 rounded text-sm text-surface-900 dark:text-surface-300 p-2 resize-none focus:outline-none focus:border-primary-500"
            placeholder="Text will be typed directly..."
        ></textarea>
        <button 
            @click="typeText(clipboardText); clipboardText = ''; open = false"
            class="w-full mt-2 px-3 py-1.5 text-xs font-medium bg-primary-600 hover:bg-primary-700 text-white rounded transition-colors"
        >
            Type to VPS
        </button>
    </div>
</div>

{{-- Screenshot --}}
<button 
    @click="takeScreenshot()"
    :disabled="connectionStatus !== 'connected'"
    class="p-1.5 bg-surface-200 dark:bg-surface-700 hover:bg-surface-300 dark:hover:bg-surface-600 disabled:opacity-50 disabled:cursor-not-allowed text-surface-700 dark:text-surface-300 rounded transition-colors"
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
    class="p-1.5 bg-surface-200 dark:bg-surface-700 hover:bg-surface-300 dark:hover:bg-surface-600 disabled:opacity-50 disabled:cursor-not-allowed text-surface-700 dark:text-surface-300 rounded transition-colors"
    title="{{ __('app.reconnect') }}"
>
    <svg class="w-4 h-4" :class="connectionStatus === 'connecting' ? 'animate-spin' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
    </svg>
</button>
