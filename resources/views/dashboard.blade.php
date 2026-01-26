<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('dashboard.title') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">{{ $clinic?->name ?? 'No clinic' }} &middot; {{ __('dashboard.today') }}, {{ now()->format('M d, Y') }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Configuration Warning -->
            @if(!$configStatus['is_complete'])
                <div style="background: #fef3c7; border: 1px solid #fcd34d; border-radius: 12px; padding: 16px 20px; margin-bottom: 24px;">
                    <div style="display: flex; align-items: flex-start; gap: 12px;">
                        <div style="width: 40px; height: 40px; background: #f59e0b; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg style="width: 20px; height: 20px; color: #fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div style="flex: 1;">
                            <h3 style="font-weight: 600; color: #92400e; margin: 0 0 8px 0;">{{ __('dashboard.setup_title') }}</h3>
                            <p style="font-size: 14px; color: #a16207; margin: 0 0 12px 0;">{{ __('dashboard.setup_text') }}</p>
                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                @foreach($configStatus['issues'] as $issue)
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <span style="color: #b45309;">
                                            @if($issue['type'] === 'phone')
                                                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                                </svg>
                                            @else
                                                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                                                </svg>
                                            @endif
                                        </span>
                                        <span style="font-size: 14px; color: #92400e;">
                                            @if($issue['type'] === 'phone')
                                                {{ __('dashboard.setup_phone') }}
                                            @else
                                                {{ __('dashboard.setup_template') }}
                                            @endif
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                            <a href="{{ route('settings.index') }}" style="display: inline-flex; align-items: center; gap: 6px; margin-top: 12px; font-size: 14px; font-weight: 600; color: #b45309; text-decoration: none;">
                                {{ __('dashboard.go_to_settings') }}
                                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Stats Cards Row -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 12px; margin-bottom: 24px;">
                <!-- At Risk -->
                @if($salesMetrics['leads_at_risk'] > 0)
                <div style="background: #f59e0b; border-radius: 12px; padding: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                    <p style="font-size: 12px; color: rgba(255,255,255,0.85); margin: 0;">{{ __('dashboard.at_risk') }}</p>
                    <p style="font-size: 20px; font-weight: 700; color: #fff; margin: 4px 0;">${{ number_format($salesMetrics['money_at_risk'], 0) }}</p>
                    <p style="font-size: 12px; color: rgba(255,255,255,0.85); margin: 0;">{{ $salesMetrics['leads_at_risk'] }} {{ __('dashboard.leads') }}</p>
                </div>
                @endif

                <!-- Total Recovered -->
                <div style="background: #10b981; border-radius: 12px; padding: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                    <p style="font-size: 12px; color: rgba(255,255,255,0.85); margin: 0;">{{ __('dashboard.recovered') }}</p>
                    <p style="font-size: 20px; font-weight: 700; color: #fff; margin: 4px 0;">${{ number_format($salesMetrics['total_recovered'], 0) }}</p>
                    <p style="font-size: 12px; color: rgba(255,255,255,0.85); margin: 0;">{{ __('dashboard.all_time') }}</p>
                </div>

                <!-- This Month -->
                <div style="background: #fff; border-radius: 12px; padding: 16px; border: 1px solid #e5e7eb; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                    <p style="font-size: 12px; color: #6b7280; margin: 0;">{{ __('dashboard.this_month') }}</p>
                    <p style="font-size: 20px; font-weight: 700; color: #111827; margin: 4px 0;">${{ number_format($salesMetrics['this_month_revenue'], 0) }}</p>
                    @if($salesMetrics['monthly_growth'] > 0)
                        <p style="font-size: 12px; color: #10b981; font-weight: 500; margin: 0;">+{{ $salesMetrics['monthly_growth'] }}%</p>
                    @elseif($salesMetrics['monthly_growth'] < 0)
                        <p style="font-size: 12px; color: #ef4444; font-weight: 500; margin: 0;">{{ $salesMetrics['monthly_growth'] }}%</p>
                    @else
                        <p style="font-size: 12px; color: #9ca3af; margin: 0;">{{ __('dashboard.vs_last_month') }}</p>
                    @endif
                </div>

                <!-- Conversion -->
                <div style="background: #fff; border-radius: 12px; padding: 16px; border: 1px solid #e5e7eb; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                    <p style="font-size: 12px; color: #6b7280; margin: 0;">{{ __('dashboard.conversion') }}</p>
                    <p style="font-size: 20px; font-weight: 700; color: #111827; margin: 4px 0;">{{ $salesMetrics['conversion_rate'] }}%</p>
                    <p style="font-size: 12px; color: #9ca3af; margin: 0;">{{ __('dashboard.calls_to_bookings') }}</p>
                </div>

                <!-- Response Time -->
                <div style="background: #fff; border-radius: 12px; padding: 16px; border: 1px solid #e5e7eb; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                    <p style="font-size: 12px; color: #6b7280; margin: 0;">{{ __('dashboard.avg_response') }}</p>
                    <p style="font-size: 20px; font-weight: 700; margin: 4px 0; color: {{ $salesMetrics['avg_response_minutes'] <= 15 ? '#10b981' : ($salesMetrics['avg_response_minutes'] <= 60 ? '#f59e0b' : '#ef4444') }};">
                        @if($salesMetrics['avg_response_minutes'] < 60)
                            {{ $salesMetrics['avg_response_minutes'] }}m
                        @else
                            {{ round($salesMetrics['avg_response_minutes'] / 60, 1) }}h
                        @endif
                    </p>
                    <p style="font-size: 12px; color: #9ca3af; margin: 0;">{{ __('dashboard.reply_time') }}</p>
                </div>

                <!-- Today -->
                <div style="background: #fff; border-radius: 12px; padding: 16px; border: 1px solid #e5e7eb; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                    <p style="font-size: 12px; color: #6b7280; margin: 0;">{{ __('dashboard.today') }}</p>
                    <p style="font-size: 20px; font-weight: 700; color: #111827; margin: 4px 0;">${{ number_format($stats['recovered_revenue'], 0) }}</p>
                    <p style="font-size: 12px; color: #9ca3af; margin: 0;">{{ $stats['booked'] }} {{ __('dashboard.booked') }}</p>
                </div>
            </div>

            <!-- Chart Section -->
            <div style="background: #fff; border-radius: 12px; border: 1px solid #e5e7eb; margin-bottom: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                <div style="padding: 16px 20px; border-bottom: 1px solid #f3f4f6;">
                    <h3 style="font-weight: 600; color: #111827; margin: 0;">{{ __('dashboard.weekly_revenue') }}</h3>
                </div>
                <div style="padding: 20px;">
                    <div style="display: flex; align-items: flex-end; justify-content: space-between; gap: 12px; height: 120px;">
                        @foreach($weeklyRevenue as $day)
                            <div style="flex: 1; display: flex; flex-direction: column; align-items: center;">
                                <div style="width: 100%; height: 100px; background: #f3f4f6; border-radius: 6px 6px 0 0; display: flex; flex-direction: column; justify-content: flex-end;">
                                    @if($day['percentage'] > 0)
                                    <div style="width: 100%; height: {{ $day['percentage'] }}%; min-height: 4px; background: #0ea5e9; border-radius: 6px 6px 0 0;"></div>
                                    @endif
                                </div>
                                <span style="font-size: 12px; color: #6b7280; margin-top: 8px;">{{ $day['label'] }}</span>
                            </div>
                        @endforeach
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-top: 16px; padding-top: 16px; border-top: 1px solid #f3f4f6;">
                        <div>
                            <p style="font-size: 12px; color: #6b7280; margin: 0;">{{ __('dashboard.this_week') }}</p>
                            <p style="font-size: 18px; font-weight: 700; color: #111827; margin: 4px 0 0 0;">${{ number_format($weeklyTotal, 0) }}</p>
                        </div>
                        <div style="text-align: right;">
                            <p style="font-size: 12px; color: #6b7280; margin: 0;">{{ __('dashboard.best_day') }}</p>
                            <p style="font-size: 18px; font-weight: 700; color: #0ea5e9; margin: 4px 0 0 0;">{{ $bestDay }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Needs Attention -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                    <div class="px-5 py-4 border-b border-gray-100">
                        <div class="flex items-center justify-between">
                            <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                                <span class="w-2 h-2 bg-yellow-400 rounded-full animate-pulse"></span>
                                {{ __('dashboard.needs_attention') }}
                            </h3>
                            <span class="text-xs text-gray-500">{{ __('dashboard.waiting_reply') }}</span>
                        </div>
                    </div>
                    <div class="divide-y divide-gray-50">
                        @forelse($needsAttention as $conversation)
                            <a href="{{ route('conversations.show', $conversation->id) }}" class="block px-5 py-3 hover:bg-gray-50 transition-colors">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 bg-sky-100 rounded-full flex items-center justify-center">
                                            <span class="text-xs font-medium text-sky-600">
                                                {{ substr($conversation->lead->caller->phone, -2) }}
                                            </span>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $conversation->lead->caller->phone }}</p>
                                            <p class="text-xs text-gray-500">{{ $conversation->last_message_at?->diffForHumans() ?? __('dashboard.no_messages') }}</p>
                                        </div>
                                    </div>
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </div>
                            </a>
                        @empty
                            <div class="px-5 py-8 text-center">
                                <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <p class="text-sm text-gray-500">{{ __('dashboard.all_caught_up') }}</p>
                            </div>
                        @endforelse
                    </div>
                    @if($needsAttention->isNotEmpty())
                        <div class="px-5 py-3 bg-gray-50 border-t border-gray-100">
                            <a href="{{ route('inbox.index') }}" class="text-sm text-sky-600 hover:text-sky-700 font-medium">
                                {{ __('dashboard.view_all') }} &rarr;
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Recent Activity -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                    <div class="px-5 py-4 border-b border-gray-100">
                        <h3 class="font-semibold text-gray-900">{{ __('dashboard.recent_leads') }}</h3>
                    </div>
                    <div class="divide-y divide-gray-50">
                        @forelse($recentLeads as $lead)
                            <div class="px-5 py-3">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $lead->caller->phone }}</p>
                                            <p class="text-xs text-gray-500">{{ $lead->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                        @if($lead->stage->value === 'new') bg-gray-100 text-gray-700
                                        @elseif($lead->stage->value === 'contacted') bg-sky-100 text-sky-700
                                        @elseif($lead->stage->value === 'responded') bg-yellow-100 text-yellow-700
                                        @elseif($lead->stage->value === 'booked') bg-emerald-100 text-emerald-700
                                        @else bg-red-100 text-red-700
                                        @endif">
                                        {{ __('stages.' . $lead->stage->value) }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="px-5 py-8 text-center">
                                <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                    </svg>
                                </div>
                                <p class="text-sm text-gray-500">{{ __('dashboard.no_leads') }}</p>
                                <p class="text-xs text-gray-400 mt-1">{{ __('dashboard.missed_calls_appear') }}</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
