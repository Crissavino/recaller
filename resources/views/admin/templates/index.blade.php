<x-admin-layout>
    <x-slot name="header">
        <h2 style="font-weight: 600; font-size: 20px; color: #1e293b; margin: 0;">
            Templates Management
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

    <!-- Filters -->
    <div style="background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 16px 20px; margin-bottom: 24px;">
        <form method="GET" action="{{ route('admin.templates.index') }}" style="display: flex; flex-wrap: wrap; gap: 12px; align-items: flex-end;">
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
                <label style="display: block; font-size: 11px; font-weight: 600; color: #64748b; margin-bottom: 4px; text-transform: uppercase;">Channel</label>
                <select name="channel" style="padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px; background: #fff; color: #1e293b;">
                    <option value="">All Channels</option>
                    <option value="sms" {{ request('channel') == 'sms' ? 'selected' : '' }}>SMS</option>
                    <option value="whatsapp" {{ request('channel') == 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                </select>
            </div>
            <button type="submit" style="background: #f1f5f9; color: #475569; padding: 8px 16px; border-radius: 8px; font-size: 13px; border: 1px solid #e2e8f0; cursor: pointer; font-weight: 500;">
                Filter
            </button>
            @if(request()->hasAny(['clinic', 'channel']))
                <a href="{{ route('admin.templates.index') }}" style="color: #64748b; font-size: 13px; text-decoration: none;">Clear</a>
            @endif
        </form>
    </div>

    <!-- Templates Table -->
    <div style="background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; overflow: hidden;">
        @if($templates->isEmpty())
            <div style="padding: 48px 20px; text-align: center;">
                <p style="color: #64748b; font-size: 14px; margin: 0;">No templates found</p>
            </div>
        @else
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f8fafc;">
                            <th style="padding: 12px 16px; text-align: left; font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase;">Clinic</th>
                            <th style="padding: 12px 16px; text-align: left; font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase;">Name</th>
                            <th style="padding: 12px 16px; text-align: left; font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase;">Trigger</th>
                            <th style="padding: 12px 16px; text-align: left; font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase;">Channel</th>
                            <th style="padding: 12px 16px; text-align: left; font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase;">Content SID</th>
                            <th style="padding: 12px 16px; text-align: left; font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase;">Status</th>
                            <th style="padding: 12px 16px; text-align: right; font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($templates as $template)
                            <tr style="border-top: 1px solid #f1f5f9;" x-data="{ editing: false }">
                                <!-- View Mode -->
                                <td x-show="!editing" style="padding: 14px 16px; color: #1e293b; font-size: 14px;">{{ $template->clinic?->name ?? 'N/A' }}</td>
                                <td x-show="!editing" style="padding: 14px 16px; color: #1e293b; font-weight: 500;">{{ $template->name }}</td>
                                <td x-show="!editing" style="padding: 14px 16px;">
                                    <span style="background: #f1f5f9; color: #475569; font-size: 11px; padding: 4px 8px; border-radius: 6px; font-weight: 500;">
                                        {{ $template->trigger_event }}
                                    </span>
                                </td>
                                <td x-show="!editing" style="padding: 14px 16px;">
                                    @if($template->channel->value === 'whatsapp')
                                        <span style="background: #dcfce7; color: #166534; font-size: 11px; padding: 4px 8px; border-radius: 6px; font-weight: 600;">WhatsApp</span>
                                    @else
                                        <span style="background: #dbeafe; color: #1e40af; font-size: 11px; padding: 4px 8px; border-radius: 6px; font-weight: 600;">SMS</span>
                                    @endif
                                </td>
                                <td x-show="!editing" style="padding: 14px 16px; color: #64748b; font-size: 13px; font-family: ui-monospace, monospace;">
                                    {{ $template->content_sid ?? '-' }}
                                </td>
                                <td x-show="!editing" style="padding: 14px 16px;">
                                    @if($template->is_active)
                                        <span style="background: #dcfce7; color: #166534; font-size: 11px; padding: 4px 8px; border-radius: 9999px; font-weight: 600;">Active</span>
                                    @else
                                        <span style="background: #f1f5f9; color: #64748b; font-size: 11px; padding: 4px 8px; border-radius: 9999px; font-weight: 600;">Inactive</span>
                                    @endif
                                </td>
                                <td x-show="!editing" style="padding: 14px 16px; text-align: right;">
                                    <button @click="editing = true" style="background: #f1f5f9; border: 1px solid #e2e8f0; padding: 6px 10px; border-radius: 6px; font-size: 11px; color: #475569; cursor: pointer; font-weight: 500;">
                                        Edit
                                    </button>
                                </td>

                                <!-- Edit Mode -->
                                <td x-show="editing" colspan="7" style="padding: 16px; background: #f8fafc;">
                                    <form action="{{ route('admin.templates.update', $template->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div style="margin-bottom: 8px; color: #1e293b; font-size: 13px;">
                                            <strong>{{ $template->clinic?->name }}</strong> — {{ $template->name }}
                                        </div>
                                        <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; margin-bottom: 12px; font-size: 13px; color: #64748b; line-height: 1.6;">
                                            {{ $template->body }}
                                        </div>
                                        <div style="display: grid; grid-template-columns: 200px 1fr; gap: 12px; margin-bottom: 12px;">
                                            <div>
                                                <label style="display: block; font-size: 12px; font-weight: 500; color: #64748b; margin-bottom: 4px;">Channel</label>
                                                <select name="channel" style="width: 100%; padding: 8px 10px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px; background: #fff; color: #1e293b;">
                                                    <option value="sms" {{ $template->channel->value === 'sms' ? 'selected' : '' }}>SMS</option>
                                                    <option value="whatsapp" {{ $template->channel->value === 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label style="display: block; font-size: 12px; font-weight: 500; color: #64748b; margin-bottom: 4px;">Content SID (Twilio WhatsApp Template)</label>
                                                <input type="text" name="content_sid" value="{{ $template->content_sid }}" placeholder="HX..."
                                                    style="width: 100%; padding: 8px 10px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px; background: #fff; color: #1e293b; font-family: ui-monospace, monospace;">
                                            </div>
                                        </div>
                                        <div style="display: flex; gap: 8px;">
                                            <button type="submit" style="background: #7c3aed; color: #fff; padding: 8px 14px; border-radius: 8px; font-size: 13px; font-weight: 500; border: none; cursor: pointer;">
                                                Save
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

            @if($templates->hasPages())
                <div style="padding: 16px 20px; border-top: 1px solid #f1f5f9;">
                    {{ $templates->appends(request()->query())->links() }}
                </div>
            @endif
        @endif
    </div>
</x-admin-layout>
