<x-setup.layout :step="$step" :totalSteps="$totalSteps">
    <div class="wizard-card">
        <div class="wizard-icon" style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);">
            <svg width="40" height="40" fill="none" stroke="#f59e0b" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
            </svg>
        </div>

        <h1 class="wizard-title">{{ __('setup.templates_title') }}</h1>
        <p class="wizard-subtitle">{{ __('setup.templates_subtitle') }}</p>

        @if($errors->any())
            <div class="alert alert-error">
                <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('setup.store.template') }}" method="POST" class="wizard-form">
            @csrf

            <div class="form-group">
                <label class="form-label">{{ __('setup.template_name') }}</label>
                <input type="text" name="name" class="form-input" value="{{ old('name', $templates->where('trigger_event', 'missed_call')->first()?->name ?? __('setup.default_template_name')) }}" required>
            </div>

            <div class="form-group">
                <label class="form-label">{{ __('setup.template_message') }}</label>
                <textarea name="body" class="form-input form-textarea" required placeholder="{{ __('setup.template_placeholder') }}">{{ old('body', $templates->where('trigger_event', 'missed_call')->first()?->body ?? __('setup.default_template_body')) }}</textarea>
            </div>

            <div style="background: #f0f9ff; border-radius: 12px; padding: 16px;">
                <p style="font-size: 13px; font-weight: 500; color: #0369a1; margin: 0 0 8px 0;">{{ __('setup.available_variables') }}</p>
                <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                    <code style="background: #e0f2fe; color: #0369a1; padding: 4px 10px; border-radius: 6px; font-size: 12px;">@{{clinic_name}}</code>
                    <code style="background: #e0f2fe; color: #0369a1; padding: 4px 10px; border-radius: 6px; font-size: 12px;">@{{booking_link}}</code>
                    <code style="background: #e0f2fe; color: #0369a1; padding: 4px 10px; border-radius: 6px; font-size: 12px;">@{{business_hours}}</code>
                </div>
            </div>

            <div class="wizard-actions">
                <a href="{{ route('setup.provider') }}" class="btn btn-outline">
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

        <div class="skip-link">
            <form action="{{ route('setup.skip.templates') }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit">{{ __('setup.skip_step') }}</button>
            </form>
        </div>
    </div>
</x-setup.layout>
