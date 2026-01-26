<x-setup.layout :step="$step" :totalSteps="$totalSteps">
    <div class="wizard-card">
        @if(session('success'))
            <div class="alert alert-success" style="margin-bottom: 24px;">
                <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        <div class="wizard-icon" style="background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);">
            <svg width="40" height="40" fill="none" stroke="#0ea5e9" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
            </svg>
        </div>

        <h1 class="wizard-title">{{ __('setup.welcome_title', ['name' => $user->name]) }}</h1>
        <p class="wizard-subtitle">{{ __('setup.welcome_subtitle') }}</p>

        <div style="background: #f0f9ff; border-radius: 16px; padding: 24px; margin-bottom: 24px;">
            <div style="display: flex; flex-direction: column; gap: 16px;">
                <div style="display: flex; align-items: flex-start; gap: 12px;">
                    <div style="width: 32px; height: 32px; background: #0ea5e9; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <svg width="16" height="16" fill="white" viewBox="0 0 20 20">
                            <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
                        </svg>
                    </div>
                    <div>
                        <p style="font-weight: 600; color: #111827; margin: 0 0 4px 0;">{{ __('setup.feature_1_title') }}</p>
                        <p style="font-size: 14px; color: #6b7280; margin: 0;">{{ __('setup.feature_1_desc') }}</p>
                    </div>
                </div>

                <div style="display: flex; align-items: flex-start; gap: 12px;">
                    <div style="width: 32px; height: 32px; background: #10b981; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <svg width="16" height="16" fill="white" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zM7 8H5v2h2V8zm2 0h2v2H9V8zm6 0h-2v2h2V8z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div>
                        <p style="font-weight: 600; color: #111827; margin: 0 0 4px 0;">{{ __('setup.feature_2_title') }}</p>
                        <p style="font-size: 14px; color: #6b7280; margin: 0;">{{ __('setup.feature_2_desc') }}</p>
                    </div>
                </div>

                <div style="display: flex; align-items: flex-start; gap: 12px;">
                    <div style="width: 32px; height: 32px; background: #8b5cf6; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <svg width="16" height="16" fill="white" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11.707 4.707a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293a1 1 0 00-1.414 0l-2 2a1 1 0 101.414 1.414L8 10.414l1.293 1.293a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div>
                        <p style="font-weight: 600; color: #111827; margin: 0 0 4px 0;">{{ __('setup.feature_3_title') }}</p>
                        <p style="font-size: 14px; color: #6b7280; margin: 0;">{{ __('setup.feature_3_desc') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="wizard-actions">
            <a href="{{ route('setup.clinic') }}" class="btn btn-primary">
                {{ __('setup.get_started') }}
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
            </a>
        </div>
    </div>
</x-setup.layout>
