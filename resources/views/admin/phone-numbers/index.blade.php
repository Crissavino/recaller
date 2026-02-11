<x-admin-layout>
    <x-slot name="header">
        <h2 style="font-weight: 600; font-size: 20px; color: #1e293b; margin: 0;">
            Phone Numbers Management
        </h2>
    </x-slot>

    @if(session('success'))
        <div style="background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; padding: 14px 16px; border-radius: 10px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; font-size: 14px;" x-data="{ show: true }" x-show="show" x-transition>
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span style="flex: 1;">{{ session('success') }}</span>
            <button @click="show = false" style="background: none; border: none; cursor: pointer; color: #166534; opacity: 0.6;">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 14px 16px; border-radius: 10px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; font-size: 14px;" x-data="{ show: true }" x-show="show" x-transition>
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span style="flex: 1;">{{ session('error') }}</span>
            <button @click="show = false" style="background: none; border: none; cursor: pointer; color: #991b1b; opacity: 0.6;">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    @endif

    <!-- Add New Number Form -->
    <div style="background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; margin-bottom: 24px;" x-data="{ showForm: false, clinicPhones: {{ $clinics->pluck('phone', 'id')->toJson() }} }">
        <div style="padding: 16px 20px; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="font-weight: 600; color: #1e293b; margin: 0; font-size: 15px;">Add New Phone Number</h3>
            <button @click="showForm = !showForm" style="display: inline-flex; align-items: center; gap: 6px; background: #7c3aed; color: #fff; padding: 8px 14px; border-radius: 8px; font-size: 13px; font-weight: 500; border: none; cursor: pointer;">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add Number
            </button>
        </div>

        <div x-show="showForm" x-transition style="padding: 20px; border-top: 1px solid #e2e8f0; background: #f8fafc;">
            <form action="{{ route('admin.phone-numbers.store') }}" method="POST">
                @csrf
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px; margin-bottom: 16px;">
                    <div>
                        <label style="display: block; font-size: 12px; font-weight: 500; color: #64748b; margin-bottom: 4px;">Clinic *</label>
                        <select name="clinic_id" required x-on:change="$refs.forwardTo.value = clinicPhones[$event.target.value] || ''" style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; background: #fff; color: #1e293b;">
                            <option value="">Select clinic...</option>
                            @foreach($clinics as $clinic)
                                <option value="{{ $clinic->id }}">{{ $clinic->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="display: block; font-size: 12px; font-weight: 500; color: #64748b; margin-bottom: 4px;">Phone Number *</label>
                        <input type="text" name="phone_number" required placeholder="+40721234567"
                            style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; background: #fff; color: #1e293b;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 12px; font-weight: 500; color: #64748b; margin-bottom: 4px;">Type *</label>
                        <select name="type" required style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; background: #fff; color: #1e293b;">
                            <option value="voice">Voice</option>
                            <option value="whatsapp">WhatsApp</option>
                        </select>
                    </div>
                    <div>
                        <label style="display: block; font-size: 12px; font-weight: 500; color: #64748b; margin-bottom: 4px;">Provider *</label>
                        <select name="provider" required style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; background: #fff; color: #1e293b;">
                            <option value="twilio">Twilio</option>
                            <option value="vonage">Vonage</option>
                            <option value="messagebird">MessageBird</option>
                        </select>
                    </div>
                    <div>
                        <label style="display: block; font-size: 12px; font-weight: 500; color: #64748b; margin-bottom: 4px;">Country</label>
                        <input type="text" name="country" placeholder="RO" maxlength="2"
                            style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; background: #fff; color: #1e293b;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 12px; font-weight: 500; color: #64748b; margin-bottom: 4px;">Friendly Name</label>
                        <input type="text" name="friendly_name" placeholder="Main Line"
                            style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; background: #fff; color: #1e293b;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 12px; font-weight: 500; color: #64748b; margin-bottom: 4px;">Forward To (Voice)</label>
                        <input type="text" name="forward_to_phone" x-ref="forwardTo" placeholder="+40721234567"
                            style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; background: #fff; color: #1e293b;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 12px; font-weight: 500; color: #64748b; margin-bottom: 4px;">Timeout (sec)</label>
                        <input type="number" name="forward_timeout_seconds" value="20" min="5" max="60"
                            style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; background: #fff; color: #1e293b;">
                    </div>
                </div>
                <div style="display: flex; gap: 8px;">
                    <button type="submit" style="background: #7c3aed; color: #fff; padding: 10px 20px; border-radius: 8px; font-size: 14px; font-weight: 500; border: none; cursor: pointer;">
                        Add Number
                    </button>
                    <button type="button" @click="showForm = false" style="background: #fff; color: #475569; border: 1px solid #e2e8f0; padding: 10px 20px; border-radius: 8px; font-size: 14px; cursor: pointer;">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Filters -->
    <div style="background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 16px 20px; margin-bottom: 24px;">
        <form method="GET" action="{{ route('admin.phone-numbers.index') }}" style="display: flex; flex-wrap: wrap; gap: 12px; align-items: flex-end;">
            <div>
                <label style="display: block; font-size: 11px; font-weight: 600; color: #64748b; margin-bottom: 4px; text-transform: uppercase;">Clinic</label>
                <select name="clinic" style="padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px; background: #fff; color: #1e293b; min-width: 150px;">
                    <option value="">All Clinics</option>
                    @foreach($clinics as $clinic)
                        <option value="{{ $clinic->id }}" {{ request('clinic') == $clinic->id ? 'selected' : '' }}>{{ $clinic->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="display: block; font-size: 11px; font-weight: 600; color: #64748b; margin-bottom: 4px; text-transform: uppercase;">Type</label>
                <select name="type" style="padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px; background: #fff; color: #1e293b;">
                    <option value="">All Types</option>
                    <option value="voice" {{ request('type') == 'voice' ? 'selected' : '' }}>Voice</option>
                    <option value="whatsapp" {{ request('type') == 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                </select>
            </div>
            <div>
                <label style="display: block; font-size: 11px; font-weight: 600; color: #64748b; margin-bottom: 4px; text-transform: uppercase;">Provider</label>
                <select name="provider" style="padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px; background: #fff; color: #1e293b;">
                    <option value="">All Providers</option>
                    <option value="twilio" {{ request('provider') == 'twilio' ? 'selected' : '' }}>Twilio</option>
                    <option value="vonage" {{ request('provider') == 'vonage' ? 'selected' : '' }}>Vonage</option>
                    <option value="messagebird" {{ request('provider') == 'messagebird' ? 'selected' : '' }}>MessageBird</option>
                </select>
            </div>
            <div>
                <label style="display: block; font-size: 11px; font-weight: 600; color: #64748b; margin-bottom: 4px; text-transform: uppercase;">Country</label>
                <input type="text" name="country" value="{{ request('country') }}" placeholder="RO" maxlength="2"
                    style="width: 60px; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px; background: #fff; color: #1e293b;">
            </div>
            <button type="submit" style="background: #f1f5f9; color: #475569; padding: 8px 16px; border-radius: 8px; font-size: 13px; border: 1px solid #e2e8f0; cursor: pointer; font-weight: 500;">
                Filter
            </button>
            @if(request()->hasAny(['clinic', 'type', 'provider', 'country']))
                <a href="{{ route('admin.phone-numbers.index') }}" style="color: #64748b; font-size: 13px; text-decoration: none;">Clear</a>
            @endif
        </form>
    </div>

    <!-- Phone Numbers Table -->
    <div style="background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; overflow: hidden;">
        @if($phoneNumbers->isEmpty())
            <div style="padding: 48px 20px; text-align: center;">
                <div style="width: 64px; height: 64px; background: #f1f5f9; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                    <svg width="32" height="32" fill="none" stroke="#94a3b8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                </div>
                <p style="color: #64748b; font-size: 14px; margin: 0;">No phone numbers found</p>
            </div>
        @else
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f8fafc;">
                            <th style="padding: 12px 16px; text-align: left; font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase;">Clinic</th>
                            <th style="padding: 12px 16px; text-align: left; font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase;">Number</th>
                            <th style="padding: 12px 16px; text-align: left; font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase;">Type</th>
                            <th style="padding: 12px 16px; text-align: left; font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase;">Provider</th>
                            <th style="padding: 12px 16px; text-align: left; font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase;">Forward To</th>
                            <th style="padding: 12px 16px; text-align: left; font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase;">WhatsApp Link</th>
                            <th style="padding: 12px 16px; text-align: left; font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase;">Status</th>
                            <th style="padding: 12px 16px; text-align: right; font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($phoneNumbers as $number)
                            <tr style="border-top: 1px solid #f1f5f9;" x-data="{ editing: false, confirmDelete: false }">
                                <!-- View Mode -->
                                <td x-show="!editing" style="padding: 14px 16px; color: #1e293b; font-size: 14px;">{{ $number->clinic?->name ?? 'N/A' }}</td>
                                <td x-show="!editing" style="padding: 14px 16px;">
                                    <span style="font-weight: 500; color: #1e293b;">{{ $number->getCleanPhoneNumber() }}</span>
                                    @if($number->country)
                                        <span style="margin-left: 6px; font-size: 10px; background: #f1f5f9; color: #475569; padding: 2px 6px; border-radius: 4px; font-weight: 600;">{{ $number->country }}</span>
                                    @endif
                                    @if($number->friendly_name)
                                        <div style="font-size: 11px; color: #94a3b8; margin-top: 2px;">{{ $number->friendly_name }}</div>
                                    @endif
                                </td>
                                <td x-show="!editing" style="padding: 14px 16px;">
                                    @if($number->type === 'whatsapp')
                                        <span style="display: inline-flex; align-items: center; gap: 4px; background: #dcfce7; color: #166534; font-size: 11px; padding: 4px 8px; border-radius: 6px; font-weight: 600;">
                                            WhatsApp
                                        </span>
                                    @else
                                        <span style="display: inline-flex; align-items: center; gap: 4px; background: #dbeafe; color: #1e40af; font-size: 11px; padding: 4px 8px; border-radius: 6px; font-weight: 600;">
                                            Voice
                                        </span>
                                    @endif
                                </td>
                                <td x-show="!editing" style="padding: 14px 16px;">
                                    @php
                                        $providerColors = [
                                            'twilio' => 'background: #fef2f2; color: #991b1b;',
                                            'vonage' => 'background: #f5f3ff; color: #5b21b6;',
                                            'messagebird' => 'background: #eff6ff; color: #1d4ed8;',
                                        ];
                                    @endphp
                                    <span style="{{ $providerColors[$number->provider] ?? 'background: #f1f5f9; color: #64748b;' }} font-size: 11px; padding: 4px 8px; border-radius: 6px; font-weight: 600; text-transform: capitalize;">
                                        {{ $number->provider }}
                                    </span>
                                </td>
                                <td x-show="!editing" style="padding: 14px 16px; color: #64748b; font-size: 13px;">
                                    @if($number->forward_to_phone)
                                        {{ $number->forward_to_phone }}
                                        <div style="font-size: 11px; color: #94a3b8;">{{ $number->forward_timeout_seconds }}s timeout</div>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td x-show="!editing" style="padding: 14px 16px; color: #64748b; font-size: 13px;">
                                    @if($number->linkedWhatsAppNumber)
                                        <span style="color: #059669;">{{ $number->linkedWhatsAppNumber->getCleanPhoneNumber() }}</span>
                                    @elseif($number->type === 'whatsapp')
                                        <span style="font-size: 11px; color: #94a3b8;">N/A</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td x-show="!editing" style="padding: 14px 16px;">
                                    @if($number->is_active)
                                        <span style="background: #dcfce7; color: #166534; font-size: 11px; padding: 4px 8px; border-radius: 9999px; font-weight: 600;">Active</span>
                                    @else
                                        <span style="background: #f1f5f9; color: #64748b; font-size: 11px; padding: 4px 8px; border-radius: 9999px; font-weight: 600;">Inactive</span>
                                    @endif
                                </td>
                                <td x-show="!editing" style="padding: 14px 16px; text-align: right;">
                                    <div style="display: flex; gap: 6px; justify-content: flex-end;">
                                        <button @click="editing = true" style="background: #f1f5f9; border: 1px solid #e2e8f0; padding: 6px 10px; border-radius: 6px; font-size: 11px; color: #475569; cursor: pointer; font-weight: 500;">
                                            Edit
                                        </button>
                                        <form action="{{ route('admin.phone-numbers.toggle', $number->id) }}" method="POST" style="margin: 0;">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" style="background: #f1f5f9; border: 1px solid #e2e8f0; padding: 6px 10px; border-radius: 6px; font-size: 11px; color: #475569; cursor: pointer; font-weight: 500;">
                                                {{ $number->is_active ? 'Disable' : 'Enable' }}
                                            </button>
                                        </form>
                                        <button @click="confirmDelete = true" style="background: #fef2f2; border: 1px solid #fecaca; padding: 6px 10px; border-radius: 6px; font-size: 11px; color: #991b1b; cursor: pointer; font-weight: 500;">
                                            Delete
                                        </button>
                                    </div>

                                    <!-- Delete Confirmation Modal -->
                                    <div x-show="confirmDelete" x-transition style="position: fixed; inset: 0; background: rgba(0,0,0,0.4); display: flex; align-items: center; justify-content: center; z-index: 50;">
                                        <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; max-width: 400px; margin: 16px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);">
                                            <h4 style="font-size: 16px; font-weight: 600; color: #1e293b; margin: 0 0 8px 0;">Delete phone number?</h4>
                                            <p style="font-size: 14px; color: #64748b; margin: 0 0 20px 0;">This will remove <strong style="color: #1e293b;">{{ $number->getCleanPhoneNumber() }}</strong> from {{ $number->clinic?->name }}. This action cannot be undone.</p>
                                            <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                                <button @click="confirmDelete = false" style="background: #f1f5f9; border: 1px solid #e2e8f0; padding: 10px 16px; border-radius: 8px; font-size: 14px; color: #475569; cursor: pointer;">
                                                    Cancel
                                                </button>
                                                <form action="{{ route('admin.phone-numbers.destroy', $number->id) }}" method="POST" style="margin: 0;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" style="background: #dc2626; border: none; padding: 10px 16px; border-radius: 8px; font-size: 14px; color: #fff; font-weight: 500; cursor: pointer;">
                                                        Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Edit Mode -->
                                <td x-show="editing" colspan="8" style="padding: 16px; background: #f8fafc;">
                                    <form action="{{ route('admin.phone-numbers.update', $number->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 12px; margin-bottom: 12px;">
                                            <div>
                                                <label style="display: block; font-size: 12px; font-weight: 500; color: #64748b; margin-bottom: 4px;">Phone Number</label>
                                                <input type="text" value="{{ $number->getCleanPhoneNumber() }}" disabled
                                                    style="width: 100%; padding: 8px 10px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 13px; background: #f1f5f9; color: #94a3b8;">
                                            </div>
                                            <div>
                                                <label style="display: block; font-size: 12px; font-weight: 500; color: #64748b; margin-bottom: 4px;">Friendly Name</label>
                                                <input type="text" name="friendly_name" value="{{ $number->friendly_name }}"
                                                    style="width: 100%; padding: 8px 10px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px; background: #fff; color: #1e293b;">
                                            </div>
                                            <div>
                                                <label style="display: block; font-size: 12px; font-weight: 500; color: #64748b; margin-bottom: 4px;">Forward To</label>
                                                <input type="text" name="forward_to_phone" value="{{ $number->forward_to_phone }}" placeholder="+40721234567"
                                                    style="width: 100%; padding: 8px 10px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px; background: #fff; color: #1e293b;">
                                            </div>
                                            <div>
                                                <label style="display: block; font-size: 12px; font-weight: 500; color: #64748b; margin-bottom: 4px;">Timeout (sec)</label>
                                                <input type="number" name="forward_timeout_seconds" value="{{ $number->forward_timeout_seconds ?? 20 }}" min="5" max="60"
                                                    style="width: 100%; padding: 8px 10px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px; background: #fff; color: #1e293b;">
                                            </div>
                                            <div>
                                                <label style="display: block; font-size: 12px; font-weight: 500; color: #64748b; margin-bottom: 4px;">Link to WhatsApp</label>
                                                <select name="linked_whatsapp_number_id" style="width: 100%; padding: 8px 10px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px; background: #fff; color: #1e293b;">
                                                    <option value="">None</option>
                                                    @foreach($whatsAppNumbers[$number->clinic_id] ?? [] as $wa)
                                                        <option value="{{ $wa->id }}" {{ $number->linked_whatsapp_number_id == $wa->id ? 'selected' : '' }}>{{ $wa->getCleanPhoneNumber() }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div style="display: flex; gap: 8px;">
                                            <button type="submit" style="background: #7c3aed; color: #fff; padding: 8px 14px; border-radius: 8px; font-size: 13px; font-weight: 500; border: none; cursor: pointer;">
                                                Save Changes
                                            </button>
                                            <button type="button" @click="editing = false" style="background: #fff; color: #475569; border: 1px solid #e2e8f0; padding: 8px 14px; border-radius: 8px; font-size: 13px; cursor: pointer;">
                                                Cancel
                                            </button>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($phoneNumbers->hasPages())
                <div style="padding: 16px 20px; border-top: 1px solid #f1f5f9;">
                    {{ $phoneNumbers->appends(request()->query())->links() }}
                </div>
            @endif
        @endif
    </div>
</x-admin-layout>
