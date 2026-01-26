<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ __('setup.title') }} - {{ config('app.name', 'Recaller') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body style="margin: 0; padding: 0; min-height: 100vh; background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 50%, #f0fdf4 100%); font-family: 'Figtree', sans-serif;">
    <style>
        .wizard-container {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .wizard-header {
            padding: 20px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .wizard-logo {
            font-size: 24px;
            font-weight: 700;
            color: #0ea5e9;
        }
        .wizard-progress {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .progress-step {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s;
        }
        .progress-step.completed {
            background: #10b981;
            color: #fff;
        }
        .progress-step.active {
            background: #0ea5e9;
            color: #fff;
            box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.2);
        }
        .progress-step.pending {
            background: #e5e7eb;
            color: #9ca3af;
        }
        .progress-line {
            width: 24px;
            height: 3px;
            background: #e5e7eb;
            border-radius: 2px;
        }
        .progress-line.completed {
            background: #10b981;
        }
        .wizard-content {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }
        .wizard-card {
            background: #fff;
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.1);
            max-width: 560px;
            width: 100%;
            padding: 48px;
            animation: slideUp 0.5s ease-out;
        }
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .wizard-icon {
            width: 80px;
            height: 80px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
        }
        .wizard-title {
            font-size: 28px;
            font-weight: 700;
            color: #111827;
            text-align: center;
            margin: 0 0 12px 0;
        }
        .wizard-subtitle {
            font-size: 16px;
            color: #6b7280;
            text-align: center;
            margin: 0 0 32px 0;
            line-height: 1.6;
        }
        .wizard-form {
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
            font-weight: 500;
            color: #374151;
        }
        .form-input {
            padding: 14px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 15px;
            transition: all 0.2s;
            outline: none;
        }
        .form-input:focus {
            border-color: #0ea5e9;
            box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.1);
        }
        .form-input::placeholder {
            color: #9ca3af;
        }
        .form-textarea {
            resize: vertical;
            min-height: 100px;
        }
        .form-help {
            font-size: 13px;
            color: #9ca3af;
        }
        .wizard-actions {
            display: flex;
            gap: 12px;
            margin-top: 32px;
        }
        .btn {
            flex: 1;
            padding: 16px 24px;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            text-align: center;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .btn-primary {
            background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
            color: #fff;
            border: none;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -5px rgba(14, 165, 233, 0.4);
        }
        .btn-secondary {
            background: #f3f4f6;
            color: #6b7280;
            border: none;
        }
        .btn-secondary:hover {
            background: #e5e7eb;
        }
        .btn-outline {
            background: transparent;
            color: #6b7280;
            border: 2px solid #e5e7eb;
        }
        .btn-outline:hover {
            border-color: #d1d5db;
            background: #f9fafb;
        }
        .alert {
            padding: 14px 16px;
            border-radius: 12px;
            font-size: 14px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .alert-success {
            background: #d1fae5;
            color: #065f46;
        }
        .alert-error {
            background: #fee2e2;
            color: #991b1b;
        }
        .skip-link {
            text-align: center;
            margin-top: 20px;
        }
        .skip-link a, .skip-link button {
            color: #9ca3af;
            font-size: 14px;
            text-decoration: none;
            background: none;
            border: none;
            cursor: pointer;
        }
        .skip-link a:hover, .skip-link button:hover {
            color: #6b7280;
            text-decoration: underline;
        }
        @media (max-width: 640px) {
            .wizard-card {
                padding: 32px 24px;
            }
            .wizard-title {
                font-size: 24px;
            }
            .wizard-actions {
                flex-direction: column;
            }
            .progress-step {
                width: 28px;
                height: 28px;
                font-size: 12px;
            }
            .progress-line {
                width: 16px;
            }
        }
    </style>

    <div class="wizard-container">
        <header class="wizard-header">
            <div class="wizard-logo">Recaller</div>
            <div class="wizard-progress">
                @for($i = 1; $i <= $totalSteps; $i++)
                    @if($i > 1)
                        <div class="progress-line {{ $i <= $step ? 'completed' : '' }}"></div>
                    @endif
                    <div class="progress-step {{ $i < $step ? 'completed' : ($i == $step ? 'active' : 'pending') }}">
                        @if($i < $step)
                            <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        @else
                            {{ $i }}
                        @endif
                    </div>
                @endfor
            </div>
        </header>

        <main class="wizard-content">
            {{ $slot }}
        </main>
    </div>
</body>
</html>
