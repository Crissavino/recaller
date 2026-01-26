<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('subscription.invoices_title') }}
            </h2>
            <a href="{{ route('subscription.index') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                {{ __('subscription.back_to_subscription') }}
            </a>
        </div>
    </x-slot>

    <style>
        .invoices-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 32px 16px;
        }
        .invoices-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .invoices-header {
            padding: 20px 24px;
            border-bottom: 1px solid #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .invoices-header h3 {
            font-size: 16px;
            font-weight: 600;
            color: #111827;
            margin: 0;
        }
        .invoices-count {
            font-size: 13px;
            color: #6b7280;
            background: #f3f4f6;
            padding: 4px 12px;
            border-radius: 9999px;
        }
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
        }
        .invoice-table th {
            text-align: left;
            padding: 12px 24px;
            font-size: 12px;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            background: #f9fafb;
            border-bottom: 1px solid #e5e7eb;
        }
        .invoice-table td {
            padding: 16px 24px;
            font-size: 14px;
            color: #374151;
            border-bottom: 1px solid #f3f4f6;
        }
        .invoice-table tr:last-child td {
            border-bottom: none;
        }
        .invoice-table tr:hover {
            background: #f9fafb;
        }
        .invoice-number {
            font-weight: 600;
            color: #111827;
        }
        .invoice-date {
            color: #6b7280;
        }
        .invoice-amount {
            font-weight: 600;
            color: #111827;
        }
        .invoice-status {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 500;
        }
        .invoice-status.paid {
            background: #d1fae5;
            color: #065f46;
        }
        .invoice-status.open {
            background: #fef3c7;
            color: #92400e;
        }
        .invoice-status.draft {
            background: #f3f4f6;
            color: #6b7280;
        }
        .invoice-status.void {
            background: #fee2e2;
            color: #991b1b;
        }
        .invoice-actions {
            display: flex;
            gap: 8px;
            justify-content: flex-end;
        }
        .invoice-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s;
        }
        .invoice-btn-primary {
            background: #0ea5e9;
            color: #fff;
        }
        .invoice-btn-primary:hover {
            background: #0284c7;
        }
        .invoice-btn-secondary {
            background: #f3f4f6;
            color: #374151;
        }
        .invoice-btn-secondary:hover {
            background: #e5e7eb;
        }
        .invoice-btn svg {
            width: 16px;
            height: 16px;
        }
        .empty-state {
            text-align: center;
            padding: 60px 24px;
        }
        .empty-state-icon {
            width: 64px;
            height: 64px;
            background: #f3f4f6;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
        }
        .empty-state-icon svg {
            width: 32px;
            height: 32px;
            color: #9ca3af;
        }
        .empty-state h4 {
            font-size: 16px;
            font-weight: 600;
            color: #111827;
            margin: 0 0 8px 0;
        }
        .empty-state p {
            font-size: 14px;
            color: #6b7280;
            margin: 0;
        }
        @media (max-width: 768px) {
            .invoice-table th:nth-child(3),
            .invoice-table td:nth-child(3) {
                display: none;
            }
            .invoice-table th,
            .invoice-table td {
                padding: 12px 16px;
            }
        }
    </style>

    <div class="invoices-container">
        <div class="invoices-card">
            <div class="invoices-header">
                <h3>{{ __('subscription.billing_history') }}</h3>
                <span class="invoices-count">{{ count($invoices) }} {{ __('subscription.invoices_count') }}</span>
            </div>

            @if(count($invoices) > 0)
                <table class="invoice-table">
                    <thead>
                        <tr>
                            <th>{{ __('subscription.invoice') }}</th>
                            <th>{{ __('subscription.date') }}</th>
                            <th>{{ __('subscription.description') }}</th>
                            <th>{{ __('subscription.amount') }}</th>
                            <th>{{ __('subscription.status') }}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoices as $invoice)
                            <tr>
                                <td>
                                    <span class="invoice-number">{{ $invoice['number'] ?? '-' }}</span>
                                </td>
                                <td>
                                    <span class="invoice-date">{{ $invoice['date']->format('M d, Y') }}</span>
                                </td>
                                <td>
                                    {{ $invoice['plan_name'] }}
                                </td>
                                <td>
                                    <span class="invoice-amount">${{ number_format($invoice['amount'], 2) }} {{ $invoice['currency'] }}</span>
                                </td>
                                <td>
                                    @php
                                        $statusClass = match($invoice['status']) {
                                            'paid' => 'paid',
                                            'open' => 'open',
                                            'draft' => 'draft',
                                            'void', 'uncollectible' => 'void',
                                            default => 'draft'
                                        };
                                        $statusLabel = match($invoice['status']) {
                                            'paid' => __('subscription.status_paid'),
                                            'open' => __('subscription.status_open'),
                                            'draft' => __('subscription.status_draft'),
                                            'void' => __('subscription.status_void'),
                                            default => ucfirst($invoice['status'])
                                        };
                                    @endphp
                                    <span class="invoice-status {{ $statusClass }}">
                                        @if($invoice['status'] === 'paid')
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 14px; height: 14px;">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        @endif
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                                <td>
                                    <div class="invoice-actions">
                                        @if($invoice['hosted_url'])
                                            <a href="{{ $invoice['hosted_url'] }}" target="_blank" class="invoice-btn invoice-btn-secondary">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                                {{ __('subscription.view') }}
                                            </a>
                                        @endif
                                        @if($invoice['pdf_url'])
                                            <a href="{{ $invoice['pdf_url'] }}" target="_blank" class="invoice-btn invoice-btn-primary">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                                PDF
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h4>{{ __('subscription.no_invoices') }}</h4>
                    <p>{{ __('subscription.no_invoices_description') }}</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
