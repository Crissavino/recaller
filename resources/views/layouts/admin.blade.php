<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Admin - {{ config('app.name', 'Recaller') }}</title>

        <!-- Favicons -->
        <link rel="icon" href="/favicon.ico" sizes="48x48">
        <link rel="icon" type="image/svg+xml" href="/favicon.svg">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased" style="background: #f1f5f9;">
        <div class="min-h-screen">
            <!-- Admin Navigation -->
            <nav style="background: #fff; border-bottom: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="shrink-0 flex items-center">
                                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
                                    <svg class="h-8 w-8" viewBox="0 0 40 40" fill="none">
                                        <rect width="40" height="40" rx="10" fill="url(#admin-logo-gradient)"/>
                                        <path d="M27 13H15c-2.2 0-4 1.8-4 4v6c0 2.2 1.8 4 4 4h12" stroke="white" stroke-width="2.5" fill="none" stroke-linecap="round"/>
                                        <path d="M23 9l4 4-4 4" stroke="white" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                                        <circle cx="15" cy="20" r="2" fill="white"/>
                                        <defs>
                                            <linearGradient id="admin-logo-gradient" x1="0" y1="0" x2="40" y2="40">
                                                <stop offset="0%" stop-color="#9333ea"/>
                                                <stop offset="100%" stop-color="#7c3aed"/>
                                            </linearGradient>
                                        </defs>
                                    </svg>
                                    <span style="font-weight: 600; color: #1e293b; font-size: 18px;">Admin</span>
                                </a>
                            </div>

                            <!-- Navigation Links -->
                            <div class="hidden space-x-1 sm:-my-px sm:ms-8 sm:flex">
                                <a href="{{ route('admin.dashboard') }}" style="display: inline-flex; align-items: center; padding: 0 12px; border-bottom: 2px solid {{ request()->routeIs('admin.dashboard') ? '#7c3aed' : 'transparent' }}; font-size: 14px; font-weight: 500; color: {{ request()->routeIs('admin.dashboard') ? '#7c3aed' : '#64748b' }}; text-decoration: none; transition: all 0.15s;">
                                    Dashboard
                                </a>
                                <a href="{{ route('admin.phone-numbers.index') }}" style="display: inline-flex; align-items: center; padding: 0 12px; border-bottom: 2px solid {{ request()->routeIs('admin.phone-numbers.*') ? '#7c3aed' : 'transparent' }}; font-size: 14px; font-weight: 500; color: {{ request()->routeIs('admin.phone-numbers.*') ? '#7c3aed' : '#64748b' }}; text-decoration: none; transition: all 0.15s;">
                                    Phone Numbers
                                </a>
                                <a href="{{ route('admin.templates.index') }}" style="display: inline-flex; align-items: center; padding: 0 12px; border-bottom: 2px solid {{ request()->routeIs('admin.templates.*') ? '#7c3aed' : 'transparent' }}; font-size: 14px; font-weight: 500; color: {{ request()->routeIs('admin.templates.*') ? '#7c3aed' : '#64748b' }}; text-decoration: none; transition: all 0.15s;">
                                    Templates
                                </a>
                                <a href="{{ route('admin.plans.index') }}" style="display: inline-flex; align-items: center; padding: 0 12px; border-bottom: 2px solid {{ request()->routeIs('admin.plans.*') ? '#7c3aed' : 'transparent' }}; font-size: 14px; font-weight: 500; color: {{ request()->routeIs('admin.plans.*') ? '#7c3aed' : '#64748b' }}; text-decoration: none; transition: all 0.15s;">
                                    Plans & Prices
                                </a>
                                <a href="{{ route('admin.subscriptions.index') }}" style="display: inline-flex; align-items: center; padding: 0 12px; border-bottom: 2px solid {{ request()->routeIs('admin.subscriptions.*') ? '#7c3aed' : 'transparent' }}; font-size: 14px; font-weight: 500; color: {{ request()->routeIs('admin.subscriptions.*') ? '#7c3aed' : '#64748b' }}; text-decoration: none; transition: all 0.15s;">
                                    Subscriptions
                                </a>
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <a href="{{ route('dashboard') }}" style="font-size: 13px; color: #64748b; text-decoration: none; display: flex; align-items: center; gap: 4px;">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/>
                                </svg>
                                Back to App
                            </a>
                            <span style="color: #94a3b8; font-size: 13px;">{{ Auth::user()->name }}</span>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            @isset($header)
                <header style="background: #fff; border-bottom: 1px solid #e2e8f0;">
                    <div class="max-w-7xl mx-auto py-5 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main class="py-8">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    {{ $slot }}
                </div>
            </main>
        </div>

        @stack('scripts')
    </body>
</html>
