<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('reports.title') }}
            </h2>
            <form method="GET" style="display: flex; gap: 8px; align-items: center;">
                <select name="period" onchange="this.form.submit()" style="padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; background: #fff;">
                    <option value="7" {{ $period == '7' ? 'selected' : '' }}>{{ __('reports.last_7_days') }}</option>
                    <option value="30" {{ $period == '30' ? 'selected' : '' }}>{{ __('reports.last_30_days') }}</option>
                    <option value="90" {{ $period == '90' ? 'selected' : '' }}>{{ __('reports.last_90_days') }}</option>
                </select>
            </form>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Key Metrics -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 16px; margin-bottom: 24px;">
                <div style="background: #fff; border-radius: 12px; border: 1px solid #e5e7eb; padding: 20px;">
                    <p style="font-size: 13px; color: #6b7280; margin: 0;">{{ __('reports.total_calls') }}</p>
                    <p style="font-size: 28px; font-weight: 700; color: #111827; margin: 4px 0;">{{ $stats['missed_calls'] }}</p>
                    <p style="font-size: 12px; color: #9ca3af; margin: 0;">{{ $stats['leads_created'] }} leads</p>
                </div>

                <div style="background: #fff; border-radius: 12px; border: 1px solid #e5e7eb; padding: 20px;">
                    <p style="font-size: 13px; color: #6b7280; margin: 0;">{{ __('stages.booked') }}</p>
                    <p style="font-size: 28px; font-weight: 700; color: #111827; margin: 4px 0;">{{ $stats['booked'] }}</p>
                    <p style="font-size: 12px; margin: 0; color: #10b981; font-weight: 500;">{{ $stats['conversion_rate'] }}% {{ app()->getLocale() === 'es' ? 'conversión' : 'conversion' }}</p>
                </div>

                <div style="background: #fff; border-radius: 12px; border: 1px solid #e5e7eb; padding: 20px;">
                    <p style="font-size: 13px; color: #6b7280; margin: 0;">{{ app()->getLocale() === 'es' ? 'Tasa de respuesta' : 'Response Rate' }}</p>
                    <p style="font-size: 28px; font-weight: 700; color: #111827; margin: 4px 0;">{{ $stats['response_rate'] }}%</p>
                    <p style="font-size: 12px; color: #9ca3af; margin: 0;">{{ $stats['messages_received'] }}/{{ $stats['messages_sent'] }} {{ app()->getLocale() === 'es' ? 'mensajes' : 'messages' }}</p>
                </div>

                <div style="background: #10b981; border-radius: 12px; padding: 20px;">
                    <p style="font-size: 13px; color: rgba(255,255,255,0.8); margin: 0;">{{ __('reports.total_recovered') }}</p>
                    <p style="font-size: 28px; font-weight: 700; color: #fff; margin: 4px 0;">${{ number_format($stats['revenue'], 0) }}</p>
                    <p style="font-size: 12px; color: rgba(255,255,255,0.8); margin: 0;">{{ app()->getLocale() === 'es' ? 'recuperados este período' : 'recovered this period' }}</p>
                </div>
            </div>

            <div class="reports-grid" style="display: grid; grid-template-columns: 1fr; gap: 24px; margin-bottom: 24px;">
                <!-- Revenue Chart -->
                <div style="background: #fff; border-radius: 12px; border: 1px solid #e5e7eb;">
                    <div style="padding: 16px 20px; border-bottom: 1px solid #f3f4f6;">
                        <h3 style="font-weight: 600; color: #111827; margin: 0;">{{ app()->getLocale() === 'es' ? 'Ingresos a lo largo del tiempo' : 'Revenue Over Time' }}</h3>
                    </div>
                    <div style="padding: 20px;">
                        @if(count($revenueByDay) > 0)
                            <div style="display: flex; align-items: flex-end; gap: 2px; height: 150px;">
                                @foreach($revenueByDay as $day)
                                    <div style="flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: flex-end; height: 100%;" title="{{ $day['date'] }}: ${{ number_format($day['revenue'], 0) }}">
                                        <div style="width: 100%; background: {{ $day['revenue'] > 0 ? '#0ea5e9' : '#f3f4f6' }}; border-radius: 2px 2px 0 0; min-height: 2px; height: {{ $day['percentage'] }}%;"></div>
                                    </div>
                                @endforeach
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-top: 8px;">
                                <span style="font-size: 11px; color: #9ca3af;">{{ $revenueByDay[0]['date'] ?? '' }}</span>
                                <span style="font-size: 11px; color: #9ca3af;">{{ $revenueByDay[count($revenueByDay)-1]['date'] ?? '' }}</span>
                            </div>
                        @else
                            <p style="text-align: center; color: #9ca3af; padding: 40px 0;">{{ app()->getLocale() === 'es' ? 'Sin datos para este período' : 'No data for this period' }}</p>
                        @endif
                    </div>
                </div>

                <!-- Leads Funnel -->
                <div style="background: #fff; border-radius: 12px; border: 1px solid #e5e7eb;">
                    <div style="padding: 16px 20px; border-bottom: 1px solid #f3f4f6;">
                        <h3 style="font-weight: 600; color: #111827; margin: 0;">{{ app()->getLocale() === 'es' ? 'Embudo de leads' : 'Lead Funnel' }}</h3>
                    </div>
                    <div style="padding: 20px;">
                        @php
                            $total = array_sum($leadsByStage);
                            $stages = [
                                ['key' => 'new', 'label' => __('stages.new'), 'color' => '#6b7280'],
                                ['key' => 'contacted', 'label' => __('stages.contacted'), 'color' => '#0ea5e9'],
                                ['key' => 'responded', 'label' => __('stages.responded'), 'color' => '#f59e0b'],
                                ['key' => 'booked', 'label' => __('stages.booked'), 'color' => '#10b981'],
                                ['key' => 'lost', 'label' => __('stages.lost'), 'color' => '#ef4444'],
                            ];
                        @endphp

                        @foreach($stages as $stage)
                            @php $count = $leadsByStage[$stage['key']]; @endphp
                            <div style="margin-bottom: 12px;">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                                    <span style="font-size: 13px; color: #374151;">{{ $stage['label'] }}</span>
                                    <span style="font-size: 13px; font-weight: 500; color: #111827;">{{ $count }}</span>
                                </div>
                                <div style="height: 8px; background: #f3f4f6; border-radius: 4px; overflow: hidden;">
                                    <div style="height: 100%; background: {{ $stage['color'] }}; width: {{ $total > 0 ? ($count / $total * 100) : 0 }}%; border-radius: 4px;"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Outcome Breakdown -->
            <div style="background: #fff; border-radius: 12px; border: 1px solid #e5e7eb;">
                <div style="padding: 16px 20px; border-bottom: 1px solid #f3f4f6;">
                    <h3 style="font-weight: 600; color: #111827; margin: 0;">{{ app()->getLocale() === 'es' ? 'Desglose de resultados' : 'Outcome Breakdown' }}</h3>
                    <p style="font-size: 13px; color: #6b7280; margin: 4px 0 0 0;">{{ $outcomeBreakdown['total'] }} {{ app()->getLocale() === 'es' ? 'resultados totales' : 'total outcomes' }}</p>
                </div>
                <div style="padding: 20px;">
                    @if($outcomeBreakdown['total'] > 0)
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 16px;">
                            @php
                                $outcomes = [
                                    ['key' => 'booked', 'label' => __('inbox.outcome_booked'), 'color' => '#10b981', 'icon' => '✓'],
                                    ['key' => 'callback_requested', 'label' => __('inbox.outcome_callback'), 'color' => '#0ea5e9', 'icon' => '📞'],
                                    ['key' => 'not_interested', 'label' => __('inbox.outcome_not_interested'), 'color' => '#ef4444', 'icon' => '✕'],
                                    ['key' => 'wrong_number', 'label' => __('inbox.outcome_wrong_number'), 'color' => '#6b7280', 'icon' => '🚫'],
                                    ['key' => 'no_response', 'label' => __('inbox.outcome_no_response'), 'color' => '#f59e0b', 'icon' => '💤'],
                                ];
                            @endphp

                            @foreach($outcomes as $outcome)
                                <div style="text-align: center; padding: 16px; background: #f9fafb; border-radius: 8px;">
                                    <div style="font-size: 24px; margin-bottom: 8px;">{{ $outcome['icon'] }}</div>
                                    <p style="font-size: 24px; font-weight: 700; color: {{ $outcome['color'] }}; margin: 0;">
                                        {{ $outcomeBreakdown[$outcome['key']]['count'] }}
                                    </p>
                                    <p style="font-size: 12px; color: #6b7280; margin: 4px 0 0 0;">{{ $outcome['label'] }}</p>
                                    <p style="font-size: 11px; color: #9ca3af; margin: 2px 0 0 0;">
                                        {{ $outcomeBreakdown[$outcome['key']]['percentage'] }}%
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p style="text-align: center; color: #9ca3af; padding: 20px 0;">{{ app()->getLocale() === 'es' ? 'Sin resultados registrados aún' : 'No outcomes recorded yet' }}</p>
                    @endif
                </div>
            </div>

        </div>
    </div>

    <style>
        @media (min-width: 768px) {
            .reports-grid {
                grid-template-columns: 2fr 1fr !important;
            }
        }
    </style>
</x-app-layout>
