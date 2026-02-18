<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('seo.pricing.title') }}</title>
    <meta name="description" content="{{ __('seo.pricing.description') }}">
    <meta name="keywords" content="{{ __('seo.pricing.keywords') }}">
    <link rel="canonical" href="{{ url('/pricing') }}">

    <!-- Favicons -->
    <link rel="icon" href="/favicon.ico" sizes="48x48">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <meta name="theme-color" content="#0ea5e9">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/pricing') }}">
    <meta property="og:title" content="{{ __('seo.pricing.title') }}">
    <meta property="og:description" content="{{ __('seo.pricing.description') }}">
    <meta property="og:site_name" content="Recaller">
    <meta property="og:locale" content="{{ str_replace('_', '-', app()->getLocale()) }}">
    @if(file_exists(public_path('images/og-image.png')))
    <meta property="og:image" content="{{ asset('images/og-image.png') }}">
    @endif

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ __('seo.pricing.title') }}">
    <meta name="twitter:description" content="{{ __('seo.pricing.description') }}">
    @if(file_exists(public_path('images/og-image.png')))
    <meta name="twitter:image" content="{{ asset('images/og-image.png') }}">
    @endif

    <!-- Structured Data -->
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "WebPage",
        "name": "{{ __('seo.pricing.title') }}",
        "description": "{{ __('seo.pricing.description') }}",
        "url": "{{ url('/pricing') }}",
        "mainEntity": {
            "@@type": "ItemList",
            "numberOfItems": 3,
            "itemListElement": [
                {
                    "@@type": "ListItem",
                    "position": 1,
                    "item": {
                        "@@type": "Product",
                        "name": "Recaller Starter",
                        "description": "{{ __('landing.pricing.starter.description') }}",
                        "offers": {
                            "@@type": "Offer",
                            "price": "39",
                            "priceCurrency": "EUR",
                            "priceValidUntil": "{{ now()->addYear()->format('Y-m-d') }}",
                            "availability": "https://schema.org/InStock"
                        }
                    }
                },
                {
                    "@@type": "ListItem",
                    "position": 2,
                    "item": {
                        "@@type": "Product",
                        "name": "Recaller Growth",
                        "description": "{{ __('landing.pricing.growth.description') }}",
                        "offers": {
                            "@@type": "Offer",
                            "price": "79",
                            "priceCurrency": "EUR",
                            "priceValidUntil": "{{ now()->addYear()->format('Y-m-d') }}",
                            "availability": "https://schema.org/InStock"
                        }
                    }
                },
                {
                    "@@type": "ListItem",
                    "position": 3,
                    "item": {
                        "@@type": "Product",
                        "name": "Recaller Pro",
                        "description": "{{ __('landing.pricing.pro.description') }}",
                        "offers": {
                            "@@type": "Offer",
                            "price": "159",
                            "priceCurrency": "EUR",
                            "priceValidUntil": "{{ now()->addYear()->format('Y-m-d') }}",
                            "availability": "https://schema.org/InStock"
                        }
                    }
                }
            ]
        }
    }
    </script>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet" />
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(180deg, #1a1a2e 0%, #2d2d44 100%);
            color: #fff;
            min-height: 100vh;
        }

        /* Navbar */
        .nav {
            padding: 16px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
        }
        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 700;
            font-size: 18px;
            color: #fff;
            text-decoration: none;
        }
        .nav-links { display: flex; gap: 16px; align-items: center; }
        .nav-links a {
            color: #94a3b8;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: color 0.2s;
        }
        .nav-links a:hover { color: #fff; }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 14px;
            text-decoration: none;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
        }
        .btn-primary {
            background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
            color: #fff;
        }
        .btn-primary:hover { transform: translateY(-1px); }
        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #fff;
        }
        .btn-secondary:hover { background: rgba(255, 255, 255, 0.15); }

        /* Header */
        .pricing-header {
            text-align: center;
            padding: 60px 24px 40px;
            max-width: 600px;
            margin: 0 auto;
        }
        .pricing-header h1 {
            font-size: 40px;
            font-weight: 800;
            margin-bottom: 16px;
            letter-spacing: -0.02em;
        }
        .pricing-header p {
            font-size: 18px;
            color: #94a3b8;
            line-height: 1.6;
        }

        /* Toggle */
        .pricing-toggle {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 48px;
        }
        .pricing-toggle-label {
            font-size: 14px;
            font-weight: 500;
            color: #94a3b8;
            cursor: pointer;
            transition: color 0.2s;
        }
        .pricing-toggle-label.active { color: #fff; }
        .pricing-toggle-switch {
            position: relative;
            width: 56px;
            height: 28px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50px;
            cursor: pointer;
        }
        .pricing-toggle-switch::after {
            content: '';
            position: absolute;
            top: 3px;
            left: 3px;
            width: 22px;
            height: 22px;
            background: #0ea5e9;
            border-radius: 50%;
            transition: transform 0.3s;
        }
        .pricing-toggle-switch.annual::after { transform: translateX(28px); }
        .pricing-save-badge {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 50px;
        }

        /* Grid */
        .pricing-grid {
            max-width: 1100px;
            margin: 0 auto;
            padding: 0 24px 60px;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
        }
        .pricing-card {
            background: rgba(255, 255, 255, 0.06);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 32px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.08);
            position: relative;
            transition: transform 0.3s;
            display: flex;
            flex-direction: column;
        }
        .pricing-card:hover { transform: translateY(-4px); }
        .pricing-card.popular {
            border-color: #0ea5e9;
            background: rgba(14, 165, 233, 0.1);
        }
        .pricing-card.popular::before {
            content: attr(data-badge);
            position: absolute;
            top: -12px;
            left: 50%;
            transform: translateX(-50%);
            background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
            color: #fff;
            font-size: 12px;
            font-weight: 700;
            padding: 6px 16px;
            border-radius: 50px;
        }
        .pricing-card-name {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 4px;
        }
        .pricing-card-desc {
            font-size: 14px;
            color: #94a3b8;
            margin-bottom: 20px;
        }
        .price {
            font-size: 48px;
            font-weight: 800;
            margin: 16px 0;
            display: flex;
            align-items: baseline;
            justify-content: center;
            gap: 4px;
        }
        .price .currency { font-size: 24px; font-weight: 600; }
        .price .period { font-size: 16px; font-weight: 500; color: #94a3b8; }
        .price-annual { display: none; }
        .price-monthly { display: flex; }
        .annual .price-annual { display: flex; }
        .annual .price-monthly { display: none; }
        .pricing-billed {
            font-size: 12px;
            color: #64748b;
            margin-bottom: 20px;
            min-height: 18px;
        }
        .pricing-features {
            text-align: left;
            margin: 24px 0;
            list-style: none;
            flex-grow: 1;
        }
        .pricing-features li {
            padding: 8px 0;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            color: #cbd5e1;
            font-size: 13px;
        }
        .pricing-features .check {
            width: 16px;
            height: 16px;
            min-width: 16px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 9px;
            margin-top: 2px;
        }
        .pricing-card .btn { width: 100%; padding: 14px 24px; font-size: 15px; margin-top: auto; }
        .pricing-sms-note {
            font-size: 11px;
            color: #64748b;
            margin-top: 12px;
        }

        /* Guarantee */
        .pricing-guarantee {
            text-align: center;
            padding: 0 24px 60px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 16px;
        }
        .pricing-guarantee-text {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: #94a3b8;
        }
        .pricing-guarantee-text svg {
            width: 20px;
            height: 20px;
            color: #10b981;
        }
        .pricing-trial-notice {
            display: flex;
            align-items: center;
            gap: 10px;
            background: rgba(14, 165, 233, 0.15);
            border: 1px solid rgba(14, 165, 233, 0.3);
            color: #7dd3fc;
            font-size: 15px;
            font-weight: 500;
            padding: 12px 20px;
            border-radius: 50px;
        }
        .pricing-trial-notice svg {
            width: 20px;
            height: 20px;
            color: #38bdf8;
        }

        /* FAQ teaser */
        .faq-teaser {
            text-align: center;
            padding: 40px 24px;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
        }
        .faq-teaser p {
            color: #94a3b8;
            font-size: 14px;
        }
        .faq-teaser a {
            color: #0ea5e9;
            text-decoration: none;
            font-weight: 500;
        }
        .faq-teaser a:hover { text-decoration: underline; }

        /* Currency selector */
        .currency-selector {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            margin-bottom: 32px;
        }
        .currency-selector a {
            padding: 6px 14px;
            border-radius: 50px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
            color: #94a3b8;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .currency-selector a:hover { color: #fff; border-color: rgba(255, 255, 255, 0.3); }
        .currency-selector a.active {
            background: rgba(14, 165, 233, 0.2);
            border-color: #0ea5e9;
            color: #7dd3fc;
        }

        @media (max-width: 900px) {
            .pricing-grid {
                grid-template-columns: 1fr;
                max-width: 400px;
            }
            .pricing-card.popular { order: -1; }
            .pricing-header h1 { font-size: 32px; }
        }
    </style>
</head>
<body>
    <nav class="nav">
        <a href="/" class="logo">
            <svg width="32" height="32" viewBox="0 0 40 40" fill="none">
                <rect width="40" height="40" rx="10" fill="url(#logo-gradient)"/>
                <path d="M27 13H15c-2.2 0-4 1.8-4 4v6c0 2.2 1.8 4 4 4h12" stroke="white" stroke-width="2.5" fill="none" stroke-linecap="round"/>
                <path d="M23 9l4 4-4 4" stroke="white" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                <circle cx="15" cy="20" r="2" fill="white"/>
                <defs>
                    <linearGradient id="logo-gradient" x1="0" y1="0" x2="40" y2="40">
                        <stop offset="0%" stop-color="#0ea5e9"/>
                        <stop offset="100%" stop-color="#06b6d4"/>
                    </linearGradient>
                </defs>
            </svg>
            Recaller
        </a>
        <div class="nav-links">
            <a href="/">{{ __('nav.home') ?? 'Home' }}</a>
            @foreach(config('app.available_locales') as $loc)
                <a href="{{ route('locale.switch', $loc) }}" style="{{ app()->getLocale() === $loc ? 'color: #fff; font-weight: 700;' : '' }}">{{ strtoupper($loc) }}</a>
            @endforeach
            <a href="{{ route('login') }}" class="btn btn-secondary">{{ __('nav.login') }}</a>
        </div>
    </nav>

    <div class="pricing-header">
        @if(session('info'))
            <div style="background: rgba(14, 165, 233, 0.15); border: 1px solid rgba(14, 165, 233, 0.3); color: #7dd3fc; padding: 12px 20px; border-radius: 12px; margin-bottom: 24px; font-size: 14px;">
                {{ session('info') }}
            </div>
        @endif
        @if(session('error'))
            <div style="background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); color: #fca5a5; padding: 12px 20px; border-radius: 12px; margin-bottom: 24px; font-size: 14px;">
                {{ session('error') }}
            </div>
        @endif
        <h1>{{ __('landing.pricing.title') }}</h1>
        <p>{{ __('landing.pricing.subtitle') }}</p>
    </div>

    <div class="pricing-toggle">
        <span class="pricing-toggle-label active" data-period="monthly">{{ __('landing.pricing.toggle_monthly') }}</span>
        <div class="pricing-toggle-switch" onclick="togglePricing()"></div>
        <span class="pricing-toggle-label" data-period="annual">{{ __('landing.pricing.toggle_annual') }}</span>
        <span class="pricing-save-badge">{{ __('landing.pricing.save_badge') }}</span>
    </div>

    <div class="currency-selector">
        <a href="{{ route('currency.switch', 'eur') }}" class="{{ $currentCurrency === 'eur' ? 'active' : '' }}">EUR (€)</a>
        <a href="{{ route('currency.switch', 'ron') }}" class="{{ $currentCurrency === 'ron' ? 'active' : '' }}">RON</a>
    </div>

    @php $prices = config('pricing.plans'); @endphp

    <div class="pricing-grid" id="pricing-container">
        <!-- Starter -->
        <div class="pricing-card">
            <h3 class="pricing-card-name">{{ __('landing.pricing.starter.name') }}</h3>
            <p class="pricing-card-desc">{{ __('landing.pricing.starter.description') }}</p>
            <div class="price price-monthly">
                <span class="currency">{{ $currencySymbol }}</span>{{ $prices['starter'][$currentCurrency]['monthly'] }}<span class="period">{{ __('landing.pricing.per_month') }}</span>
            </div>
            <div class="price price-annual">
                <span class="currency">{{ $currencySymbol }}</span>{{ $prices['starter'][$currentCurrency]['annual'] }}<span class="period">{{ __('landing.pricing.per_month') }}</span>
            </div>
            <p class="pricing-billed"><span class="annual-only">{{ __('landing.pricing.billed_annually') }}</span></p>
            <ul class="pricing-features">
                @foreach(__('landing.pricing.starter.features') as $feature)
                <li><span class="check">✓</span> {{ $feature }}</li>
                @endforeach
            </ul>
            @auth
                <a href="{{ route('subscription.checkout', ['plan' => 'starter', 'interval' => 'monthly']) }}" class="btn btn-secondary plan-btn" data-plan="starter">{{ __('landing.pricing.cta') }}</a>
            @else
                <a href="{{ route('register') }}?plan=starter&interval=monthly" class="btn btn-secondary plan-btn" data-plan="starter">{{ __('landing.pricing.cta') }}</a>
            @endauth
            <p class="pricing-sms-note">{{ $prices['starter'][$currentCurrency]['sms_extra'] }}</p>
        </div>

        <!-- Growth -->
        <div class="pricing-card popular" data-badge="{{ __('landing.pricing.most_popular') }}">
            <h3 class="pricing-card-name">{{ __('landing.pricing.growth.name') }}</h3>
            <p class="pricing-card-desc">{{ __('landing.pricing.growth.description') }}</p>
            <div class="price price-monthly">
                <span class="currency">{{ $currencySymbol }}</span>{{ $prices['growth'][$currentCurrency]['monthly'] }}<span class="period">{{ __('landing.pricing.per_month') }}</span>
            </div>
            <div class="price price-annual">
                <span class="currency">{{ $currencySymbol }}</span>{{ $prices['growth'][$currentCurrency]['annual'] }}<span class="period">{{ __('landing.pricing.per_month') }}</span>
            </div>
            <p class="pricing-billed"><span class="annual-only">{{ __('landing.pricing.billed_annually') }}</span></p>
            <ul class="pricing-features">
                @foreach(__('landing.pricing.growth.features') as $feature)
                <li><span class="check">✓</span> {{ $feature }}</li>
                @endforeach
            </ul>
            @auth
                <a href="{{ route('subscription.checkout', ['plan' => 'growth', 'interval' => 'monthly']) }}" class="btn btn-primary plan-btn" data-plan="growth">{{ __('landing.pricing.cta') }}</a>
            @else
                <a href="{{ route('register') }}?plan=growth&interval=monthly" class="btn btn-primary plan-btn" data-plan="growth">{{ __('landing.pricing.cta') }}</a>
            @endauth
            <p class="pricing-sms-note">{{ $prices['growth'][$currentCurrency]['sms_extra'] }}</p>
        </div>

        <!-- Pro -->
        <div class="pricing-card">
            <h3 class="pricing-card-name">{{ __('landing.pricing.pro.name') }}</h3>
            <p class="pricing-card-desc">{{ __('landing.pricing.pro.description') }}</p>
            <div class="price price-monthly">
                <span class="currency">{{ $currencySymbol }}</span>{{ $prices['pro'][$currentCurrency]['monthly'] }}<span class="period">{{ __('landing.pricing.per_month') }}</span>
            </div>
            <div class="price price-annual">
                <span class="currency">{{ $currencySymbol }}</span>{{ $prices['pro'][$currentCurrency]['annual'] }}<span class="period">{{ __('landing.pricing.per_month') }}</span>
            </div>
            <p class="pricing-billed"><span class="annual-only">{{ __('landing.pricing.billed_annually') }}</span></p>
            <ul class="pricing-features">
                @foreach(__('landing.pricing.pro.features') as $feature)
                <li><span class="check">✓</span> {{ $feature }}</li>
                @endforeach
            </ul>
            @auth
                <a href="{{ route('subscription.checkout', ['plan' => 'pro', 'interval' => 'monthly']) }}" class="btn btn-secondary plan-btn" data-plan="pro">{{ __('landing.pricing.cta') }}</a>
            @else
                <a href="{{ route('register') }}?plan=pro&interval=monthly" class="btn btn-secondary plan-btn" data-plan="pro">{{ __('landing.pricing.cta') }}</a>
            @endauth
            <p class="pricing-sms-note">{{ $prices['pro'][$currentCurrency]['sms_extra'] }}</p>
        </div>
    </div>

    <div class="pricing-guarantee">
        <div class="pricing-trial-notice">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ __('landing.pricing.no_charge_until', ['date' => now()->addDays(14)->format('M d, Y')]) }}
        </div>
        <div class="pricing-guarantee-text">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            {{ __('landing.pricing.guarantee') }}
        </div>
    </div>

    <div class="faq-teaser">
        <p>{{ __('landing.pricing.questions') ?? '¿Tienes preguntas?' }} <a href="mailto:contactus@recaller.io">{{ __('landing.pricing.contact_us') ?? 'Contáctanos' }}</a></p>
    </div>

    <script>
        function togglePricing() {
            const container = document.getElementById('pricing-container');
            const toggle = document.querySelector('.pricing-toggle-switch');
            const labels = document.querySelectorAll('.pricing-toggle-label');
            const annualTexts = document.querySelectorAll('.annual-only');
            const planButtons = document.querySelectorAll('.plan-btn');

            container.classList.toggle('annual');
            toggle.classList.toggle('annual');

            const isAnnual = container.classList.contains('annual');
            const interval = isAnnual ? 'annual' : 'monthly';

            labels.forEach(label => {
                label.classList.toggle('active', label.dataset.period === interval);
            });

            annualTexts.forEach(text => {
                text.style.visibility = isAnnual ? 'visible' : 'hidden';
            });

            // Update button URLs with new interval
            planButtons.forEach(btn => {
                const url = new URL(btn.href);
                if (url.pathname.includes('/subscription/checkout/')) {
                    // Authenticated user - update path
                    const parts = url.pathname.split('/');
                    parts[parts.length - 1] = interval;
                    url.pathname = parts.join('/');
                } else {
                    // Guest user - update query param
                    url.searchParams.set('interval', interval);
                }
                btn.href = url.toString();
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.annual-only').forEach(text => {
                text.style.visibility = 'hidden';
            });
        });
    </script>
</body>
</html>
