<x-admin-layout>
    <x-slot name="header">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h2 style="font-weight: 600; font-size: 20px; color: #1e293b; margin: 0;">
                Transactions
            </h2>
            <a href="{{ route('admin.subscriptions.index') }}" style="display: inline-flex; align-items: center; gap: 6px; color: #64748b; font-size: 13px; text-decoration: none; font-weight: 500;">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/>
                </svg>
                Back to Subscriptions
            </a>
        </div>
    </x-slot>

    <!-- Stats Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px; margin-bottom: 24px;">
        <div style="background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 20px;">
            <div style="font-size: 12px; font-weight: 500; color: #64748b; text-transform: uppercase; margin-bottom: 8px;">Total Revenue</div>
            <div style="font-size: 28px; font-weight: 700; color: #059669;">${{ number_format($stats['total_revenue'], 2) }}</div>
        </div>
        <div style="background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 20px;">
            <div style="font-size: 12px; font-weight: 500; color: #64748b; text-transform: uppercase; margin-bottom: 8px;">Successful</div>
            <div style="font-size: 28px; font-weight: 700; color: #1e293b;">{{ $stats['count'] }}</div>
        </div>
        <div style="background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 20px;">
            <div style="font-size: 12px; font-weight: 500; color: #64748b; text-transform: uppercase; margin-bottom: 8px;">Failed</div>
            <div style="font-size: 28px; font-weight: 700; color: #dc2626;">{{ $stats['failed'] }}</div>
        </div>
    </div>

    <!-- Filters -->
    <div style="background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 16px 20px; margin-bottom: 24px;">
        <form method="GET" action="{{ route('admin.subscriptions.transactions') }}" style="display: flex; flex-wrap: wrap; gap: 12px; align-items: flex-end;">
            <div>
                <label style="display: block; font-size: 11px; font-weight: 600; color: #64748b; margin-bottom: 4px; text-transform: uppercase;">Status</label>
                <select name="status" style="padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px; background: #fff; color: #1e293b; min-width: 130px;">
                    <option value="">All Statuses</option>
                    <option value="succeeded" {{ request('status') == 'succeeded' ? 'selected' : '' }}>Succeeded</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                </select>
            </div>
            <div>
                <label style="display: block; font-size: 11px; font-weight: 600; color: #64748b; margin-bottom: 4px; text-transform: uppercase;">Type</label>
                <select name="type" style="padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px; background: #fff; color: #1e293b; min-width: 130px;">
                    <option value="">All Types</option>
                    <option value="charge" {{ request('type') == 'charge' ? 'selected' : '' }}>Charge</option>
                    <option value="refund" {{ request('type') == 'refund' ? 'selected' : '' }}>Refund</option>
                    <option value="invoice" {{ request('type') == 'invoice' ? 'selected' : '' }}>Invoice</option>
                </select>
            </div>
            <div>
                <label style="display: block; font-size: 11px; font-weight: 600; color: #64748b; margin-bottom: 4px; text-transform: uppercase;">From</label>
                <input type="date" name="from" value="{{ request('from') }}"
                    style="padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px; background: #fff; color: #1e293b;">
            </div>
            <div>
                <label style="display: block; font-size: 11px; font-weight: 600; color: #64748b; margin-bottom: 4px; text-transform: uppercase;">To</label>
                <input type="date" name="to" value="{{ request('to') }}"
                    style="padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px; background: #fff; color: #1e293b;">
            </div>
            <button type="submit" style="background: #f1f5f9; color: #475569; padding: 8px 16px; border-radius: 8px; font-size: 13px; border: 1px solid #e2e8f0; cursor: pointer; font-weight: 500;">
                Filter
            </button>
            @if(request()->hasAny(['status', 'type', 'from', 'to']))
                <a href="{{ route('admin.subscriptions.transactions') }}" style="color: #64748b; font-size: 13px; text-decoration: none;">Clear</a>
            @endif
        </form>
    </div>

    <!-- Transactions Table -->
    <div style="background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; overflow: hidden;">
        @if($transactions->isEmpty())
            <div style="padding: 48px 20px; text-align: center;">
                <div style="width: 64px; height: 64px; background: #f1f5f9; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                    <svg width="32" height="32" fill="none" stroke="#94a3b8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <p style="color: #64748b; font-size: 14px; margin: 0;">No transactions found</p>
            </div>
        @else
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f8fafc;">
                            <th style="padding: 12px 16px; text-align: left; font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase;">Date</th>
                            <th style="padding: 12px 16px; text-align: left; font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase;">Clinic</th>
                            <th style="padding: 12px 16px; text-align: left; font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase;">Plan</th>
                            <th style="padding: 12px 16px; text-align: left; font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase;">Type</th>
                            <th style="padding: 12px 16px; text-align: left; font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase;">Status</th>
                            <th style="padding: 12px 16px; text-align: right; font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase;">Amount</th>
                            <th style="padding: 12px 16px; text-align: left; font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase;">Description</th>
                            <th style="padding: 12px 16px; text-align: right; font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase;">Stripe</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transactions as $transaction)
                            <tr style="border-top: 1px solid #f1f5f9;">
                                <td style="padding: 14px 16px; color: #64748b; font-size: 13px;">{{ $transaction->created_at->format('M d, Y H:i') }}</td>
                                <td style="padding: 14px 16px; color: #1e293b; font-weight: 500; font-size: 14px;">{{ $transaction->clinic?->name ?? 'N/A' }}</td>
                                <td style="padding: 14px 16px; color: #64748b; font-size: 13px;">{{ $transaction->subscription?->plan?->name ?? '-' }}</td>
                                <td style="padding: 14px 16px;">
                                    @php
                                        $typeStyles = [
                                            'charge' => 'background: #dbeafe; color: #1e40af;',
                                            'refund' => 'background: #ffedd5; color: #9a3412;',
                                            'invoice' => 'background: #f1f5f9; color: #475569;',
                                        ];
                                    @endphp
                                    <span style="{{ $typeStyles[$transaction->type] ?? 'background: #f1f5f9; color: #64748b;' }} font-size: 11px; padding: 3px 8px; border-radius: 9999px; font-weight: 600; text-transform: capitalize;">
                                        {{ $transaction->type }}
                                    </span>
                                </td>
                                <td style="padding: 14px 16px;">
                                    @php
                                        $txStatusStyles = [
                                            'succeeded' => 'background: #dcfce7; color: #166534;',
                                            'pending' => 'background: #fef3c7; color: #92400e;',
                                            'failed' => 'background: #fef2f2; color: #991b1b;',
                                        ];
                                    @endphp
                                    <span style="{{ $txStatusStyles[$transaction->status] ?? 'background: #f1f5f9; color: #64748b;' }} font-size: 11px; padding: 3px 8px; border-radius: 9999px; font-weight: 600; text-transform: capitalize;">
                                        {{ $transaction->status }}
                                    </span>
                                </td>
                                <td style="padding: 14px 16px; text-align: right; font-weight: 600; color: #1e293b; font-size: 14px;">
                                    {{ $transaction->formatted_amount }}
                                </td>
                                <td style="padding: 14px 16px; color: #64748b; font-size: 13px; max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    {{ $transaction->description ?? '-' }}
                                </td>
                                <td style="padding: 14px 16px; text-align: right;">
                                    @if($transaction->provider_transaction_id)
                                        <a href="https://dashboard.stripe.com/payments/{{ $transaction->provider_transaction_id }}" target="_blank" rel="noopener" style="color: #7c3aed; font-size: 12px; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; font-weight: 500;">
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

            @if($transactions->hasPages())
                <div style="padding: 16px 20px; border-top: 1px solid #f1f5f9;">
                    {{ $transactions->appends(request()->query())->links() }}
                </div>
            @endif
        @endif
    </div>
</x-admin-layout>
