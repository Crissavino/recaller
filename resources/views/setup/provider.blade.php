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

            {{-- Forwarding guide --}}
            <div style="margin-bottom: 24px;">
                <h3 style="font-size: 16px; font-weight: 600; color: #111827; margin: 0 0 4px 0; text-align: center;">{{ __('setup.forwarding_title') }}</h3>
                <p style="font-size: 13px; color: #6b7280; margin: 0 0 16px 0; text-align: center;">{{ __('setup.forwarding_subtitle') }}</p>

                {{-- Cards grid --}}
                <div style="display: grid; grid-template-columns: 1fr; gap: 12px;">

                    {{-- Card 1: Mobile --}}
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px; position: relative;">
                        <div style="position: absolute; top: -8px; left: 16px; background: #10b981; color: white; font-size: 11px; font-weight: 600; padding: 2px 10px; border-radius: 10px;">{{ __('setup.forwarding_recommended') }}</div>
                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 12px; margin-top: 4px;">
                            <div style="width: 36px; height: 36px; background: #dbeafe; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <svg width="20" height="20" fill="none" stroke="#3b82f6" viewBox="0 0 24 24" stroke-width="2">
                                    <rect x="5" y="2" width="14" height="20" rx="2" ry="2"/><line x1="12" y1="18" x2="12.01" y2="18"/>
                                </svg>
                            </div>
                            <div>
                                <p style="font-weight: 600; color: #111827; margin: 0; font-size: 14px;">{{ __('setup.forwarding_mobile_title') }}</p>
                                <p style="font-size: 12px; color: #6b7280; margin: 0;">Vodafone, Orange, Digi</p>
                            </div>
                        </div>
                        <ol style="margin: 0; padding-left: 20px; color: #374151; font-size: 13px; line-height: 1.9;">
                            <li>{{ __('setup.forwarding_mobile_step_1') }}</li>
                            <li>{{ __('setup.forwarding_mobile_step_2') }}</li>
                            <li>{{ __('setup.forwarding_mobile_step_3') }}</li>
                            <li>{{ __('setup.forwarding_mobile_step_4') }}</li>
                        </ol>
                        <div style="margin-top: 10px; background: #eff6ff; border-radius: 8px; padding: 10px 12px;">
                            <p style="margin: 0; font-size: 12px; color: #1e40af;">
                                <strong>{{ __('setup.forwarding_mobile_quick_code') }}</strong>
                                <code style="background: #dbeafe; padding: 2px 6px; border-radius: 4px; font-size: 12px; letter-spacing: 0.5px;">**004*{{ str_replace(' ', '', $recallerNumber->phone_number) }}#</code>
                            </p>
                        </div>
                    </div>

                    {{-- Card 2: Landline --}}
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px;">
                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 12px;">
                            <div style="width: 36px; height: 36px; background: #dbeafe; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <svg width="20" height="20" fill="none" stroke="#3b82f6" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                            </div>
                            <div>
                                <p style="font-weight: 600; color: #111827; margin: 0; font-size: 14px;">{{ __('setup.forwarding_landline_title') }}</p>
                                <p style="font-size: 12px; color: #6b7280; margin: 0;">RCS&RDS, Telekom, PBX</p>
                            </div>
                        </div>
                        <ol style="margin: 0; padding-left: 20px; color: #374151; font-size: 13px; line-height: 1.9;">
                            <li>{{ __('setup.forwarding_landline_step_1') }}</li>
                            <li>{{ __('setup.forwarding_landline_step_2') }}</li>
                            <li>{{ __('setup.forwarding_landline_step_3') }}</li>
                            <li>{{ __('setup.forwarding_landline_step_4') }}</li>
                        </ol>
                        <div style="margin-top: 10px; background: #f5f5f4; border-radius: 8px; padding: 10px 12px;">
                            <p style="margin: 0; font-size: 12px; color: #57534e;">{{ __('setup.forwarding_landline_note') }}</p>
                        </div>
                    </div>

                    {{-- Card 3: VoIP --}}
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px;">
                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 12px;">
                            <div style="width: 36px; height: 36px; background: #dbeafe; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <svg width="20" height="20" fill="none" stroke="#3b82f6" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/>
                                </svg>
                            </div>
                            <div>
                                <p style="font-weight: 600; color: #111827; margin: 0; font-size: 14px;">{{ __('setup.forwarding_voip_title') }}</p>
                                <p style="font-size: 12px; color: #6b7280; margin: 0;">RingCentral, 3CX, Ooma</p>
                            </div>
                        </div>
                        <ol style="margin: 0; padding-left: 20px; color: #374151; font-size: 13px; line-height: 1.9;">
                            <li>{{ __('setup.forwarding_voip_step_1') }}</li>
                            <li>{{ __('setup.forwarding_voip_step_2') }}</li>
                            <li>{{ __('setup.forwarding_voip_step_3') }}</li>
                            <li>{{ __('setup.forwarding_voip_step_4') }}</li>
                        </ol>
                    </div>
                </div>

                {{-- Tip --}}
                <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px; padding: 14px 16px; margin-top: 14px; display: flex; gap: 10px; align-items: flex-start;">
                    <svg width="20" height="20" fill="#16a34a" viewBox="0 0 20 20" style="flex-shrink: 0; margin-top: 1px;">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <p style="margin: 0; font-size: 13px; color: #166534; line-height: 1.5;">
                        {!! __('setup.forwarding_tip') !!}
                    </p>
                </div>
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
