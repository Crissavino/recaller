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
                    <x-nav-link :href="route('inbox.index')" :active="request()->routeIs('inbox.*') || request()->routeIs('conversations.*')">
                        {{ __('nav.inbox') }}
                        <span data-unread-badge class="ml-1.5 inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-500 rounded-full" style="{{ (isset($unreadConversationsCount) && $unreadConversationsCount > 0) ? '' : 'display: none;' }}">
                            {{ isset($unreadConversationsCount) ? ($unreadConversationsCount > 9 ? '9+' : $unreadConversationsCount) : '0' }}
                        </span>
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
                @php
                    $localeFlags = ['en' => '🇬🇧', 'es' => '🇪🇸', 'ro' => '🇷🇴'];
                    $localeNames = ['en' => 'English', 'es' => 'Español', 'ro' => 'Română'];
                    $currentLocale = app()->getLocale();
                @endphp
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-sm font-medium text-gray-500 hover:text-gray-700 bg-gray-50 hover:bg-gray-100 rounded-lg border border-gray-200 transition">
                        <span>{{ $localeFlags[$currentLocale] ?? '🇬🇧' }}</span>
                        <span class="uppercase">{{ $currentLocale }}</span>
                        <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-40 bg-white rounded-lg shadow-lg border border-gray-100 py-1 z-50" style="display: none;">
                        @foreach(['en', 'es', 'ro'] as $loc)
                            <a href="{{ route('locale.switch', $loc) }}" class="flex items-center gap-2.5 px-4 py-2 text-sm {{ $currentLocale === $loc ? 'text-sky-600 bg-sky-50 font-medium' : 'text-gray-700 hover:bg-gray-50' }}">
                                <span>{{ $localeFlags[$loc] }}</span>
                                {{ $localeNames[$loc] }}
                            </a>
                        @endforeach
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
                        @if(Auth::user()->is_admin)
                            <x-dropdown-link :href="route('admin.dashboard')" class="text-purple-600 font-medium">
                                Admin Panel
                            </x-dropdown-link>
                            <div class="border-t border-gray-100 my-1"></div>
                        @endif

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
                <span data-unread-badge class="ml-2 inline-flex items-center justify-center px-2 py-0.5 text-xs font-bold text-white bg-red-500 rounded-full" style="{{ (isset($unreadConversationsCount) && $unreadConversationsCount > 0) ? '' : 'display: none;' }}">
                    {{ isset($unreadConversationsCount) ? ($unreadConversationsCount > 9 ? '9+' : $unreadConversationsCount) : '0' }}
                </span>
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
                @if(Auth::user()->is_admin)
                    <x-responsive-nav-link :href="route('admin.dashboard')" class="text-purple-600 font-medium">
                        Admin Panel
                    </x-responsive-nav-link>
                @endif

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
                @foreach(['en', 'es', 'ro'] as $loc)
                    <a href="{{ route('locale.switch', $loc) }}" class="flex items-center gap-1 px-3 py-1.5 text-sm rounded-md {{ app()->getLocale() === $loc ? 'bg-sky-100 text-sky-700 font-medium' : 'text-gray-600' }}">
                        <span>{{ $localeFlags[$loc] }}</span> {{ strtoupper($loc) }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</nav>
