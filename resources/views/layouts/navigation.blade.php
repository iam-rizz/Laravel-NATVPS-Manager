<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <svg class="block h-9 w-auto text-gray-800 dark:text-gray-200" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>
                        </svg>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <a href="{{ route('dashboard') }}" 
                       class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('dashboard') ? 'border-indigo-400 dark:border-indigo-600 text-gray-900 dark:text-gray-100' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-700' }} text-sm font-medium leading-5 focus:outline-none transition duration-150 ease-in-out">
                        {{ __('app.dashboard') }}
                    </a>
                    
                    {{-- Shared menu items (both admin and user) --}}
                    <a href="{{ route('nat-vps.index') }}" 
                       class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('nat-vps.*') ? 'border-indigo-400 dark:border-indigo-600 text-gray-900 dark:text-gray-100' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-700' }} text-sm font-medium leading-5 focus:outline-none transition duration-150 ease-in-out">
                        {{ auth()->user()->isAdmin() ? __('app.nat_vps') : __('app.my_vps') }}
                    </a>
                    <a href="{{ route('console.index') }}" 
                       class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('console.*') ? 'border-indigo-400 dark:border-indigo-600 text-gray-900 dark:text-gray-100' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-700' }} text-sm font-medium leading-5 focus:outline-none transition duration-150 ease-in-out">
                        {{ __('app.console') }}
                    </a>
                    
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('servers.index') }}" 
                           class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('servers.*') ? 'border-indigo-400 dark:border-indigo-600 text-gray-900 dark:text-gray-100' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-700' }} text-sm font-medium leading-5 focus:outline-none transition duration-150 ease-in-out">
                            {{ __('app.servers') }}
                        </a>
                        <a href="{{ route('users.index') }}" 
                           class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('users.*') ? 'border-indigo-400 dark:border-indigo-600 text-gray-900 dark:text-gray-100' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-700' }} text-sm font-medium leading-5 focus:outline-none transition duration-150 ease-in-out">
                            {{ __('app.users') }}
                        </a>
                        <a href="{{ route('settings.general') }}" 
                           class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('settings.*') ? 'border-indigo-400 dark:border-indigo-600 text-gray-900 dark:text-gray-100' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-700' }} text-sm font-medium leading-5 focus:outline-none transition duration-150 ease-in-out">
                            {{ __('app.settings') }}
                        </a>
                        <a href="{{ route('audit-logs.index') }}" 
                           class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('audit-logs.*') ? 'border-indigo-400 dark:border-indigo-600 text-gray-900 dark:text-gray-100' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-700' }} text-sm font-medium leading-5 focus:outline-none transition duration-150 ease-in-out">
                            {{ __('app.audit_logs') }}
                        </a>
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <!-- Language Switcher -->
                <div x-data="{ open: false }" class="relative me-3">
                    <button @click="open = ! open" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                        <svg class="w-5 h-5 me-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                        </svg>
                        <span>{{ strtoupper(app()->getLocale()) }}</span>
                        <svg class="ms-1 fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>

                    <div x-show="open" 
                         @click.away="open = false"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 z-50 mt-2 w-32 rounded-md shadow-lg origin-top-right"
                         style="display: none;">
                        <div class="rounded-md ring-1 ring-black ring-opacity-5 py-1 bg-white dark:bg-gray-700">
                            <a href="{{ route('language.switch', 'en') }}" 
                               class="flex items-center px-4 py-2 text-sm {{ app()->getLocale() == 'en' ? 'text-indigo-600 dark:text-indigo-400 bg-gray-100 dark:bg-gray-600' : 'text-gray-700 dark:text-gray-300' }} hover:bg-gray-100 dark:hover:bg-gray-600">
                                <span class="me-2">🇺🇸</span> English
                            </a>
                            <a href="{{ route('language.switch', 'id') }}" 
                               class="flex items-center px-4 py-2 text-sm {{ app()->getLocale() == 'id' ? 'text-indigo-600 dark:text-indigo-400 bg-gray-100 dark:bg-gray-600' : 'text-gray-700 dark:text-gray-300' }} hover:bg-gray-100 dark:hover:bg-gray-600">
                                <span class="me-2">🇮🇩</span> Indonesia
                            </a>
                        </div>
                    </div>
                </div>

                <!-- User Dropdown -->
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = ! open" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                        <div>{{ Auth::user()->name }}</div>
                        <div class="ms-1">
                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </button>

                    <div x-show="open" 
                         @click.away="open = false"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 z-50 mt-2 w-48 rounded-md shadow-lg origin-top-right"
                         style="display: none;">
                        <div class="rounded-md ring-1 ring-black ring-opacity-5 py-1 bg-white dark:bg-gray-700">
                            <a href="{{ route('two-factor.setup') }}" class="block px-4 py-2 text-sm leading-5 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-800 transition duration-150 ease-in-out {{ request()->routeIs('two-factor.*') ? 'bg-gray-100 dark:bg-gray-800' : '' }}">
                                {{ __('app.security') }}
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-800 transition duration-150 ease-in-out">
                                    {{ __('app.logout') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <a href="{{ route('dashboard') }}" 
               class="block w-full ps-3 pe-4 py-2 border-l-4 {{ request()->routeIs('dashboard') ? 'border-indigo-400 dark:border-indigo-600 text-indigo-700 dark:text-indigo-300 bg-indigo-50 dark:bg-indigo-900/50' : 'border-transparent text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 dark:hover:border-gray-600' }} text-start text-base font-medium focus:outline-none transition duration-150 ease-in-out">
                {{ __('app.dashboard') }}
            </a>
            
            {{-- Shared menu items (both admin and user) --}}
            <a href="{{ route('nat-vps.index') }}" 
               class="block w-full ps-3 pe-4 py-2 border-l-4 {{ request()->routeIs('nat-vps.*') ? 'border-indigo-400 dark:border-indigo-600 text-indigo-700 dark:text-indigo-300 bg-indigo-50 dark:bg-indigo-900/50' : 'border-transparent text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 dark:hover:border-gray-600' }} text-start text-base font-medium focus:outline-none transition duration-150 ease-in-out">
                {{ auth()->user()->isAdmin() ? __('app.nat_vps') : __('app.my_vps') }}
            </a>
            <a href="{{ route('console.index') }}" 
               class="block w-full ps-3 pe-4 py-2 border-l-4 {{ request()->routeIs('console.*') ? 'border-indigo-400 dark:border-indigo-600 text-indigo-700 dark:text-indigo-300 bg-indigo-50 dark:bg-indigo-900/50' : 'border-transparent text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 dark:hover:border-gray-600' }} text-start text-base font-medium focus:outline-none transition duration-150 ease-in-out">
                {{ __('app.console') }}
            </a>
            
            @if(auth()->user()->isAdmin())
                <a href="{{ route('servers.index') }}" 
                   class="block w-full ps-3 pe-4 py-2 border-l-4 {{ request()->routeIs('servers.*') ? 'border-indigo-400 dark:border-indigo-600 text-indigo-700 dark:text-indigo-300 bg-indigo-50 dark:bg-indigo-900/50' : 'border-transparent text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 dark:hover:border-gray-600' }} text-start text-base font-medium focus:outline-none transition duration-150 ease-in-out">
                    {{ __('app.servers') }}
                </a>
                <a href="{{ route('users.index') }}" 
                   class="block w-full ps-3 pe-4 py-2 border-l-4 {{ request()->routeIs('users.*') ? 'border-indigo-400 dark:border-indigo-600 text-indigo-700 dark:text-indigo-300 bg-indigo-50 dark:bg-indigo-900/50' : 'border-transparent text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 dark:hover:border-gray-600' }} text-start text-base font-medium focus:outline-none transition duration-150 ease-in-out">
                    {{ __('app.users') }}
                </a>
                <a href="{{ route('settings.general') }}" 
                   class="block w-full ps-3 pe-4 py-2 border-l-4 {{ request()->routeIs('settings.*') ? 'border-indigo-400 dark:border-indigo-600 text-indigo-700 dark:text-indigo-300 bg-indigo-50 dark:bg-indigo-900/50' : 'border-transparent text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 dark:hover:border-gray-600' }} text-start text-base font-medium focus:outline-none transition duration-150 ease-in-out">
                    {{ __('app.settings') }}
                </a>
                <a href="{{ route('audit-logs.index') }}" 
                   class="block w-full ps-3 pe-4 py-2 border-l-4 {{ request()->routeIs('audit-logs.*') ? 'border-indigo-400 dark:border-indigo-600 text-indigo-700 dark:text-indigo-300 bg-indigo-50 dark:bg-indigo-900/50' : 'border-transparent text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 dark:hover:border-gray-600' }} text-start text-base font-medium focus:outline-none transition duration-150 ease-in-out">
                    {{ __('app.audit_logs') }}
                </a>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <!-- Language Switcher Mobile -->
                <div class="ps-3 pe-4 py-2 border-l-4 border-transparent">
                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('app.language') }}:</span>
                    <div class="flex space-x-2 mt-1">
                        <a href="{{ route('language.switch', 'en') }}" 
                           class="px-3 py-1 text-sm rounded {{ app()->getLocale() == 'en' ? 'bg-indigo-100 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-300' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}">
                            🇺🇸 EN
                        </a>
                        <a href="{{ route('language.switch', 'id') }}" 
                           class="px-3 py-1 text-sm rounded {{ app()->getLocale() == 'id' ? 'bg-indigo-100 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-300' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}">
                            🇮🇩 ID
                        </a>
                    </div>
                </div>

                <a href="{{ route('two-factor.setup') }}" 
                   class="block w-full ps-3 pe-4 py-2 border-l-4 {{ request()->routeIs('two-factor.*') ? 'border-indigo-400 dark:border-indigo-600 text-indigo-700 dark:text-indigo-300 bg-indigo-50 dark:bg-indigo-900/50' : 'border-transparent text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 dark:hover:border-gray-600' }} text-start text-base font-medium focus:outline-none transition duration-150 ease-in-out">
                    {{ __('app.security') }}
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 dark:hover:border-gray-600 focus:outline-none focus:text-gray-800 dark:focus:text-gray-200 focus:bg-gray-50 dark:focus:bg-gray-700 focus:border-gray-300 dark:focus:border-gray-600 transition duration-150 ease-in-out">
                        {{ __('app.logout') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>
