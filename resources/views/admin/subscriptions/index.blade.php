<x-admin-layout>
    <x-slot name="header">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h2 style="font-weight: 600; font-size: 20px; color: #1e293b; margin: 0;">
                Subscriptions
            </h2>
            <a href="{{ route('admin.subscriptions.transactions') }}" style="display: inline-flex; align-items: center; gap: 6px; background: #f1f5f9; border: 1px solid #e2e8f0; color: #475569; padding: 8px 14px; border-radius: 8px; font-size: 13px; font-weight: 500; text-decoration: none;">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                View Transactions
            </a>
        </div>
    </x-slot>

    <!-- Stats Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px; margin-bottom: 24px;">
        <div style="background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 20px;">
            <div style="font-size: 12px; font-weight: 500; color: #64748b; text-transform: uppercase; margin-bottom: 8px;">Active</div>
            <div style="font-size: 28px; font-weight: 700; color: #059669;">{{ $stats['active'] }}</div>
        </div>
        <div style="background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 20px;">
            <div style="font-size: 12px; font-weight: 500; color: #64748b; text-transform: uppercase; margin-bottom: 8px;">Trialing</div>
            <div style="font-size: 28px; font-weight: 700; color: #2563eb;">{{ $stats['trialing'] }}</div>
        </div>
        <div style="background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 20px;">
            <div style="font-size: 12px; font-weight: 500; color: #64748b; text-transform: uppercase; margin-bottom: 8px;">Canceled</div>
            <div style="font-size: 28px; font-weight: 700; color: #dc2626;">{{ $stats['canceled'] }}</div>
        </div>
        <div style="background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 20px;">
            <div style="font-size: 12px; font-weight: 500; color: #64748b; text-transform: uppercase; margin-bottom: 8px;">MRR</div>
            <div style="font-size: 28px; font-weight: 700; color: #1e293b;">${{ number_format($stats['mrr'], 2) }}</div>
        </div>
    </div>

    <!-- Filters -->
    <div style="background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 16px 20px; margin-bottom: 24px;">
        <form method="GET" action="{{ route('admin.subscriptions.index') }}" style="display: flex; flex-wrap: wrap; gap: 12px; align-items: flex-end;">
            <div>
                <label style="display: block; font-size: 11px; font-weight: 600; color: #64748b; margin-bottom: 4px; text-transform: uppercase;">Status</label>
                <select name="status" style="padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px; background: #fff; color: #1e293b; min-width: 130px;">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="trialing" {{ request('status') == 'trialing' ? 'selected' : '' }}>Trialing</option>
                    <option value="past_due" {{ request('status') == 'past_due' ? 'selected' : '' }}>Past Due</option>
                    <option value="canceled" {{ request('status') == 'canceled' ? 'selected' : '' }}>Canceled</option>
                    <option value="paused" {{ request('status') == 'paused' ? 'selected' : '' }}>Paused</option>
                </select>
            </div>
            <div>
                <label style="display: block; font-size: 11px; font-weight: 600; color: #64748b; margin-bottom: 4px; text-transform: uppercase;">Plan</label>
                <select name="plan" style="padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px; background: #fff; color: #1e293b; min-width: 130px;">
                    <option value="">All Plans</option>
                    @foreach($plans as $plan)
                        <option value="{{ $plan->id }}" {{ request('plan') == $plan->id ? 'selected' : '' }}>{{ $plan->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="display: block; font-size: 11px; font-weight: 600; color: #64748b; margin-bottom: 4px; text-transform: uppercase;">Clinic Name</label>
                <input type="text" name="clinic_name" value="{{ request('clinic_name') }}" placeholder="Search..."
                    style="padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px; background: #fff; color: #1e293b; min-width: 150px;">
            </div>
            <button type="submit" style="background: #f1f5f9; color: #475569; padding: 8px 16px; border-radius: 8px; font-size: 13px; border: 1px solid #e2e8f0; cursor: pointer; font-weight: 500;">
                Filter
            </button>
            @if(request()->hasAny(['status', 'plan', 'clinic_name']))
                <a href="{{ route('admin.subscriptions.index') }}" style="color: #64748b; font-size: 13px; text-decoration: none;">Clear</a>
            @endif
        </form>
    </div>

    <!-- Subscriptions Table -->
    <div style="background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; overflow: hidden;">
        @if($subscriptions->isEmpty())
            <div style="padding: 48px 20px; text-align: center;">
                <div style="width: 64px; height: 64px; background: #f1f5f9; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                    <svg width="32" height="32" fill="none" stroke="#94a3b8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                </div>
                <p style="color: #64748b; font-size: 14px; margin: 0;">No subscriptions found</p>
            </div>
        @else
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f8fafc;">
                            <th style="padding: 12px 16px; text-align: left; font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase;">Clinic</th>
                            <th style="padding: 12px 16px; text-align: left; font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase;">Plan</th>
                            <th style="padding: 12px 16px; text-align: left; font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase;">Status</th>
                            <th style="padding: 12px 16px; text-align: left; font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase;">Interval</th>
                            <th style="padding: 12px 16px; text-align: left; font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase;">Period</th>
                            <th style="padding: 12px 16px; text-align: left; font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase;">Trial End</th>
                            <th style="padding: 12px 16px; text-align: left; font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase;">Created</th>
                            <th style="padding: 12px 16px; text-align: right; font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase;">Stripe</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($subscriptions as $subscription)
                            <tr style="border-top: 1px solid #f1f5f9;">
                                <td style="padding: 14px 16px; color: #1e293b; font-weight: 500; font-size: 14px;">{{ $subscription->clinic?->name ?? 'N/A' }}</td>
                                <td style="padding: 14px 16px; color: #475569; font-size: 14px;">{{ $subscription->plan?->name ?? 'N/A' }}</td>
                                <td style="padding: 14px 16px;">
                                    @php
                                        $statusStyles = [
                                            'active' => 'background: #dcfce7; color: #166534;',
                                            'trialing' => 'background: #dbeafe; color: #1e40af;',
                                            'past_due' => 'background: #fef3c7; color: #92400e;',
                                            'canceled' => 'background: #fef2f2; color: #991b1b;',
                                            'paused' => 'background: #f1f5f9; color: #64748b;',
                                            'incomplete' => 'background: #ffedd5; color: #9a3412;',
                                        ];
                                    @endphp
                                    <span style="{{ $statusStyles[$subscription->status] ?? 'background: #f1f5f9; color: #64748b;' }} font-size: 11px; padding: 3px 8px; border-radius: 9999px; font-weight: 600;">
                                        {{ ucfirst(str_replace('_', ' ', $subscription->status)) }}
                                    </span>
                                </td>
                                <td style="padding: 14px 16px; color: #64748b; font-size: 13px; text-transform: capitalize;">{{ $subscription->interval ?? '-' }}</td>
                                <td style="padding: 14px 16px; color: #64748b; font-size: 13px;">
                                    @if($subscription->current_period_start && $subscription->current_period_end)
                                        {{ $subscription->current_period_start->format('M d') }} - {{ $subscription->current_period_end->format('M d, Y') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td style="padding: 14px 16px; color: #64748b; font-size: 13px;">
                                    @if($subscription->trial_ends_at)
                                        {{ $subscription->trial_ends_at->format('M d, Y') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td style="padding: 14px 16px; color: #64748b; font-size: 13px;">{{ $subscription->created_at->format('M d, Y') }}</td>
                                <td style="padding: 14px 16px; text-align: right;">
                                    @if($subscription->provider_subscription_id)
                                        <a href="https://dashboard.stripe.com/subscriptions/{{ $subscription->provider_subscription_id }}" target="_blank" rel="noopener" style="color: #7c3aed; font-size: 12px; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; font-weight: 500;">
                                            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                            </svg>
                                            Stripe
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($subscriptions->hasPages())
                <div style="padding: 16px 20px; border-top: 1px solid #f1f5f9;">
                    {{ $subscriptions->appends(request()->query())->links() }}
                </div>
            @endif
        @endif
    </div>
</x-admin-layout>
