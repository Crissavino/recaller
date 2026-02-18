<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('seo.cookies.title') }}</title>
    <meta name="description" content="{{ __('seo.cookies.description') }}">
    <link rel="canonical" href="{{ url('/cookies') }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/cookies') }}">
    <meta property="og:title" content="{{ __('seo.cookies.title') }}">
    <meta property="og:description" content="{{ __('seo.cookies.description') }}">
    <meta property="og:site_name" content="Recaller">
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="{{ __('seo.cookies.title') }}">
    <meta name="twitter:description" content="{{ __('seo.cookies.description') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Figtree', sans-serif;
            background: #f8fafc;
            color: #334155;
            line-height: 1.7;
        }
        .header {
            background: #0f172a;
            padding: 20px 24px;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .header-inner {
            max-width: 800px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .logo {
            font-size: 24px;
            font-weight: 700;
            color: #fff;
            text-decoration: none;
        }
        .logo span { color: #0ea5e9; }
        .back-link {
            color: #94a3b8;
            text-decoration: none;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: color 0.2s;
        }
        .back-link:hover { color: #fff; }
        .content {
            max-width: 800px;
            margin: 0 auto;
            padding: 48px 24px 80px;
        }
        h1 {
            font-size: 36px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 8px;
        }
        .last-updated {
            color: #64748b;
            font-size: 14px;
            margin-bottom: 40px;
        }
        h2 {
            font-size: 22px;
            font-weight: 600;
            color: #0f172a;
            margin-top: 40px;
            margin-bottom: 16px;
        }
        p {
            margin-bottom: 16px;
        }
        a {
            color: #0ea5e9;
        }
        .highlight {
            background: #f0f9ff;
            border-left: 4px solid #0ea5e9;
            padding: 16px 20px;
            margin: 24px 0;
            border-radius: 0 8px 8px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 16px 0 24px;
        }
        th, td {
            text-align: left;
            padding: 12px 16px;
            border-bottom: 1px solid #e2e8f0;
        }
        th {
            background: #f1f5f9;
            font-weight: 600;
            color: #0f172a;
            font-size: 14px;
        }
        td {
            font-size: 14px;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-inner">
            <a href="{{ url('/') }}" class="logo">Re<span>caller</span></a>
            <a href="{{ url()->previous() }}" class="back-link">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                {{ __('legal.back') }}
            </a>
        </div>
    </header>

    <main class="content">
        <h1>{{ __('legal.cookies.title') }}</h1>
        <p class="last-updated">{{ __('legal.last_updated') }}: {{ now()->format('F d, Y') }}</p>

        <div class="highlight">
            <p><strong>{{ __('legal.cookies.summary') }}</strong></p>
        </div>

        <h2>1. {{ __('legal.cookies.what_title') }}</h2>
        <p>{{ __('legal.cookies.what_content') }}</p>

        <h2>2. {{ __('legal.cookies.cookies_we_use_title') }}</h2>
        <p>{{ __('legal.cookies.cookies_we_use_content') }}</p>
        <table>
            <thead>
                <tr>
                    <th>{{ __('legal.cookies.cookie_name') }}</th>
                    <th>{{ __('legal.cookies.cookie_purpose') }}</th>
                    <th>{{ __('legal.cookies.cookie_duration') }}</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ __('legal.cookies.cookie_session') }}</td>
                    <td>{{ __('legal.cookies.cookie_session_purpose') }}</td>
                    <td>{{ __('legal.cookies.cookie_session_duration') }}</td>
                </tr>
                <tr>
                    <td>{{ __('legal.cookies.cookie_remember') }}</td>
                    <td>{{ __('legal.cookies.cookie_remember_purpose') }}</td>
                    <td>{{ __('legal.cookies.cookie_remember_duration') }}</td>
                </tr>
            </tbody>
        </table>

        <h2>3. {{ __('legal.cookies.no_tracking_title') }}</h2>
        <p>{{ __('legal.cookies.no_tracking_content') }}</p>

        <h2>4. {{ __('legal.cookies.manage_title') }}</h2>
        <p>{{ __('legal.cookies.manage_content') }}</p>

        <h2>5. {{ __('legal.cookies.contact_title') }}</h2>
        <p>{{ __('legal.cookies.contact_content') }} <a href="mailto:contactus@recaller.io">contactus@recaller.io</a></p>
    </main>
</body>
</html>
