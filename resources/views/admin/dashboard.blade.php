<x-admin-layout>
    <x-slot name="header">
        <h2 style="font-weight: 600; font-size: 20px; color: #1e293b; margin: 0;">
            Admin Dashboard
        </h2>
    </x-slot>

    <!-- Stats Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 32px;">
        <div style="background: #fff; border-radius: 12px; padding: 24px; border: 1px solid #e2e8f0;">
            <p style="font-size: 13px; color: #64748b; margin: 0 0 8px 0; font-weight: 500;">Total Clinics</p>
            <p style="font-size: 32px; font-weight: 700; color: #1e293b; margin: 0;">{{ $stats['total_clinics'] }}</p>
        </div>
        <div style="background: #fff; border-radius: 12px; padding: 24px; border: 1px solid #e2e8f0;">
            <p style="font-size: 13px; color: #64748b; margin: 0 0 8px 0; font-weight: 500;">Total Users</p>
            <p style="font-size: 32px; font-weight: 700; color: #1e293b; margin: 0;">{{ $stats['total_users'] }}</p>
        </div>
        <div style="background: #fff; border-radius: 12px; padding: 24px; border: 1px solid #e2e8f0;">
            <p style="font-size: 13px; color: #64748b; margin: 0 0 8px 0; font-weight: 500;">Phone Numbers</p>
            <p style="font-size: 32px; font-weight: 700; color: #1e293b; margin: 0;">
                {{ $stats['active_phone_numbers'] }}
                <span style="font-size: 14px; color: #94a3b8;">/ {{ $stats['total_phone_numbers'] }}</span>
            </p>
        </div>
        <div style="background: #fff; border-radius: 12px; padding: 24px; border: 1px solid #e2e8f0;">
            <p style="font-size: 13px; color: #64748b; margin: 0 0 8px 0; font-weight: 500;">Total Leads</p>
            <p style="font-size: 32px; font-weight: 700; color: #1e293b; margin: 0;">
                {{ $stats['total_leads'] }}
                <span style="font-size: 14px; color: #059669; font-weight: 500;">+{{ $stats['leads_today'] }} today</span>
            </p>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
        <!-- Recent Clinics -->
        <div style="background: #fff; border-radius: 12px; border: 1px solid #e2e8f0;">
            <div style="padding: 16px 20px; border-bottom: 1px solid #f1f5f9;">
                <h3 style="font-weight: 600; color: #1e293b; margin: 0; font-size: 15px;">Recent Clinics</h3>
            </div>
            <div>
                @forelse($recentClinics as $clinic)
                    <div style="padding: 12px 20px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <p style="font-weight: 500; color: #1e293b; margin: 0; font-size: 14px;">{{ $clinic->name }}</p>
                            <p style="font-size: 12px; color: #94a3b8; margin: 4px 0 0 0;">Created {{ $clinic->created_at->diffForHumans() }}</p>
                        </div>
                        <div>
                            <span style="background: #f1f5f9; color: #64748b; font-size: 11px; padding: 4px 8px; border-radius: 9999px; font-weight: 500;">
                                {{ $clinic->phoneNumbers->count() }} numbers
                            </span>
                        </div>
                    </div>
                @empty
                    <div style="padding: 32px 20px; text-align: center;">
                        <p style="color: #94a3b8; margin: 0;">No clinics yet</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Phone Numbers by Provider -->
        <div style="background: #fff; border-radius: 12px; border: 1px solid #e2e8f0;">
            <div style="padding: 16px 20px; border-bottom: 1px solid #f1f5f9;">
                <h3 style="font-weight: 600; color: #1e293b; margin: 0; font-size: 15px;">Numbers by Provider</h3>
            </div>
            <div style="padding: 20px;">
                @php
                    $providerColors = [
                        'twilio' => '#ef4444',
                        'vonage' => '#8b5cf6',
                        'messagebird' => '#3b82f6',
                    ];
                @endphp
                @forelse($phoneNumbersByProvider as $provider => $count)
                    <div style="margin-bottom: 16px;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                            <span style="color: #334155; font-weight: 500; text-transform: capitalize; font-size: 14px;">{{ $provider }}</span>
                            <span style="color: #64748b; font-size: 14px;">{{ $count }}</span>
                        </div>
                        <div style="background: #f1f5f9; border-radius: 4px; height: 8px; overflow: hidden;">
                            @php
                                $maxCount = max($phoneNumbersByProvider) ?: 1;
                                $percentage = ($count / $maxCount) * 100;
                            @endphp
                            <div style="background: {{ $providerColors[$provider] ?? '#6b7280' }}; height: 100%; width: {{ $percentage }}%; border-radius: 4px;"></div>
                        </div>
                    </div>
                @empty
                    <p style="color: #94a3b8; text-align: center; margin: 20px 0;">No phone numbers yet</p>
                @endforelse

                <a href="{{ route('admin.phone-numbers.index') }}" style="display: block; text-align: center; margin-top: 20px; color: #7c3aed; text-decoration: none; font-size: 14px; font-weight: 500;">
                    Manage Phone Numbers &rarr;
                </a>
            </div>
        </div>
    </div>
</x-admin-layout>
