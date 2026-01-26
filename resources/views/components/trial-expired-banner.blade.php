@php
    $clinic = auth()->user()?->clinics()->first();
    $showBanner = $clinic && $clinic->hasExpiredTrial();
@endphp

@if($showBanner)
<div class="bg-gradient-to-r from-amber-500 to-orange-500 text-white" x-data="{ show: true }" x-show="show">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between py-3">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm font-medium">
                    {{ __('subscription.trial_expired_message') }}
                </p>
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ route('pricing') }}" class="inline-flex items-center gap-2 bg-white text-orange-600 px-4 py-1.5 rounded-full text-sm font-semibold hover:bg-orange-50 transition-colors">
                    {{ __('subscription.subscribe_now') }}
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </a>
                <button @click="show = false" class="text-white/80 hover:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>
@endif
