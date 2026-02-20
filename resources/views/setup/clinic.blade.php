<x-setup.layout :step="$step" :totalSteps="$totalSteps">
    <div class="wizard-card">
        <div class="wizard-icon" style="background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);">
            <svg width="40" height="40" fill="none" stroke="#10b981" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
        </div>

        <h1 class="wizard-title">{{ __('setup.clinic_title') }}</h1>
        <p class="wizard-subtitle">{{ __('setup.clinic_subtitle') }}</p>

        @if($errors->any())
            <div class="alert alert-error">
                <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('setup.store.clinic') }}" method="POST" class="wizard-form">
            @csrf

            <div class="form-group">
                <label class="form-label">{{ __('setup.clinic_name') }} *</label>
                <input type="text" name="name" class="form-input" value="{{ old('name', $clinic->name) }}" required placeholder="{{ __('setup.clinic_name_placeholder') }}">
            </div>

            <div class="form-group">
                <label class="form-label">{{ __('setup.clinic_phone') }} *</label>
                <input type="tel" name="phone" class="form-input" value="{{ old('phone', $clinic->phone) }}" required placeholder="{{ __('setup.clinic_phone_placeholder') }}">
                <span class="form-help">{{ __('setup.clinic_phone_help') }}</span>
            </div>

            <div class="form-group">
                <label class="form-label">{{ __('setup.business_hours') }}</label>
                <input type="text" name="business_hours_text" class="form-input" value="{{ old('business_hours_text', $clinic->settings?->business_hours_text) }}" placeholder="{{ __('setup.business_hours_placeholder') }}">
                <span class="form-help">{{ __('setup.business_hours_help') }}</span>
            </div>

            <div class="wizard-actions">
                <a href="{{ route('setup.welcome') }}" class="btn btn-outline">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/>
                    </svg>
                    {{ __('common.back') }}
                </a>
                <button type="submit" class="btn btn-primary">
                    {{ __('common.next') }}
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </button>
            </div>
        </form>
    </div>
</x-setup.layout>
