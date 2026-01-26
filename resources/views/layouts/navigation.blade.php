<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                        <svg class="h-8 w-8" viewBox="0 0 40 40" fill="none">
                            <rect width="40" height="40" rx="10" fill="url(#nav-logo-gradient)"/>
                            <path d="M27 13H15c-2.2 0-4 1.8-4 4v6c0 2.2 1.8 4 4 4h12" stroke="white" stroke-width="2.5" fill="none" stroke-linecap="round"/>
                            <path d="M23 9l4 4-4 4" stroke="white" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                            <circle cx="15" cy="20" r="2" fill="white"/>
                            <defs>
                                <linearGradient id="nav-logo-gradient" x1="0" y1="0" x2="40" y2="40">
                                    <stop offset="0%" stop-color="#0ea5e9"/>
                                    <stop offset="100%" stop-color="#06b6d4"/>
                                </linearGradient>
                            </defs>
                        </svg>
                        <span class="font-semibold text-gray-800 text-lg hidden sm:block">Recaller</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('nav.dashboard') }}
                    </x-nav-link>
                    <x-nav-link :href="route('inbox.index')" :active="request()->routeIs('inbox.*') || request()->routeIs('conversations.*')" class="relative">
                        {{ __('nav.inbox') }}
                        @if(isset($unreadConversationsCount) && $unreadConversationsCount > 0)
                            <span class="absolute -top-1 -right-2 inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-500 rounded-full">
                                {{ $unreadConversationsCount > 9 ? '9+' : $unreadConversationsCount }}
                            </span>
                        @endif
                    </x-nav-link>
                    <x-nav-link :href="route('reports.index')" :active="request()->routeIs('reports.*')">
                        {{ __('nav.reports') }}
                    </x-nav-link>
                    <x-nav-link :href="route('settings.index')" :active="request()->routeIs('settings.*')">
                        {{ __('nav.settings') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Language Selector & Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 sm:gap-4">
                <!-- Language Selector -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="inline-flex items-center gap-1.5 px-2 py-1.5 text-sm font-medium text-gray-500 hover:text-gray-700 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                        </svg>
                        <span class="uppercase">{{ app()->getLocale() }}</span>
                    </button>
                    <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-24 bg-white rounded-lg shadow-lg border border-gray-100 py-1 z-50" style="display: none;">
                        <a href="{{ route('locale.switch', 'es') }}" class="block px-4 py-2 text-sm {{ app()->getLocale() === 'es' ? 'text-sky-600 bg-sky-50 font-medium' : 'text-gray-700 hover:bg-gray-50' }}">
                            Español
                        </a>
                        <a href="{{ route('locale.switch', 'en') }}" class="block px-4 py-2 text-sm {{ app()->getLocale() === 'en' ? 'text-sky-600 bg-sky-50 font-medium' : 'text-gray-700 hover:bg-gray-50' }}">
                            English
                        </a>
                    </div>
                </div>

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('nav.profile') }}
                        </x-dropdown-link>

                        <x-dropdown-link :href="route('subscription.index')">
                            {{ __('nav.subscription') }}
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('nav.logout') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
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
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('nav.dashboard') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('inbox.index')" :active="request()->routeIs('inbox.*') || request()->routeIs('conversations.*')">
                {{ __('nav.inbox') }}
                @if(isset($unreadConversationsCount) && $unreadConversationsCount > 0)
                    <span class="ml-2 inline-flex items-center justify-center px-2 py-0.5 text-xs font-bold text-white bg-red-500 rounded-full">
                        {{ $unreadConversationsCount > 9 ? '9+' : $unreadConversationsCount }}
                    </span>
                @endif
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('reports.index')" :active="request()->routeIs('reports.*')">
                {{ __('nav.reports') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('settings.index')" :active="request()->routeIs('settings.*')">
                {{ __('nav.settings') }}
            </x-responsive-nav-link>
        </div>

        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('nav.profile') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('subscription.index')">
                    {{ __('nav.subscription') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('nav.logout') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>

        <!-- Mobile Language Selector -->
        <div class="pt-2 pb-3 border-t border-gray-200">
            <div class="px-4 flex items-center gap-2">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                </svg>
                <a href="{{ route('locale.switch', 'es') }}" class="px-3 py-1.5 text-sm rounded-md {{ app()->getLocale() === 'es' ? 'bg-sky-100 text-sky-700 font-medium' : 'text-gray-600' }}">ES</a>
                <a href="{{ route('locale.switch', 'en') }}" class="px-3 py-1.5 text-sm rounded-md {{ app()->getLocale() === 'en' ? 'bg-sky-100 text-sky-700 font-medium' : 'text-gray-600' }}">EN</a>
            </div>
        </div>
    </div>
</nav>
