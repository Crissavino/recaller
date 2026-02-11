<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Recaller') }}</title>

    <!-- Favicons -->
    <link rel="icon" href="/favicon.ico" sizes="48x48">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="manifest" href="/site.webmanifest">
    <meta name="theme-color" content="#0ea5e9">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet" />
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: #fafbfc;
            color: #1a1a2e;
            line-height: 1.6;
            min-height: 100vh;
        }
        .auth-container {
            min-height: 100vh;
            display: flex;
        }
        .auth-left {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 48px;
            max-width: 520px;
        }
        .auth-right {
            flex: 1.2;
            background: linear-gradient(135deg, #1a1a2e 0%, #2d2d44 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 48px;
            position: relative;
            overflow: hidden;
        }
        .auth-right::before {
            content: '';
            position: absolute;
            top: -30%;
            right: -20%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(14, 165, 233, 0.15) 0%, transparent 70%);
            pointer-events: none;
        }
        .auth-right::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -20%;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(6, 182, 212, 0.1) 0%, transparent 70%);
            pointer-events: none;
        }
        .auth-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 700;
            font-size: 20px;
            color: #1a1a2e;
            text-decoration: none;
        }
        .auth-logo svg { color: #0ea5e9; }
        .auth-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 48px;
        }
        .lang-switch {
            display: flex;
            gap: 4px;
            padding: 4px;
            background: #f1f5f9;
            border-radius: 8px;
        }
        .lang-switch a {
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            color: #64748b;
            text-decoration: none;
            transition: all 0.2s;
        }
        .lang-switch a:hover { color: #1a1a2e; background: #fff; }
        .lang-switch a.active {
            background: #fff;
            color: #0ea5e9;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        }
        .auth-content {
            width: 100%;
            max-width: 380px;
        }
        .auth-title {
            font-size: 28px;
            font-weight: 800;
            color: #1a1a2e;
            margin-bottom: 8px;
            letter-spacing: -0.02em;
        }
        .auth-subtitle {
            font-size: 15px;
            color: #64748b;
            margin-bottom: 32px;
        }
        .auth-form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        .form-label {
            font-size: 14px;
            font-weight: 600;
            color: #1a1a2e;
        }
        .form-input {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            font-size: 15px;
            font-family: 'Inter', sans-serif;
            color: #1a1a2e;
            background: #fff;
            transition: all 0.2s ease;
        }
        .form-input:focus {
            outline: none;
            border-color: #0ea5e9;
            box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.1);
        }
        .form-input::placeholder {
            color: #94a3b8;
        }
        .form-error {
            font-size: 13px;
            color: #ef4444;
            margin-top: 4px;
        }
        .form-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .form-checkbox {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: #64748b;
            cursor: pointer;
        }
        .form-checkbox input {
            width: 16px;
            height: 16px;
            accent-color: #0ea5e9;
            cursor: pointer;
        }
        .form-link {
            font-size: 14px;
            color: #0ea5e9;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }
        .form-link:hover {
            color: #0284c7;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 24px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 15px;
            text-decoration: none;
            transition: all 0.2s ease;
            border: none;
            cursor: pointer;
            font-family: 'Inter', sans-serif;
            width: 100%;
        }
        .btn-primary {
            background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
            color: #fff;
            box-shadow: 0 2px 12px rgba(14, 165, 233, 0.35);
        }
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 16px rgba(14, 165, 233, 0.45);
        }
        .auth-divider {
            display: flex;
            align-items: center;
            gap: 16px;
            margin: 24px 0;
        }
        .auth-divider::before,
        .auth-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e2e8f0;
        }
        .auth-divider span {
            font-size: 13px;
            color: #94a3b8;
        }
        .auth-footer {
            text-align: center;
            margin-top: 24px;
            font-size: 14px;
            color: #64748b;
        }
        .auth-footer a {
            color: #0ea5e9;
            text-decoration: none;
            font-weight: 600;
        }
        .auth-footer a:hover {
            color: #0284c7;
        }
        .auth-status {
            padding: 12px 16px;
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            border-radius: 10px;
            color: #047857;
            font-size: 14px;
            margin-bottom: 20px;
        }

        /* Right panel content */
        .promo-content {
            text-align: center;
            color: #fff;
            position: relative;
            z-index: 1;
            max-width: 400px;
        }
        .promo-icon {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            font-size: 36px;
        }
        .promo-title {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 16px;
            letter-spacing: -0.02em;
        }
        .promo-text {
            font-size: 16px;
            color: #94a3b8;
            line-height: 1.7;
            margin-bottom: 32px;
        }
        .promo-stats {
            display: flex;
            justify-content: center;
            gap: 40px;
        }
        .promo-stat {
            text-align: center;
        }
        .promo-stat-value {
            font-size: 32px;
            font-weight: 800;
            color: #0ea5e9;
        }
        .promo-stat-label {
            font-size: 13px;
            color: #94a3b8;
            margin-top: 4px;
        }

        @media (max-width: 1024px) {
            .auth-right { display: none; }
            .auth-left {
                max-width: 100%;
                padding: 32px 24px;
            }
        }
        @media (max-width: 480px) {
            .auth-left { padding: 24px 20px; }
            .auth-title { font-size: 24px; }
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-left">
            <div class="auth-header">
                <a href="/" class="auth-logo" style="margin-bottom: 0;">
                    <svg width="32" height="32" viewBox="0 0 40 40" fill="none">
                        <rect width="40" height="40" rx="10" fill="url(#auth-logo-gradient)"/>
                        <path d="M27 13H15c-2.2 0-4 1.8-4 4v6c0 2.2 1.8 4 4 4h12" stroke="white" stroke-width="2.5" fill="none" stroke-linecap="round"/>
                        <path d="M23 9l4 4-4 4" stroke="white" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                        <circle cx="15" cy="20" r="2" fill="white"/>
                        <defs>
                            <linearGradient id="auth-logo-gradient" x1="0" y1="0" x2="40" y2="40">
                                <stop offset="0%" stop-color="#0ea5e9"/>
                                <stop offset="100%" stop-color="#06b6d4"/>
                            </linearGradient>
                        </defs>
                    </svg>
                    Recaller
                </a>
                <div class="lang-switch">
                    <a href="{{ route('locale.switch', 'ro') }}" class="{{ app()->getLocale() === 'ro' ? 'active' : '' }}">RO</a>
                    <a href="{{ route('locale.switch', 'es') }}" class="{{ app()->getLocale() === 'es' ? 'active' : '' }}">ES</a>
                    <a href="{{ route('locale.switch', 'en') }}" class="{{ app()->getLocale() === 'en' ? 'active' : '' }}">EN</a>
                </div>
            </div>
            <div class="auth-content">
                {{ $slot }}
            </div>
        </div>
        <div class="auth-right">
            <div class="promo-content">
                <div class="promo-icon">💰</div>
                <h2 class="promo-title">{{ __('auth.promo.title') }}</h2>
                <p class="promo-text">{{ __('auth.promo.text') }}</p>
                <div class="promo-stats">
                    <div class="promo-stat">
                        <div class="promo-stat-value">{{ __('auth.promo.stat1_value') }}</div>
                        <div class="promo-stat-label">{{ __('auth.promo.stat1_label') }}</div>
                    </div>
                    <div class="promo-stat">
                        <div class="promo-stat-value">{{ __('auth.promo.stat2_value') }}</div>
                        <div class="promo-stat-label">{{ __('auth.promo.stat2_label') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
