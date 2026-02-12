<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Recaller - {{ __('landing.hero.subhead') }}</title>
    <meta name="description" content="{{ __('landing.hero.description') }}">

    <!-- Favicons -->
    <link rel="icon" href="/favicon.ico" sizes="48x48">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="manifest" href="/site.webmanifest">
    <meta name="theme-color" content="#0ea5e9">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:title" content="Recaller - {{ __('landing.hero.subhead') }}">
    <meta property="og:description" content="{{ __('landing.hero.description') }}">
    <meta property="og:site_name" content="Recaller">
    @if(file_exists(public_path('images/og-image.png')))
    <meta property="og:image" content="{{ asset('images/og-image.png') }}">
    @endif

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ url('/') }}">
    <meta name="twitter:title" content="Recaller - {{ __('landing.hero.subhead') }}">
    <meta name="twitter:description" content="{{ __('landing.hero.description') }}">
    @if(file_exists(public_path('images/og-image.png')))
    <meta name="twitter:image" content="{{ asset('images/og-image.png') }}">
    @endif

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900" rel="stylesheet" />
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body { font-family: 'Inter', sans-serif; background: #fafbfc; color: #1a1a2e; line-height: 1.6; overflow-x: hidden; }

        /* Navbar */
        .nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            padding: 14px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 100;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.04);
        }
        .nav-inner {
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 700;
            font-size: 18px;
            color: #1a1a2e;
            text-decoration: none;
        }
        .logo svg { color: #0ea5e9; }
        .nav-links { display: flex; gap: 24px; align-items: center; }
        .nav-links a {
            color: #64748b;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: color 0.2s;
        }
        .nav-links a:hover { color: #1a1a2e; }
        .lang-dropdown {
            position: relative;
        }
        .lang-dropdown-btn {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 500;
            color: #475569;
            transition: all 0.2s;
            text-decoration: none;
        }
        .lang-dropdown-btn:hover { background: #e2e8f0; color: #1a1a2e; }
        .lang-dropdown-btn .flag { font-size: 16px; line-height: 1; }
        .lang-dropdown-btn .arrow { font-size: 10px; color: #94a3b8; transition: transform 0.2s; }
        .lang-dropdown.open .arrow { transform: rotate(180deg); }
        .lang-dropdown-menu {
            position: absolute;
            top: calc(100% + 6px);
            right: 0;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
            min-width: 160px;
            padding: 6px;
            display: none;
            z-index: 200;
        }
        .lang-dropdown.open .lang-dropdown-menu { display: block; }
        .lang-dropdown-menu a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
            color: #475569;
            text-decoration: none;
            transition: background 0.15s;
        }
        .lang-dropdown-menu a:hover { background: #f1f5f9; color: #1a1a2e; }
        .lang-dropdown-menu a.active { background: #eff6ff; color: #0ea5e9; }
        .lang-dropdown-menu a .flag { font-size: 18px; }
        .lang-dropdown-menu a .lang-label { flex: 1; }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 11px 20px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 14px;
            text-decoration: none;
            transition: all 0.2s ease;
            border: none;
            cursor: pointer;
            white-space: nowrap;
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
        .btn-secondary {
            background: #fff;
            color: #1a1a2e;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }
        .btn-secondary:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
        }
        .btn-dark {
            background: #1a1a2e;
            color: #fff;
            box-shadow: 0 2px 12px rgba(26, 26, 46, 0.2);
        }
        .btn-dark:hover {
            background: #2d2d44;
            transform: translateY(-1px);
        }
        .btn-large {
            padding: 14px 24px;
            font-size: 15px;
            border-radius: 12px;
        }

        /* Hero Section */
        .hero {
            padding: 130px 24px 70px;
            text-align: center;
            max-width: 900px;
            margin: 0 auto;
            position: relative;
        }
        .hero-glow {
            position: absolute;
            width: 100%;
            max-width: 700px;
            height: 700px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(14, 165, 233, 0.08) 0%, transparent 70%);
            top: -150px;
            left: 50%;
            transform: translateX(-50%);
            z-index: -1;
            animation: pulse 5s ease-in-out infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 0.6; transform: translateX(-50%) scale(1); }
            50% { opacity: 1; transform: translateX(-50%) scale(1.03); }
        }
        .hero-pain {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            color: #dc2626;
            padding: 8px 16px;
            border-radius: 50px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 24px;
            border: 1px solid rgba(220, 38, 38, 0.1);
            animation: fadeInUp 0.5s ease;
        }
        .hero-pain::before {
            content: '';
            width: 8px;
            height: 8px;
            background: #ef4444;
            border-radius: 50%;
            animation: blink 1.5s ease-in-out infinite;
        }
        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.3; }
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(16px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .hero h1 {
            font-size: clamp(32px, 6vw, 52px);
            font-weight: 800;
            line-height: 1.15;
            margin-bottom: 20px;
            letter-spacing: -0.03em;
            color: #1a1a2e;
            animation: fadeInUp 0.5s ease 0.05s both;
        }
        .hero h1 .highlight { color: #dc2626; }
        .hero-subhead {
            font-size: clamp(18px, 3vw, 22px);
            font-weight: 600;
            color: #0ea5e9;
            margin-bottom: 16px;
            animation: fadeInUp 0.5s ease 0.1s both;
        }
        .hero p {
            font-size: clamp(15px, 2vw, 17px);
            color: #64748b;
            max-width: 540px;
            margin: 0 auto 32px;
            animation: fadeInUp 0.5s ease 0.15s both;
            line-height: 1.7;
        }
        .hero-cta {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
            animation: fadeInUp 0.5s ease 0.2s both;
        }
        .hero-trust {
            margin-top: 40px;
            animation: fadeInUp 0.5s ease 0.25s both;
        }
        .hero-trust-items {
            display: flex;
            justify-content: center;
            gap: 16px;
            flex-wrap: wrap;
        }
        .hero-trust-item {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            color: #64748b;
            font-weight: 500;
        }
        .hero-trust-item svg {
            width: 16px;
            height: 16px;
            color: #10b981;
            flex-shrink: 0;
        }

        /* Problem Section */
        .problem {
            padding: 70px 24px;
            background: linear-gradient(180deg, #fff 0%, #f8fafc 100%);
        }
        .problem-inner {
            max-width: 1000px;
            margin: 0 auto;
        }
        .problem-header {
            text-align: center;
            margin-bottom: 48px;
        }
        .problem-label {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #ef4444;
            margin-bottom: 12px;
        }
        .problem-header h2 {
            font-size: clamp(26px, 4vw, 36px);
            font-weight: 800;
            color: #1a1a2e;
            letter-spacing: -0.02em;
        }
        .problem-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
        }
        .problem-card {
            background: #fff;
            border-radius: 16px;
            padding: 24px;
            border: 1px solid #f1f5f9;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
            transition: all 0.25s ease;
        }
        .problem-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
        }
        .problem-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
            font-size: 22px;
        }
        .problem-card h3 {
            font-size: 32px;
            font-weight: 800;
            color: #ef4444;
            margin-bottom: 4px;
        }
        .problem-card p {
            font-size: 14px;
            color: #64748b;
            line-height: 1.6;
        }

        /* Solution Section */
        .solution {
            padding: 70px 24px;
            background: #fff;
        }
        .solution-inner {
            max-width: 1000px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 48px;
            align-items: center;
        }
        .solution-content h2 {
            font-size: clamp(26px, 4vw, 36px);
            font-weight: 800;
            color: #1a1a2e;
            margin-bottom: 20px;
            letter-spacing: -0.02em;
            line-height: 1.2;
        }
        .solution-content h2 .gradient {
            background: linear-gradient(135deg, #0ea5e9 0%, #06b6d4 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .solution-content p {
            font-size: 15px;
            color: #64748b;
            line-height: 1.7;
            margin-bottom: 24px;
        }
        .solution-benefits {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-bottom: 24px;
        }
        .solution-benefit {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
            color: #1a1a2e;
            font-weight: 500;
        }
        .solution-benefit .check {
            width: 20px;
            height: 20px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 10px;
            flex-shrink: 0;
        }
        .solution-visual {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border-radius: 20px;
            padding: 24px;
            border: 1px solid #bae6fd;
        }
        .solution-demo {
            background: #fff;
            border-radius: 14px;
            padding: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
        }
        .demo-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
            padding-bottom: 16px;
            border-bottom: 1px solid #f1f5f9;
        }
        .demo-avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 14px;
            font-weight: 600;
            flex-shrink: 0;
        }
        .demo-info h4 {
            font-size: 14px;
            font-weight: 600;
            color: #1a1a2e;
        }
        .demo-info span {
            font-size: 12px;
            color: #10b981;
            font-weight: 500;
        }
        .demo-messages {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .demo-msg {
            padding: 10px 14px;
            border-radius: 12px;
            font-size: 13px;
            line-height: 1.5;
            max-width: 85%;
        }
        .demo-msg.out {
            background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
            color: #fff;
            align-self: flex-end;
            border-bottom-right-radius: 4px;
        }
        .demo-msg.in {
            background: #f1f5f9;
            color: #1a1a2e;
            align-self: flex-start;
            border-bottom-left-radius: 4px;
        }
        .demo-time {
            font-size: 10px;
            color: #94a3b8;
            text-align: right;
            margin-top: 8px;
        }

        /* Section Common */
        .section {
            padding: 70px 24px;
            max-width: 1100px;
            margin: 0 auto;
        }
        .section-header {
            text-align: center;
            margin-bottom: 48px;
        }
        .section-label {
            display: inline-block;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #0ea5e9;
            margin-bottom: 12px;
        }
        .section-title {
            font-size: clamp(26px, 4vw, 38px);
            font-weight: 800;
            letter-spacing: -0.02em;
            margin-bottom: 14px;
            color: #1a1a2e;
        }
        .section-subtitle {
            font-size: 16px;
            color: #64748b;
            max-width: 480px;
            margin: 0 auto;
        }

        /* Steps Section */
        .steps-section { background: #f8fafc; }
        .steps {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            position: relative;
        }
        .step {
            text-align: center;
            position: relative;
            z-index: 1;
        }
        .step-icon {
            width: 80px;
            height: 80px;
            background: #fff;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
            font-size: 32px;
            border: 2px solid #e2e8f0;
            transition: all 0.25s ease;
            position: relative;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }
        .step:hover .step-icon {
            transform: scale(1.05);
            border-color: #bae6fd;
            box-shadow: 0 8px 20px rgba(14, 165, 233, 0.12);
        }
        .step-number {
            position: absolute;
            top: -6px;
            right: -6px;
            width: 24px;
            height: 24px;
            background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
            color: #fff;
            border-radius: 50%;
            font-size: 11px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .step h3 {
            font-size: 15px;
            font-weight: 700;
            margin-bottom: 6px;
            color: #1a1a2e;
        }
        .step p {
            font-size: 13px;
            color: #64748b;
            line-height: 1.5;
        }

        /* Features Section */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
        }
        .feature {
            background: #fff;
            border-radius: 16px;
            padding: 24px;
            border: 1px solid #f1f5f9;
            transition: all 0.25s ease;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
        }
        .feature:hover {
            transform: translateY(-2px);
            border-color: #e0f2fe;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
        }
        .feature-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
            font-size: 22px;
        }
        .feature h3 {
            font-size: 15px;
            font-weight: 700;
            margin-bottom: 8px;
            color: #1a1a2e;
        }
        .feature p {
            font-size: 14px;
            color: #64748b;
            line-height: 1.6;
        }

        /* Pricing Section */
        .pricing {
            background: linear-gradient(180deg, #1a1a2e 0%, #2d2d44 100%);
            padding: 70px 24px;
            position: relative;
            overflow: hidden;
        }
        .pricing::before {
            content: '';
            position: absolute;
            top: -40%;
            left: -10%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(14, 165, 233, 0.12) 0%, transparent 70%);
            pointer-events: none;
        }
        .pricing .section-title { color: #fff; }
        .pricing .section-subtitle { color: #94a3b8; }

        /* Pricing Toggle */
        .pricing-toggle {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 40px;
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
            transition: background 0.3s;
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
        .pricing-toggle-switch.annual::after {
            transform: translateX(28px);
        }
        .pricing-save-badge {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 50px;
        }

        /* Pricing Grid */
        .pricing-grid {
            max-width: 1100px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
            position: relative;
            z-index: 1;
        }
        .pricing-card {
            background: rgba(255, 255, 255, 0.06);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 32px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.08);
            position: relative;
            transition: transform 0.3s, border-color 0.3s;
            display: flex;
            flex-direction: column;
        }
        .pricing-card:hover {
            transform: translateY(-4px);
        }
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
            color: #fff;
            margin-bottom: 4px;
        }
        .pricing-card-desc {
            font-size: 14px;
            color: #94a3b8;
            margin-bottom: 20px;
        }
        .pricing-card .price {
            font-size: 48px;
            font-weight: 800;
            color: #fff;
            line-height: 1;
            margin: 16px 0;
            display: flex;
            align-items: baseline;
            justify-content: center;
            gap: 4px;
        }
        .pricing-card .price .currency { font-size: 24px; font-weight: 600; }
        .pricing-card .price .period { font-size: 16px; font-weight: 500; color: #94a3b8; }
        .pricing-card .price-annual { display: none; }
        .pricing-card .price-monthly { display: flex; }
        .pricing.annual .pricing-card .price-annual { display: flex; }
        .pricing.annual .pricing-card .price-monthly { display: none; }
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
            line-height: 1.4;
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
        .pricing-card .btn {
            width: 100%;
            padding: 14px 24px;
            font-size: 15px;
            margin-top: auto;
        }
        .pricing-card .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #fff;
        }
        .pricing-card .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.15);
        }
        .pricing-sms-note {
            font-size: 11px;
            color: #64748b;
            margin-top: 12px;
        }
        .pricing-guarantee {
            max-width: 1100px;
            margin: 40px auto 0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 14px;
            color: #94a3b8;
            position: relative;
            z-index: 1;
        }
        .pricing-guarantee svg {
            width: 20px;
            height: 20px;
            color: #10b981;
            flex-shrink: 0;
        }
        .pricing-trial-notice {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            background: rgba(14, 165, 233, 0.15);
            border: 1px solid rgba(14, 165, 233, 0.3);
            color: #7dd3fc;
            font-size: 15px;
            font-weight: 500;
            padding: 12px 24px;
            border-radius: 50px;
            margin-top: 24px;
            margin-bottom: 16px;
        }
        .pricing-trial-notice svg {
            width: 20px;
            height: 20px;
            color: #38bdf8;
            flex-shrink: 0;
        }
        @media (max-width: 900px) {
            .pricing-grid {
                grid-template-columns: 1fr;
                max-width: 400px;
            }
            .pricing-card.popular { order: -1; }
        }

        /* CTA Section */
        .cta {
            padding: 80px 24px;
            text-align: center;
            background: linear-gradient(180deg, #fafbfc 0%, #f0f9ff 100%);
        }
        .cta-content {
            max-width: 560px;
            margin: 0 auto;
        }
        .cta h2 {
            font-size: clamp(26px, 4vw, 38px);
            font-weight: 800;
            margin-bottom: 14px;
            letter-spacing: -0.02em;
            color: #1a1a2e;
        }
        .cta p {
            font-size: 16px;
            color: #64748b;
            margin-bottom: 28px;
        }
        .cta-features {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 32px;
            flex-wrap: wrap;
        }
        .cta-feature {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            color: #64748b;
            font-weight: 500;
        }
        .cta-feature svg {
            width: 18px;
            height: 18px;
            color: #10b981;
            flex-shrink: 0;
        }

        /* Footer */
        .footer {
            padding: 32px 24px;
            text-align: center;
            color: #94a3b8;
            font-size: 13px;
            background: #fafbfc;
            border-top: 1px solid #f1f5f9;
        }
        .footer-links {
            margin-bottom: 12px;
        }
        .footer-links a {
            color: #64748b;
            text-decoration: none;
            transition: color 0.2s;
        }
        .footer-links a:hover {
            color: #0ea5e9;
        }
        .footer-divider {
            margin: 0 12px;
            color: #cbd5e1;
        }

        /* Responsive */
        @media (max-width: 900px) {
            .solution-inner {
                grid-template-columns: 1fr;
                gap: 32px;
            }
            .solution-visual { order: -1; }
        }
        @media (max-width: 640px) {
            .nav-links > a:not(.btn) { display: none; }
            .lang-dropdown { display: block; }
            .hero { padding: 110px 20px 50px; }
            .problem, .solution, .section, .pricing, .cta { padding: 50px 20px; }
            .hero-cta { flex-direction: column; align-items: center; }
            .hero-cta .btn { width: 100%; max-width: 280px; }
            .steps { grid-template-columns: 1fr 1fr; }
        }
    </style>
</head>
<body>
    <nav class="nav">
        <div class="nav-inner">
            <a href="/" class="logo">
                <svg width="32" height="32" viewBox="0 0 40 40" fill="none">
                    <rect width="40" height="40" rx="10" fill="url(#logo-gradient)"/>
                    <!-- Simple callback icon: arrow returning to phone -->
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
                <a href="#problem">{{ __('nav.the_problem') }}</a>
                <a href="#como-funciona">{{ __('nav.how_it_works') }}</a>
                <a href="#pricing">{{ __('nav.pricing') }}</a>
                @php
                    $localeFlags = ['en' => '🇬🇧', 'es' => '🇪🇸', 'ro' => '🇷🇴'];
                    $localeNames = ['en' => 'English', 'es' => 'Español', 'ro' => 'Română'];
                    $currentLocale = app()->getLocale();
                @endphp
                <div class="lang-dropdown" id="lang-dropdown">
                    <button class="lang-dropdown-btn" onclick="document.getElementById('lang-dropdown').classList.toggle('open')" type="button">
                        <span class="flag">{{ $localeFlags[$currentLocale] ?? '🇬🇧' }}</span>
                        {{ strtoupper($currentLocale) }}
                        <span class="arrow">▼</span>
                    </button>
                    <div class="lang-dropdown-menu">
                        @foreach(['en', 'es', 'ro'] as $loc)
                            <a href="{{ route('locale.switch', $loc) }}" class="{{ $currentLocale === $loc ? 'active' : '' }}">
                                <span class="flag">{{ $localeFlags[$loc] }}</span>
                                <span class="lang-label">{{ $localeNames[$loc] }}</span>
                                <span style="font-size: 11px; color: #94a3b8;">{{ strtoupper($loc) }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
                <a href="{{ route('login') }}" class="btn btn-secondary">{{ __('nav.login') }}</a>
            </div>
        </div>
    </nav>

    <section class="hero">
        <div class="hero-glow"></div>
        <div class="hero-pain">{{ __('landing.hero.badge') }}</div>
        <h1>{{ __('landing.hero.title') }} <span class="highlight">{{ __('landing.hero.title_highlight') }}</span></h1>
        <p class="hero-subhead">{{ __('landing.hero.subhead') }}</p>
        <p>{{ __('landing.hero.description') }}</p>
        <div class="hero-cta">
            <a href="#pricing" class="btn btn-primary btn-large">
                {{ __('landing.hero.cta_primary') }}
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
            </a>
            <a href="#como-funciona" class="btn btn-secondary btn-large">{{ __('landing.hero.cta_secondary') }}</a>
        </div>
        <div class="hero-trust">
            <div class="hero-trust-items">
                <div class="hero-trust-item">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    {{ __('landing.hero.trust_trial') }}
                </div>
                <div class="hero-trust-item">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    {{ __('landing.hero.trust_card') }}
                </div>
                <div class="hero-trust-item">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    {{ __('landing.hero.trust_setup') }}
                </div>
            </div>
        </div>
    </section>

    <section class="problem" id="problem">
        <div class="problem-inner">
            <div class="problem-header">
                <div class="problem-label">{{ __('landing.problem.label') }}</div>
                <h2>{{ __('landing.problem.title') }}</h2>
            </div>
            <div class="problem-grid">
                <div class="problem-card">
                    <div class="problem-icon">📞</div>
                    <h3>{{ __('landing.problem.stat1_value') }}</h3>
                    <p>{{ __('landing.problem.stat1_text') }}</p>
                </div>
                <div class="problem-card">
                    <div class="problem-icon">💸</div>
                    <h3>{{ __('landing.problem.stat2_value') }}</h3>
                    <p>{{ __('landing.problem.stat2_text') }}</p>
                </div>
                <div class="problem-card">
                    <div class="problem-icon">😔</div>
                    <h3>{{ __('landing.problem.stat3_value') }}</h3>
                    <p>{{ __('landing.problem.stat3_text') }}</p>
                </div>
            </div>
        </div>
    </section>

    <section class="solution">
        <div class="solution-inner">
            <div class="solution-content">
                <h2>{{ __('landing.solution.title') }} <span class="gradient">{{ __('landing.solution.title_highlight') }}</span> {{ __('landing.solution.title_suffix') }}</h2>
                <p>{{ __('landing.solution.description') }}</p>
                <div class="solution-benefits">
                    <div class="solution-benefit"><span class="check">✓</span> {{ __('landing.solution.benefit1') }}</div>
                    <div class="solution-benefit"><span class="check">✓</span> {{ __('landing.solution.benefit2') }}</div>
                    <div class="solution-benefit"><span class="check">✓</span> {{ __('landing.solution.benefit3') }}</div>
                    <div class="solution-benefit"><span class="check">✓</span> {{ __('landing.solution.benefit4') }}</div>
                </div>
                <a href="#pricing" class="btn btn-primary">{{ __('landing.solution.cta') }}</a>
            </div>
            <div class="solution-visual">
                <div class="solution-demo">
                    <div class="demo-header">
                        <div class="demo-avatar">MG</div>
                        <div class="demo-info">
                            <h4>{{ __('landing.solution.demo_phone') }}</h4>
                            <span>{{ __('landing.solution.demo_responded') }}</span>
                        </div>
                    </div>
                    <div class="demo-messages">
                        <div class="demo-msg out">{{ __('landing.solution.demo_msg1') }}</div>
                        <div class="demo-msg in">{{ __('landing.solution.demo_msg2') }}</div>
                        <div class="demo-msg out">{{ __('landing.solution.demo_msg3') }}</div>
                        <div class="demo-msg in">{{ __('landing.solution.demo_msg4') }}</div>
                    </div>
                    <div class="demo-time">{{ __('landing.solution.demo_booked') }}</div>
                </div>
            </div>
        </div>
    </section>

    <section class="section steps-section" id="como-funciona">
        <div class="section-header">
            <div class="section-label">{{ __('landing.steps.label') }}</div>
            <h2 class="section-title">{{ __('landing.steps.title') }}</h2>
            <p class="section-subtitle">{{ __('landing.steps.subtitle') }}</p>
        </div>
        <div class="steps">
            <div class="step">
                <div class="step-icon"><span class="step-number">1</span>📞</div>
                <h3>{{ __('landing.steps.step1_title') }}</h3>
                <p>{{ __('landing.steps.step1_text') }}</p>
            </div>
            <div class="step">
                <div class="step-icon"><span class="step-number">2</span>💬</div>
                <h3>{{ __('landing.steps.step2_title') }}</h3>
                <p>{{ __('landing.steps.step2_text') }}</p>
            </div>
            <div class="step">
                <div class="step-icon"><span class="step-number">3</span>💼</div>
                <h3>{{ __('landing.steps.step3_title') }}</h3>
                <p>{{ __('landing.steps.step3_text') }}</p>
            </div>
            <div class="step">
                <div class="step-icon"><span class="step-number">4</span>✅</div>
                <h3>{{ __('landing.steps.step4_title') }}</h3>
                <p>{{ __('landing.steps.step4_text') }}</p>
            </div>
        </div>
    </section>

    <section class="section" id="features">
        <div class="section-header">
            <div class="section-label">{{ __('landing.features.label') }}</div>
            <h2 class="section-title">{{ __('landing.features.title') }}</h2>
            <p class="section-subtitle">{{ __('landing.features.subtitle') }}</p>
        </div>
        <div class="features-grid">
            <div class="feature">
                <div class="feature-icon">⚡</div>
                <h3>{{ __('landing.features.feature1_title') }}</h3>
                <p>{{ __('landing.features.feature1_text') }}</p>
            </div>
            <div class="feature">
                <div class="feature-icon">💬</div>
                <h3>{{ __('landing.features.feature2_title') }}</h3>
                <p>{{ __('landing.features.feature2_text') }}</p>
            </div>
            <div class="feature">
                <div class="feature-icon">📊</div>
                <h3>{{ __('landing.features.feature3_title') }}</h3>
                <p>{{ __('landing.features.feature3_text') }}</p>
            </div>
            <div class="feature">
                <div class="feature-icon">🎯</div>
                <h3>{{ __('landing.features.feature4_title') }}</h3>
                <p>{{ __('landing.features.feature4_text') }}</p>
            </div>
            <div class="feature">
                <div class="feature-icon">🔔</div>
                <h3>{{ __('landing.features.feature5_title') }}</h3>
                <p>{{ __('landing.features.feature5_text') }}</p>
            </div>
            <div class="feature">
                <div class="feature-icon">🌍</div>
                <h3>{{ __('landing.features.feature6_title') }}</h3>
                <p>{{ __('landing.features.feature6_text') }}</p>
            </div>
        </div>
    </section>

    <section class="pricing" id="pricing">
        <div class="section-header">
            <div class="section-label">{{ __('landing.pricing.label') }}</div>
            <h2 class="section-title">{{ __('landing.pricing.title') }}</h2>
            <p class="section-subtitle">{{ __('landing.pricing.subtitle') }}</p>
        </div>

        <!-- Toggle Mensual/Anual -->
        <div class="pricing-toggle">
            <span class="pricing-toggle-label active" data-period="monthly">{{ __('landing.pricing.toggle_monthly') }}</span>
            <div class="pricing-toggle-switch" onclick="togglePricing()"></div>
            <span class="pricing-toggle-label" data-period="annual">{{ __('landing.pricing.toggle_annual') }}</span>
            <span class="pricing-save-badge">{{ __('landing.pricing.save_badge') }}</span>
        </div>

        <div class="currency-selector" style="display: flex; align-items: center; justify-content: center; gap: 6px; margin-bottom: 32px;">
            <a href="{{ route('currency.switch', 'eur') }}" style="padding: 6px 14px; border-radius: 50px; font-size: 13px; font-weight: 600; text-decoration: none; transition: all 0.2s; {{ $currentCurrency === 'eur' ? 'background: rgba(14,165,233,0.2); border: 1px solid #0ea5e9; color: #7dd3fc;' : 'color: #94a3b8; border: 1px solid rgba(255,255,255,0.1);' }}">EUR (€)</a>
            <a href="{{ route('currency.switch', 'ron') }}" style="padding: 6px 14px; border-radius: 50px; font-size: 13px; font-weight: 600; text-decoration: none; transition: all 0.2s; {{ $currentCurrency === 'ron' ? 'background: rgba(14,165,233,0.2); border: 1px solid #0ea5e9; color: #7dd3fc;' : 'color: #94a3b8; border: 1px solid rgba(255,255,255,0.1);' }}">RON</a>
        </div>

        @php $prices = config('pricing.plans'); @endphp

        <div class="pricing-grid">
            <!-- Starter Plan -->
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
                <a href="{{ route('register', ['plan' => 'starter', 'interval' => 'monthly']) }}" class="btn btn-secondary plan-btn" data-plan="starter">{{ __('landing.pricing.cta') }}</a>
                <p class="pricing-sms-note">{{ $prices['starter'][$currentCurrency]['sms_extra'] }}</p>
            </div>

            <!-- Growth Plan (Popular) -->
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
                <a href="{{ route('register', ['plan' => 'growth', 'interval' => 'monthly']) }}" class="btn btn-primary plan-btn" data-plan="growth">{{ __('landing.pricing.cta') }}</a>
                <p class="pricing-sms-note">{{ $prices['growth'][$currentCurrency]['sms_extra'] }}</p>
            </div>

            <!-- Pro Plan -->
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
                <a href="{{ route('register', ['plan' => 'pro', 'interval' => 'monthly']) }}" class="btn btn-secondary plan-btn" data-plan="pro">{{ __('landing.pricing.cta') }}</a>
                <p class="pricing-sms-note">{{ $prices['pro'][$currentCurrency]['sms_extra'] }}</p>
            </div>
        </div>

        <div class="pricing-trial-notice">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ __('landing.pricing.no_charge_until', ['date' => now()->addDays(14)->format('M d, Y')]) }}
        </div>

        <div class="pricing-guarantee">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            {{ __('landing.pricing.guarantee') }}
        </div>
    </section>

    <script>
        function togglePricing() {
            const pricingSection = document.querySelector('.pricing');
            const toggle = document.querySelector('.pricing-toggle-switch');
            const labels = document.querySelectorAll('.pricing-toggle-label');
            const annualTexts = document.querySelectorAll('.annual-only');
            const planButtons = document.querySelectorAll('.plan-btn');

            pricingSection.classList.toggle('annual');
            toggle.classList.toggle('annual');

            const isAnnual = pricingSection.classList.contains('annual');
            const interval = isAnnual ? 'annual' : 'monthly';

            labels.forEach(label => {
                label.classList.toggle('active', label.dataset.period === interval);
            });

            annualTexts.forEach(text => {
                text.style.visibility = isAnnual ? 'visible' : 'hidden';
            });

            // Update plan button URLs
            planButtons.forEach(btn => {
                const url = new URL(btn.href);
                url.searchParams.set('interval', interval);
                btn.href = url.toString();
            });
        }

        // Initialize annual text visibility
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.annual-only').forEach(text => {
                text.style.visibility = 'hidden';
            });
        });
    </script>

    <section class="cta">
        <div class="cta-content">
            <h2>{{ __('landing.cta.title') }}</h2>
            <p>{{ __('landing.cta.subtitle') }}</p>
            <a href="#pricing" class="btn btn-dark btn-large">
                {{ __('landing.cta.button') }}
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            </a>
            <div class="cta-features">
                <div class="cta-feature">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    {{ __('landing.hero.trust_trial') }}
                </div>
                <div class="cta-feature">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    {{ __('landing.hero.trust_card') }}
                </div>
                <div class="cta-feature">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    {{ __('landing.hero.trust_setup') }}
                </div>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="footer-links">
            <a href="{{ route('terms') }}">{{ __('legal.terms_link') }}</a>
            <span class="footer-divider">|</span>
            <a href="{{ route('privacy') }}">{{ __('legal.privacy_link') }}</a>
            <span class="footer-divider">|</span>
            <a href="{{ route('gdpr') }}">{{ __('legal.gdpr_link') }}</a>
            <span class="footer-divider">|</span>
            <a href="{{ route('cookies') }}">{{ __('legal.cookies_link') }}</a>
            <span class="footer-divider">|</span>
            <a href="{{ route('dpa') }}">{{ __('legal.dpa_link') }}</a>
        </div>
        <p>{{ __('landing.footer.copyright', ['year' => date('Y')]) }}</p>
    </footer>
<script>
    document.addEventListener('click', function(e) {
        var dd = document.getElementById('lang-dropdown');
        if (dd && !dd.contains(e.target)) dd.classList.remove('open');
    });
</script>
</body>
</html>
