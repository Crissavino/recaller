<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('subscription.title') }}
        </h2>
    </x-slot>

    <style>
        .alert {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 16px;
            border-radius: 12px;
            margin-bottom: 24px;
            font-size: 14px;
            animation: slideDown 0.3s ease-out;
        }
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .alert-success {
            background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
            border: 1px solid #a7f3d0;
            color: #065f46;
        }
        .alert-error {
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            border: 1px solid #fecaca;
            color: #991b1b;
        }
        .alert-info {
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            border: 1px solid #bfdbfe;
            color: #1e40af;
        }
        .alert-icon {
            flex-shrink: 0;
            width: 24px;
            height: 24px;
        }
        .alert-icon svg {
            width: 24px;
            height: 24px;
        }
        .alert-content {
            flex: 1;
            font-weight: 500;
            line-height: 1.5;
        }
        .alert-close {
            flex-shrink: 0;
            width: 20px;
            height: 20px;
            background: none;
            border: none;
            cursor: pointer;
            opacity: 0.5;
            transition: opacity 0.2s;
            padding: 0;
        }
        .alert-close:hover {
            opacity: 1;
        }
        .alert-close svg {
            width: 20px;
            height: 20px;
        }
    </style>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            {{-- Flash Messages --}}
            @if (session('success'))
                <div class="alert alert-success" x-data="{ show: true }" x-show="show" x-transition>
                    <div class="alert-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="alert-content">{{ session('success') }}</div>
                    <button type="button" class="alert-close" @click="show = false">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-error" x-data="{ show: true }" x-show="show" x-transition>
                    <div class="alert-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="alert-content">{{ session('error') }}</div>
                    <button type="button" class="alert-close" @click="show = false">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            @endif

            @if (session('info'))
                <div class="alert alert-info" x-data="{ show: true }" x-show="show" x-transition>
                    <div class="alert-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="alert-content">{{ session('info') }}</div>
                    <button type="button" class="alert-close" @click="show = false">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            @endif

            {{-- Current Plan Card --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('subscription.current_plan') }}</h3>

                    @if ($subscription)
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-2xl font-bold text-gray-900">
                                    {{ $currentPlan->name ?? 'Unknown Plan' }}
                                </p>
                                <p class="text-sm text-gray-500 mt-1">
                                    @if ($subscription->onTrial())
                                        {{ __('subscription.trial_ends', ['date' => $subscription->trial_ends_at->format('M d, Y')]) }}
                                    @elseif ($subscription->onGracePeriod())
                                        {{ __('subscription.cancels_at', ['date' => $subscription->ends_at->format('M d, Y')]) }}
                                    @elseif ($subscription->current_period_end)
                                        {{ __('subscription.renews_at', ['date' => $subscription->current_period_end->format('M d, Y')]) }}
                                    @endif
                                </p>
                            </div>

                            <div class="flex items-center gap-2">
                                @if ($subscription->onGracePeriod())
                                    <form action="{{ route('subscription.resume') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                                            {{ __('subscription.resume') }}
                                        </button>
                                    </form>
                                @else
                                    <a href="{{ route('subscription.billing-portal') }}"
                                       class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                                        {{ __('subscription.manage_billing') }}
                                    </a>
                                @endif
                            </div>
                        </div>

                        {{-- Subscription Status Badge --}}
                        <div class="mt-4">
                            @if ($subscription->onTrial())
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                    {{ __('subscription.status_trial') }}
                                </span>
                            @elseif ($subscription->onGracePeriod())
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                    {{ __('subscription.status_cancelling') }}
                                </span>
                            @elseif ($subscription->isActive())
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    {{ __('subscription.status_active') }}
                                </span>
                            @elseif ($subscription->isPastDue())
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                    {{ __('subscription.status_past_due') }}
                                </span>
                            @endif

                            <span class="ml-2 text-sm text-gray-500">
                                {{ $subscription->interval === 'annual' ? __('subscription.billed_annually') : __('subscription.billed_monthly') }}
                            </span>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <p class="text-gray-500 mb-6">{{ __('subscription.no_subscription') }}</p>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-left">
                                @foreach ($plans as $plan)
                                    <div class="border border-gray-200 rounded-lg p-5 hover:border-sky-300 hover:shadow-md transition">
                                        <h4 class="font-semibold text-gray-900 text-lg">{{ $plan->name }}</h4>
                                        <p class="text-3xl font-bold text-gray-900 mt-2">
                                            {{ $currencySymbol }}{{ config('pricing.plans.' . $plan->slug . '.' . $currentCurrency . '.monthly') }}
                                            <span class="text-sm font-normal text-gray-500">/{{ __('subscription.month') }}</span>
                                        </p>
                                        <p class="text-sm text-gray-500 mt-1 mb-4">
                                            {{ $plan->features['sms_included'] ?? 0 }} SMS/{{ __('subscription.month') }}
                                        </p>
                                        <a href="{{ route('subscription.checkout', ['plan' => $plan->slug, 'interval' => 'monthly']) }}"
                                           class="block w-full text-center px-4 py-3 bg-sky-500 text-white font-semibold rounded-lg hover:bg-sky-600 transition">
                                            {{ __('subscription.subscribe_now') }}
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Plans Comparison --}}
            @if ($subscription && !$subscription->onGracePeriod())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('subscription.change_plan') }}</h3>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            @foreach ($plans as $plan)
                                <div class="border rounded-lg p-4 {{ $currentPlan?->id === $plan->id ? 'border-sky-500 bg-sky-50' : 'border-gray-200' }}">
                                    <h4 class="font-semibold text-gray-900">{{ $plan->name }}</h4>
                                    <p class="text-2xl font-bold text-gray-900 mt-2">
                                        {{ $currencySymbol }}{{ config('pricing.plans.' . $plan->slug . '.' . $currentCurrency . '.monthly') }}
                                        <span class="text-sm font-normal text-gray-500">/{{ __('subscription.month') }}</span>
                                    </p>
                                    <p class="text-sm text-gray-500 mt-1">
                                        {{ $plan->features['sms_included'] ?? 0 }} SMS/{{ __('subscription.month') }}
                                    </p>

                                    @if ($currentPlan?->id === $plan->id)
                                        <span class="inline-block mt-3 text-sm text-sky-600 font-medium">
                                            {{ __('subscription.current') }}
                                        </span>
                                    @else
                                        <form action="{{ route('subscription.change-plan') }}" method="POST" class="mt-3">
                                            @csrf
                                            <input type="hidden" name="plan" value="{{ $plan->slug }}">
                                            <input type="hidden" name="interval" value="{{ $subscription->interval }}">
                                            <button type="submit" class="text-sm text-sky-600 hover:text-sky-700 font-medium">
                                                {{ __('subscription.switch_to') }} {{ $plan->name }} →
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Cancel Subscription --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-red-800 mb-2">{{ __('settings.danger_zone') }}</h3>
                        <p class="text-sm text-gray-500 mb-4">{{ __('subscription.cancel_description') }}</p>

                        <button
                            type="button"
                            onclick="document.getElementById('cancel-modal').style.display = 'flex'"
                            class="px-4 py-2 bg-red-50 text-red-700 border border-red-200 rounded-lg hover:bg-red-100 transition font-medium"
                        >
                            {{ __('subscription.cancel_button') }}
                        </button>
                    </div>
                </div>
            @endif

            {{-- Cancel Modal --}}
            @if ($subscription && !$subscription->onGracePeriod())
            <style>
                .modal-backdrop {
                    position: fixed;
                    inset: 0;
                    background: rgba(0, 0, 0, 0.6);
                    backdrop-filter: blur(4px);
                    z-index: 50;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    padding: 16px;
                    animation: fadeIn 0.2s ease-out;
                }
                @keyframes fadeIn {
                    from { opacity: 0; }
                    to { opacity: 1; }
                }
                .modal-content {
                    background: #fff;
                    border-radius: 20px;
                    max-width: 440px;
                    width: 100%;
                    padding: 28px;
                    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
                    animation: slideUp 0.3s ease-out;
                }
                @keyframes slideUp {
                    from { opacity: 0; transform: translateY(20px) scale(0.95); }
                    to { opacity: 1; transform: translateY(0) scale(1); }
                }
                .modal-icon {
                    width: 56px;
                    height: 56px;
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    margin: 0 auto 20px;
                    background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
                }
                .modal-icon svg {
                    color: #dc2626;
                }
                .modal-title {
                    font-size: 20px;
                    font-weight: 700;
                    color: #111827;
                    text-align: center;
                    margin: 0 0 12px 0;
                }
                .modal-description {
                    font-size: 14px;
                    color: #6b7280;
                    text-align: center;
                    margin: 0 0 16px 0;
                    line-height: 1.6;
                }
                .modal-warning-list {
                    background: #fef2f2;
                    border-radius: 12px;
                    padding: 16px;
                    margin: 0 0 24px 0;
                }
                .modal-warning-list ul {
                    margin: 0;
                    padding: 0;
                    list-style: none;
                }
                .modal-warning-list li {
                    display: flex;
                    align-items: flex-start;
                    gap: 10px;
                    font-size: 13px;
                    color: #991b1b;
                    padding: 6px 0;
                }
                .modal-warning-list li::before {
                    content: '×';
                    font-weight: bold;
                    font-size: 16px;
                    line-height: 1;
                }
                .modal-actions {
                    display: flex;
                    gap: 12px;
                }
                .modal-actions button, .modal-actions .btn-form {
                    flex: 1;
                    padding: 14px 20px;
                    border-radius: 12px;
                    font-size: 15px;
                    font-weight: 600;
                    cursor: pointer;
                    transition: all 0.2s;
                }
                .btn-cancel-modal {
                    background: #f3f4f6;
                    border: none;
                    color: #374151;
                }
                .btn-cancel-modal:hover {
                    background: #e5e7eb;
                }
                .btn-danger {
                    background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
                    border: none;
                    color: #fff;
                    width: 100%;
                }
                .btn-danger:hover {
                    background: linear-gradient(135deg, #b91c1c 0%, #991b1b 100%);
                    transform: translateY(-1px);
                }
            </style>
            <div id="cancel-modal" class="modal-backdrop" style="display: none;" onclick="if(event.target === this) this.style.display = 'none'">
                <div class="modal-content">
                    <div class="modal-icon">
                        <svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <h3 class="modal-title">{{ __('settings.cancel_subscription_title') }}</h3>
                    <p class="modal-description">{{ __('settings.cancel_subscription_warning') }}</p>
                    <div class="modal-warning-list">
                        <ul>
                            <li>{{ __('settings.cancel_warning_1') }}</li>
                            <li>{{ __('settings.cancel_warning_2') }}</li>
                            <li>{{ __('settings.cancel_warning_3') }}</li>
                        </ul>
                    </div>
                    <div class="modal-actions">
                        <button type="button" class="btn-cancel-modal" onclick="document.getElementById('cancel-modal').style.display = 'none'">
                            {{ __('settings.keep_subscription') }}
                        </button>
                        <form action="{{ route('subscription.cancel') }}" method="POST" class="btn-form" style="margin: 0;">
                            @csrf
                            <button type="submit" class="btn-danger">
                                {{ __('settings.confirm_cancel') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endif

            {{-- Payment Method --}}
            @php
                $defaultPaymentMethod = $clinic->defaultPaymentMethodFor('stripe');
            @endphp
            @if ($subscription && $defaultPaymentMethod)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('subscription.payment_method') }}</h3>

                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-8 bg-gray-100 rounded flex items-center justify-center">
                                    <span class="text-xs font-semibold text-gray-600">{{ strtoupper($defaultPaymentMethod->brand ?? $defaultPaymentMethod->type) }}</span>
                                </div>
                                <span class="text-gray-700">•••• {{ $defaultPaymentMethod->last_four }}</span>
                                @if ($defaultPaymentMethod->expiration)
                                    <span class="text-sm text-gray-500">{{ __('subscription.expires') }} {{ $defaultPaymentMethod->expiration }}</span>
                                @endif
                            </div>

                            <a href="{{ route('subscription.billing-portal') }}"
                               class="text-sm text-sky-600 hover:text-sky-700 font-medium">
                                {{ __('subscription.update_payment') }}
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Invoice History --}}
            @if ($subscription)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">{{ __('subscription.billing_history') }}</h3>
                                <p class="text-sm text-gray-500 mt-1">{{ __('subscription.no_invoices_description') }}</p>
                            </div>
                            <a href="{{ route('subscription.invoices') }}"
                               class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-sm font-medium">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                {{ __('subscription.view_invoices') }}
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
