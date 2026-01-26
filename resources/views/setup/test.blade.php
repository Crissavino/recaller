<x-setup.layout :step="$step" :totalSteps="$totalSteps">
    <div class="wizard-card">
        <div class="wizard-icon" style="background: linear-gradient(135deg, #ede9fe 0%, #ddd6fe 100%);">
            <svg width="40" height="40" fill="none" stroke="#8b5cf6" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
            </svg>
        </div>

        <h1 class="wizard-title">{{ __('setup.test_title') }}</h1>
        <p class="wizard-subtitle">{{ __('setup.test_subtitle') }}</p>

        @if(session('success'))
            <div class="alert alert-success">
                <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">
                <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                {{ session('error') }}
            </div>
        @endif

        @if($activePhone)
            <form action="{{ route('setup.send.test') }}" method="POST" class="wizard-form">
                @csrf

                <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px; padding: 16px; margin-bottom: 8px;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div style="width: 40px; height: 40px; background: #10b981; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                            <svg width="20" height="20" fill="white" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div>
                            <p style="font-weight: 600; color: #065f46; margin: 0;">{{ __('setup.phone_ready') }}</p>
                            <p style="font-size: 14px; color: #047857; margin: 0;">{{ $activePhone->phone_number }}</p>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">{{ __('setup.test_phone_number') }}</label>
                    <input type="tel" name="phone_number" class="form-input" required placeholder="+1 (555) 123-4567">
                    <span class="form-help">{{ __('setup.test_phone_help') }}</span>
                </div>

                @if($messagePreview)
                    <div style="background: #f9fafb; border-radius: 12px; padding: 16px;">
                        <p style="font-size: 13px; font-weight: 500; color: #6b7280; margin: 0 0 8px 0;">{{ __('setup.message_preview') }}</p>
                        <p style="font-size: 14px; color: #374151; margin: 0; white-space: pre-wrap;">{{ $messagePreview }}</p>
                    </div>
                @endif

                <div class="wizard-actions">
                    <a href="{{ route('setup.templates') }}" class="btn btn-outline">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/>
                        </svg>
                        {{ __('common.back') }}
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        {{ __('setup.send_test') }}
                    </button>
                </div>
            </form>
        @else
            <div style="background: #fef3c7; border: 1px solid #fde68a; border-radius: 12px; padding: 20px; text-align: center;">
                <div style="width: 48px; height: 48px; background: #f59e0b; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px;">
                    <svg width="24" height="24" fill="white" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <p style="font-weight: 600; color: #92400e; margin: 0 0 8px 0;">{{ __('setup.no_phone_title') }}</p>
                <p style="font-size: 14px; color: #a16207; margin: 0;">{{ __('setup.no_phone_desc') }}</p>
            </div>

            <div class="wizard-actions" style="margin-top: 24px;">
                <a href="{{ route('setup.templates') }}" class="btn btn-outline">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/>
                    </svg>
                    {{ __('common.back') }}
                </a>
                <a href="{{ route('setup.complete') }}" class="btn btn-primary">
                    {{ __('common.next') }}
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </a>
            </div>
        @endif

        <div class="skip-link">
            <form action="{{ route('setup.skip.test') }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit">{{ __('setup.skip_step') }}</button>
            </form>
        </div>
    </div>
</x-setup.layout>
