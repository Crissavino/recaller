<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('legal.terms.title') }} - {{ config('app.name') }}</title>
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
        ul, ol {
            margin-bottom: 16px;
            padding-left: 24px;
        }
        li {
            margin-bottom: 8px;
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
        <h1>{{ __('legal.terms.title') }}</h1>
        <p class="last-updated">{{ __('legal.last_updated') }}: {{ now()->format('F d, Y') }}</p>

        <div class="highlight">
            <p><strong>{{ __('legal.terms.summary') }}</strong></p>
        </div>

        <h2>1. {{ __('legal.terms.section1_title') }}</h2>
        <p>{{ __('legal.terms.section1_content') }}</p>

        <h2>2. {{ __('legal.terms.section2_title') }}</h2>
        <p>{{ __('legal.terms.section2_content') }}</p>
        <ul>
            <li>{{ __('legal.terms.section2_item1') }}</li>
            <li>{{ __('legal.terms.section2_item2') }}</li>
            <li>{{ __('legal.terms.section2_item3') }}</li>
            <li>{{ __('legal.terms.section2_item4') }}</li>
        </ul>

        <h2>3. {{ __('legal.terms.section3_title') }}</h2>
        <p>{{ __('legal.terms.section3_content') }}</p>

        <h2>4. {{ __('legal.terms.section4_title') }}</h2>
        <p>{{ __('legal.terms.section4_content') }}</p>

        <h2>5. {{ __('legal.terms.section5_title') }}</h2>
        <p>{{ __('legal.terms.section5_content') }}</p>

        <h2>6. {{ __('legal.terms.section6_title') }}</h2>
        <p>{{ __('legal.terms.section6_content') }}</p>

        <h2>7. {{ __('legal.terms.section7_title') }}</h2>
        <p>{{ __('legal.terms.section7_content') }}</p>

        <h2>8. {{ __('legal.terms.section8_title') }}</h2>
        <p>{{ __('legal.terms.section8_content') }} <a href="mailto:contactus@recaller.io">contactus@recaller.io</a></p>
    </main>
</body>
</html>
