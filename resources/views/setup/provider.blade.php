<x-setup.layout :step="$step" :totalSteps="$totalSteps">
    <div class="wizard-card">
        <div class="wizard-icon" style="background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
            </svg>
        </div>

        <h1 class="wizard-title">{{ __('setup.phone_title') }}</h1>
        <p class="wizard-subtitle">{{ __('setup.phone_subtitle') }}</p>

        {{-- Clinic phone display --}}
        <div style="background: #f9fafb; border-radius: 12px; padding: 20px; margin-bottom: 24px;">
            <p style="font-size: 13px; color: #6b7280; margin: 0 0 4px 0;">{{ __('setup.clinic_phone') }}</p>
            <p style="font-size: 20px; font-weight: 600; color: #111827; margin: 0;">{{ $clinic->phone }}</p>
        </div>

        {{-- How it works --}}
        <div style="background: #f0f9ff; border-radius: 12px; padding: 20px; margin-bottom: 24px;">
            <p style="font-weight: 600; color: #0369a1; margin: 0 0 12px 0;">{{ __('setup.phone_how_it_works') }}</p>
            <ol style="margin: 0; padding-left: 20px; color: #374151; font-size: 14px; line-height: 1.8;">
                <li>{{ __('setup.phone_flow_1') }}</li>
                <li>{{ __('setup.phone_flow_2') }}</li>
                <li>{{ __('setup.phone_flow_3') }}</li>
                <li>{{ __('setup.phone_flow_4') }}</li>
            </ol>
        </div>

        @if($recallerNumber)
            {{-- Recaller number assigned --}}
            <div style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border: 2px solid #86efac; border-radius: 16px; padding: 24px; margin-bottom: 24px; text-align: center;">
                <div style="width: 64px; height: 64px; background: #22c55e; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                    <svg width="32" height="32" fill="white" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <p style="font-size: 14px; color: #166534; margin: 0 0 8px 0; font-weight: 500;">{{ __('setup.your_recaller_number') }}</p>
                <p style="font-size: 28px; font-weight: 700; color: #15803d; margin: 0; letter-spacing: 1px;">{{ $recallerNumber->phone_number }}</p>
            </div>

            <div style="background: #f0f9ff; border-radius: 12px; padding: 20px; margin-bottom: 24px;">
                <p style="font-weight: 600; color: #0369a1; margin: 0 0 12px 0;">{{ __('setup.phone_instructions_title') }}</p>
                <ol style="margin: 0; padding-left: 20px; color: #374151; font-size: 14px; line-height: 1.8;">
                    <li>{{ __('setup.phone_instruction_1') }}</li>
                    <li>{{ __('setup.phone_instruction_2', ['number' => $recallerNumber->phone_number]) }}</li>
                    <li>{{ __('setup.phone_instruction_3') }}</li>
                </ol>
            </div>
        @else
            {{-- No Recaller number yet --}}
            <div style="background: linear-gradient(135deg, #fefce8 0%, #fef9c3 100%); border: 2px solid #fde047; border-radius: 16px; padding: 24px; margin-bottom: 24px; text-align: center;">
                <div style="width: 64px; height: 64px; background: #eab308; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                    <svg width="32" height="32" fill="white" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <p style="font-size: 18px; font-weight: 600; color: #854d0e; margin: 0 0 8px 0;">{{ __('setup.phone_pending_title') }}</p>
                <p style="font-size: 14px; color: #a16207; margin: 0;">{{ __('setup.phone_pending_desc') }}</p>
            </div>
        @endif

        <div class="wizard-actions">
            <a href="{{ route('setup.clinic') }}" class="btn btn-outline">
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
    </div>
</x-setup.layout>
