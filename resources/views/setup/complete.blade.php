<x-setup.layout :step="$step" :totalSteps="$totalSteps">
    <div class="wizard-card" style="text-align: center;">
        <div style="width: 100px; height: 100px; background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px; animation: bounceIn 0.6s ease-out;">
            <svg width="50" height="50" fill="none" stroke="#10b981" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>

        <style>
            @keyframes bounceIn {
                0% { transform: scale(0); opacity: 0; }
                50% { transform: scale(1.1); }
                100% { transform: scale(1); opacity: 1; }
            }
            @keyframes confetti {
                0% { transform: translateY(0) rotate(0deg); opacity: 1; }
                100% { transform: translateY(100px) rotate(720deg); opacity: 0; }
            }
            .confetti {
                position: absolute;
                width: 10px;
                height: 10px;
                border-radius: 2px;
                animation: confetti 1.5s ease-out forwards;
            }
        </style>

        <h1 class="wizard-title">{{ __('setup.complete_title') }}</h1>
        <p class="wizard-subtitle">{{ __('setup.complete_subtitle') }}</p>

        <div style="background: #f0f9ff; border-radius: 16px; padding: 24px; margin: 24px 0; text-align: left;">
            <p style="font-size: 14px; font-weight: 600; color: #0369a1; margin: 0 0 16px 0;">{{ __('setup.whats_next') }}</p>

            <div style="display: flex; flex-direction: column; gap: 12px;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 28px; height: 28px; background: #0ea5e9; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <span style="color: #fff; font-size: 12px; font-weight: 700;">1</span>
                    </div>
                    <p style="font-size: 14px; color: #374151; margin: 0;">{{ __('setup.next_step_1') }}</p>
                </div>

                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 28px; height: 28px; background: #0ea5e9; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <span style="color: #fff; font-size: 12px; font-weight: 700;">2</span>
                    </div>
                    <p style="font-size: 14px; color: #374151; margin: 0;">{{ __('setup.next_step_2') }}</p>
                </div>

                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 28px; height: 28px; background: #0ea5e9; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <span style="color: #fff; font-size: 12px; font-weight: 700;">3</span>
                    </div>
                    <p style="font-size: 14px; color: #374151; margin: 0;">{{ __('setup.next_step_3') }}</p>
                </div>
            </div>
        </div>

        <form action="{{ route('setup.finish') }}" method="POST">
            @csrf
            <div class="wizard-actions" style="justify-content: center;">
                <button type="submit" class="btn btn-primary" style="min-width: 200px;">
                    {{ __('setup.go_to_dashboard') }}
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </button>
            </div>
        </form>
    </div>
</x-setup.layout>
