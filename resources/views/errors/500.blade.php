<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('errors.500_title') }} - Recaller</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }
        .container {
            text-align: center;
            max-width: 480px;
        }
        .logo {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 48px;
        }
        .logo-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .logo-text {
            font-size: 24px;
            font-weight: 700;
            color: #0f172a;
        }
        .error-code {
            font-size: 120px;
            font-weight: 700;
            color: #ef4444;
            line-height: 1;
            margin-bottom: 16px;
            text-shadow: 0 4px 24px rgba(239, 68, 68, 0.2);
        }
        .error-title {
            font-size: 24px;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 12px;
        }
        .error-message {
            font-size: 16px;
            color: #64748b;
            line-height: 1.6;
            margin-bottom: 32px;
        }
        .actions {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 14px 28px;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
        }
        .btn-primary {
            background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
            color: #fff;
            box-shadow: 0 4px 14px rgba(14, 165, 233, 0.3);
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(14, 165, 233, 0.4);
        }
        .btn-secondary {
            background: #fff;
            color: #374151;
            border: 1px solid #e5e7eb;
        }
        .btn-secondary:hover {
            background: #f9fafb;
            border-color: #d1d5db;
        }
        .illustration {
            margin-bottom: 32px;
        }
        .illustration svg {
            width: 200px;
            height: 200px;
        }
        .support-text {
            margin-top: 32px;
            font-size: 14px;
            color: #64748b;
        }
        .support-text a {
            color: #0ea5e9;
            text-decoration: none;
        }
        .support-text a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="/" class="logo">
            <div class="logo-icon">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                </svg>
            </div>
            <span class="logo-text">Recaller</span>
        </a>

        <div class="illustration">
            <svg viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="100" cy="100" r="80" fill="#fef2f2"/>
                <circle cx="100" cy="100" r="60" fill="#fee2e2"/>
                <path d="M70 90C70 84.477 74.477 80 80 80H120C125.523 80 130 84.477 130 90V130C130 135.523 125.523 140 120 140H80C74.477 140 70 135.523 70 130V90Z" fill="#ef4444"/>
                <path d="M90 100L110 120M110 100L90 120" stroke="white" stroke-width="6" stroke-linecap="round"/>
            </svg>
        </div>

        <div class="error-code">500</div>
        <h1 class="error-title">{{ __('errors.500_title') }}</h1>
        <p class="error-message">{{ __('errors.500_message') }}</p>

        <div class="actions">
            <a href="{{ url('/') }}" class="btn btn-primary">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                {{ __('errors.go_home') }}
            </a>
            <a href="javascript:location.reload()" class="btn btn-secondary">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                {{ __('errors.try_again') }}
            </a>
        </div>

        <p class="support-text">
            {{ __('errors.support_text') }} <a href="mailto:support@recaller.io">support@recaller.io</a>
        </p>
    </div>
</body>
</html>
