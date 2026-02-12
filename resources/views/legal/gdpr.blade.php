<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('legal.gdpr.title') }} - {{ config('app.name') }}</title>
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
        <h1>{{ __('legal.gdpr.title') }}</h1>
        <p class="last-updated">{{ __('legal.last_updated') }}: {{ now()->format('F d, Y') }}</p>

        <div class="highlight">
            <p><strong>{{ __('legal.gdpr.summary') }}</strong></p>
        </div>

        <h2>1. {{ __('legal.gdpr.controller_title') }}</h2>
        <p>{{ __('legal.gdpr.controller_content') }}</p>
        <p>Jackcode FZ-LLC<br>Dubai, United Arab Emirates<br><a href="mailto:contactus@recaller.io">contactus@recaller.io</a></p>

        <h2>2. {{ __('legal.gdpr.data_collected_title') }}</h2>
        <p>{{ __('legal.gdpr.data_collected_content') }}</p>
        <table>
            <thead>
                <tr>
                    <th>{{ __('legal.gdpr.data_category') }}</th>
                    <th>{{ __('legal.gdpr.data_examples') }}</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ __('legal.gdpr.data_cat1') }}</td>
                    <td>{{ __('legal.gdpr.data_ex1') }}</td>
                </tr>
                <tr>
                    <td>{{ __('legal.gdpr.data_cat2') }}</td>
                    <td>{{ __('legal.gdpr.data_ex2') }}</td>
                </tr>
                <tr>
                    <td>{{ __('legal.gdpr.data_cat3') }}</td>
                    <td>{{ __('legal.gdpr.data_ex3') }}</td>
                </tr>
                <tr>
                    <td>{{ __('legal.gdpr.data_cat4') }}</td>
                    <td>{{ __('legal.gdpr.data_ex4') }}</td>
                </tr>
            </tbody>
        </table>

        <h2>3. {{ __('legal.gdpr.legal_basis_title') }}</h2>
        <p>{{ __('legal.gdpr.legal_basis_content') }}</p>
        <ul>
            <li><strong>{{ __('legal.gdpr.basis_contract') }}</strong> — {{ __('legal.gdpr.basis_contract_desc') }}</li>
            <li><strong>{{ __('legal.gdpr.basis_legitimate') }}</strong> — {{ __('legal.gdpr.basis_legitimate_desc') }}</li>
            <li><strong>{{ __('legal.gdpr.basis_consent') }}</strong> — {{ __('legal.gdpr.basis_consent_desc') }}</li>
        </ul>

        <h2>4. {{ __('legal.gdpr.processors_title') }}</h2>
        <p>{{ __('legal.gdpr.processors_content') }}</p>
        <table>
            <thead>
                <tr>
                    <th>{{ __('legal.gdpr.processor_name') }}</th>
                    <th>{{ __('legal.gdpr.processor_purpose') }}</th>
                    <th>{{ __('legal.gdpr.processor_location') }}</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Twilio</td>
                    <td>{{ __('legal.gdpr.processor_twilio') }}</td>
                    <td>{{ __('legal.gdpr.location_us') }}</td>
                </tr>
                <tr>
                    <td>Stripe</td>
                    <td>{{ __('legal.gdpr.processor_stripe') }}</td>
                    <td>{{ __('legal.gdpr.location_us') }}</td>
                </tr>
                <tr>
                    <td>Resend</td>
                    <td>{{ __('legal.gdpr.processor_resend') }}</td>
                    <td>{{ __('legal.gdpr.location_us') }}</td>
                </tr>
                <tr>
                    <td>Hetzner</td>
                    <td>{{ __('legal.gdpr.processor_hetzner') }}</td>
                    <td>{{ __('legal.gdpr.location_eu') }}</td>
                </tr>
            </tbody>
        </table>
        <p>{{ __('legal.gdpr.processors_safeguards') }}</p>

        <h2>5. {{ __('legal.gdpr.transfers_title') }}</h2>
        <p>{{ __('legal.gdpr.transfers_content') }}</p>

        <h2>6. {{ __('legal.gdpr.retention_title') }}</h2>
        <p>{{ __('legal.gdpr.retention_content') }}</p>
        <ul>
            <li>{{ __('legal.gdpr.retention_active') }}</li>
            <li>{{ __('legal.gdpr.retention_deleted') }}</li>
            <li>{{ __('legal.gdpr.retention_logs') }}</li>
        </ul>

        <h2>7. {{ __('legal.gdpr.rights_title') }}</h2>
        <p>{{ __('legal.gdpr.rights_content') }}</p>
        <ul>
            <li><strong>{{ __('legal.gdpr.right_access') }}</strong> — {{ __('legal.gdpr.right_access_desc') }}</li>
            <li><strong>{{ __('legal.gdpr.right_rectification') }}</strong> — {{ __('legal.gdpr.right_rectification_desc') }}</li>
            <li><strong>{{ __('legal.gdpr.right_erasure') }}</strong> — {{ __('legal.gdpr.right_erasure_desc') }}</li>
            <li><strong>{{ __('legal.gdpr.right_portability') }}</strong> — {{ __('legal.gdpr.right_portability_desc') }}</li>
            <li><strong>{{ __('legal.gdpr.right_restriction') }}</strong> — {{ __('legal.gdpr.right_restriction_desc') }}</li>
            <li><strong>{{ __('legal.gdpr.right_objection') }}</strong> — {{ __('legal.gdpr.right_objection_desc') }}</li>
        </ul>
        <p>{{ __('legal.gdpr.rights_exercise') }} <a href="mailto:contactus@recaller.io">contactus@recaller.io</a></p>

        <h2>8. {{ __('legal.gdpr.breach_title') }}</h2>
        <p>{{ __('legal.gdpr.breach_content') }}</p>

        <h2>9. {{ __('legal.gdpr.dpo_title') }}</h2>
        <p>{{ __('legal.gdpr.dpo_content') }} <a href="mailto:contactus@recaller.io">contactus@recaller.io</a></p>
    </main>
</body>
</html>
