<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('settings.title') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="alert alert-success" x-data="{ show: true }" x-show="show" x-transition>
                    <div class="alert-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="alert-content">{{ session('success') }}</div>
                    <button type="button" class="alert-close" @click="show = false">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-error" x-data="{ show: true }" x-show="show" x-transition>
                    <div class="alert-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="alert-content">{{ session('error') }}</div>
                    <button type="button" class="alert-close" @click="show = false">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            @endif

            @if(session('info'))
                <div class="alert alert-info" x-data="{ show: true }" x-show="show" x-transition>
                    <div class="alert-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="alert-content">{{ session('info') }}</div>
                    <button type="button" class="alert-close" @click="show = false">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            @endif

            <style>
                .alert {
                    display: flex;
                    align-items: flex-start;
                    gap: 12px;
                    padding: 16px;
                    border-radius: 12px;
                    margin-bottom: 24px;
                    font-size: 14px;
                    animation: slideDown 0.3s ease-out;
                }
                @keyframes slideDown {
                    from { opacity: 0; transform: translateY(-10px); }
                    to { opacity: 1; transform: translateY(0); }
                }
                .alert-success {
                    background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
                    border: 1px solid #a7f3d0;
                    color: #065f46;
                }
                .alert-error {
                    background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
                    border: 1px solid #fecaca;
                    color: #991b1b;
                }
                .alert-info {
                    background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
                    border: 1px solid #bfdbfe;
                    color: #1e40af;
                }
                .alert-icon {
                    flex-shrink: 0;
                    width: 24px;
                    height: 24px;
                }
                .alert-icon svg {
                    width: 24px;
                    height: 24px;
                }
                .alert-content {
                    flex: 1;
                    font-weight: 500;
                    line-height: 1.5;
                }
                .alert-close {
                    flex-shrink: 0;
                    width: 20px;
                    height: 20px;
                    background: none;
                    border: none;
                    cursor: pointer;
                    opacity: 0.5;
                    transition: opacity 0.2s;
                    padding: 0;
                }
                .alert-close:hover {
                    opacity: 1;
                }
                .alert-close svg {
                    width: 20px;
                    height: 20px;
                }

                /* Modal styles */
                .modal-backdrop {
                    position: fixed;
                    inset: 0;
                    background: rgba(0, 0, 0, 0.6);
                    backdrop-filter: blur(4px);
                    z-index: 50;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    padding: 16px;
                    animation: fadeIn 0.2s ease-out;
                }
                @keyframes fadeIn {
                    from { opacity: 0; }
                    to { opacity: 1; }
                }
                .modal-content {
                    background: #fff;
                    border-radius: 20px;
                    max-width: 440px;
                    width: 100%;
                    padding: 28px;
                    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
                    animation: slideUp 0.3s ease-out;
                }
                @keyframes slideUp {
                    from { opacity: 0; transform: translateY(20px) scale(0.95); }
                    to { opacity: 1; transform: translateY(0) scale(1); }
                }
                .modal-icon {
                    width: 56px;
                    height: 56px;
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    margin: 0 auto 20px;
                }
                .modal-icon.danger {
                    background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
                }
                .modal-icon.danger svg {
                    color: #dc2626;
                }
                .modal-title {
                    font-size: 20px;
                    font-weight: 700;
                    color: #111827;
                    text-align: center;
                    margin: 0 0 12px 0;
                }
                .modal-description {
                    font-size: 14px;
                    color: #6b7280;
                    text-align: center;
                    margin: 0 0 16px 0;
                    line-height: 1.6;
                }
                .modal-warning-list {
                    background: #fef2f2;
                    border-radius: 12px;
                    padding: 16px;
                    margin: 0 0 24px 0;
                }
                .modal-warning-list ul {
                    margin: 0;
                    padding: 0;
                    list-style: none;
                }
                .modal-warning-list li {
                    display: flex;
                    align-items: flex-start;
                    gap: 10px;
                    font-size: 13px;
                    color: #991b1b;
                    padding: 6px 0;
                }
                .modal-warning-list li::before {
                    content: '×';
                    font-weight: bold;
                    font-size: 16px;
                    line-height: 1;
                }
                .modal-actions {
                    display: flex;
                    gap: 12px;
                }
                .modal-actions button {
                    flex: 1;
                    padding: 14px 20px;
                    border-radius: 12px;
                    font-size: 15px;
                    font-weight: 600;
                    cursor: pointer;
                    transition: all 0.2s;
                }
                .btn-cancel-modal {
                    background: #f3f4f6;
                    border: none;
                    color: #374151;
                }
                .btn-cancel-modal:hover {
                    background: #e5e7eb;
                }
                .btn-danger {
                    background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
                    border: none;
                    color: #fff;
                }
                .btn-danger:hover {
                    background: linear-gradient(135deg, #b91c1c 0%, #991b1b 100%);
                    transform: translateY(-1px);
                }
            </style>

            <!-- General Settings -->
            <div style="background: #fff; border-radius: 12px; border: 1px solid #e5e7eb; margin-bottom: 24px;">
                <div style="padding: 16px 20px; border-bottom: 1px solid #f3f4f6;">
                    <h3 style="font-weight: 600; color: #111827; margin: 0;">{{ __('settings.general') }}</h3>
                </div>
                <form action="{{ route('settings.update.general') }}" method="POST" style="padding: 20px;">
                    @csrf
                    @method('PUT')

                    <div style="margin-bottom: 16px;">
                        <label style="display: block; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 6px;">{{ __('settings.clinic_name') }}</label>
                        <input type="text" name="name" value="{{ old('name', $clinic->name) }}" required
                            style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;">
                        @error('name')
                            <p style="color: #dc2626; font-size: 12px; margin-top: 4px;">{{ $message }}</p>
                        @enderror
                    </div>

                    <div style="margin-bottom: 16px;">
                        <label style="display: block; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 6px;">{{ __('settings.booking_link') }}</label>
                        <input type="url" name="booking_link" value="{{ old('booking_link', $clinic->settings?->booking_link) }}" placeholder="https://your-booking-page.com"
                            style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;">
                        <p style="font-size: 12px; color: #9ca3af; margin-top: 4px;">{{ app()->getLocale() === 'es' ? 'Usado en templates SMS como' : 'Used in SMS templates as' }} @{{booking_link}}</p>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 6px;">{{ __('settings.business_hours') }}</label>
                        <input type="text" name="business_hours_text" value="{{ old('business_hours_text', $clinic->settings?->business_hours_text) }}" placeholder="{{ app()->getLocale() === 'es' ? 'Lun-Vie 9am-6pm' : 'Mon-Fri 9am-6pm' }}"
                            style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;">
                        <p style="font-size: 12px; color: #9ca3af; margin-top: 4px;">{{ app()->getLocale() === 'es' ? 'Usado en templates SMS como' : 'Used in SMS templates as' }} @{{business_hours}}</p>
                    </div>

                    <button type="submit" style="background: #111827; color: #fff; padding: 10px 20px; border-radius: 8px; font-size: 14px; font-weight: 500; border: none; cursor: pointer;">
                        {{ __('settings.save') }}
                    </button>
                </form>
            </div>

            <!-- Follow-up Settings -->
            <div style="background: #fff; border-radius: 12px; border: 1px solid #e5e7eb; margin-bottom: 24px;">
                <div style="padding: 16px 20px; border-bottom: 1px solid #f3f4f6;">
                    <h3 style="font-weight: 600; color: #111827; margin: 0;">{{ __('settings.followup') }}</h3>
                </div>
                <form action="{{ route('settings.update.followup') }}" method="POST" style="padding: 20px;">
                    @csrf
                    @method('PUT')

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 20px;">
                        <div>
                            <label style="display: block; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 6px;">{{ __('settings.followup_delay') }}</label>
                            <input type="number" name="followup_delay_seconds" value="{{ old('followup_delay_seconds', $clinic->settings?->followup_delay_seconds ?? 60) }}" min="30" max="3600" required
                                style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;">
                            <p style="font-size: 12px; color: #9ca3af; margin-top: 4px;">{{ __('settings.followup_delay_help') }}</p>
                        </div>

                        <div>
                            <label style="display: block; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 6px;">{{ __('settings.avg_ticket') }} ($)</label>
                            <input type="number" name="avg_ticket_value" value="{{ old('avg_ticket_value', $clinic->settings?->avg_ticket_value ?? 250) }}" min="0" step="0.01" required
                                style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;">
                            <p style="font-size: 12px; color: #9ca3af; margin-top: 4px;">{{ app()->getLocale() === 'es' ? 'Usado para calcular ingresos potenciales' : 'Used to calculate potential revenue' }}</p>
                        </div>
                    </div>

                    <button type="submit" style="background: #111827; color: #fff; padding: 10px 20px; border-radius: 8px; font-size: 14px; font-weight: 500; border: none; cursor: pointer;">
                        {{ __('settings.save') }}
                    </button>
                </form>
            </div>

            <!-- Notification Preferences -->
            <div style="background: #fff; border-radius: 12px; border: 1px solid #e5e7eb; margin-bottom: 24px;">
                <div style="padding: 16px 20px; border-bottom: 1px solid #f3f4f6;">
                    <h3 style="font-weight: 600; color: #111827; margin: 0;">{{ __('settings.notifications') }}</h3>
                </div>
                <form action="{{ route('settings.update.notifications') }}" method="POST" style="padding: 20px;">
                    @csrf
                    @method('PUT')

                    <div style="display: flex; flex-direction: column; gap: 16px; margin-bottom: 20px;">
                        <label style="display: flex; align-items: flex-start; gap: 12px; cursor: pointer;">
                            <input type="checkbox" name="email_new_lead" value="1" {{ $notificationPreferences['email_new_lead'] ? 'checked' : '' }}
                                style="width: 20px; height: 20px; margin-top: 2px; accent-color: #0ea5e9;">
                            <div>
                                <span style="font-weight: 500; color: #111827; display: block;">{{ __('settings.notify_new_lead') }}</span>
                                <span style="font-size: 13px; color: #6b7280;">{{ __('settings.notify_new_lead_help') }}</span>
                            </div>
                        </label>

                        <label style="display: flex; align-items: flex-start; gap: 12px; cursor: pointer;">
                            <input type="checkbox" name="email_lead_responded" value="1" {{ $notificationPreferences['email_lead_responded'] ? 'checked' : '' }}
                                style="width: 20px; height: 20px; margin-top: 2px; accent-color: #0ea5e9;">
                            <div>
                                <span style="font-weight: 500; color: #111827; display: block;">{{ __('settings.notify_lead_responded') }}</span>
                                <span style="font-size: 13px; color: #6b7280;">{{ __('settings.notify_lead_responded_help') }}</span>
                            </div>
                        </label>

                        <label style="display: flex; align-items: flex-start; gap: 12px; cursor: pointer; opacity: 0.5;" title="{{ __('common.coming_soon') }}">
                            <input type="checkbox" name="email_daily_summary" value="1" {{ $notificationPreferences['email_daily_summary'] ? 'checked' : '' }} disabled
                                style="width: 20px; height: 20px; margin-top: 2px;">
                            <div>
                                <span style="font-weight: 500; color: #111827; display: block;">
                                    {{ __('settings.notify_daily_summary') }}
                                    <span style="background: #f3f4f6; color: #6b7280; font-size: 10px; padding: 2px 6px; border-radius: 4px; margin-left: 6px;">{{ __('common.coming_soon') }}</span>
                                </span>
                                <span style="font-size: 13px; color: #6b7280;">{{ __('settings.notify_daily_summary_help') }}</span>
                            </div>
                        </label>
                    </div>

                    <button type="submit" style="background: #111827; color: #fff; padding: 10px 20px; border-radius: 8px; font-size: 14px; font-weight: 500; border: none; cursor: pointer;">
                        {{ __('settings.save') }}
                    </button>
                </form>
            </div>

            <!-- Message Templates -->
            <div style="background: #fff; border-radius: 12px; border: 1px solid #e5e7eb; margin-bottom: 24px;" x-data="{ showNewForm: false }">
                <div style="padding: 16px 20px; border-bottom: 1px solid #f3f4f6; display: flex; justify-content: space-between; align-items: center;">
                    <h3 style="font-weight: 600; color: #111827; margin: 0;">{{ __('settings.templates') }}</h3>
                    <button @click="showNewForm = !showNewForm" style="display: inline-flex; align-items: center; gap: 6px; background: #0ea5e9; color: #fff; padding: 8px 14px; border-radius: 8px; font-size: 13px; font-weight: 500; border: none; cursor: pointer;">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        {{ __('settings.add_template') }}
                    </button>
                </div>

                <!-- New Template Form -->
                <div x-show="showNewForm" x-transition style="padding: 20px; background: #f0f9ff; border-bottom: 1px solid #bae6fd;">
                    <form action="{{ route('settings.store.template') }}" method="POST">
                        @csrf
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                            <div>
                                <label style="display: block; font-size: 13px; font-weight: 500; color: #374151; margin-bottom: 4px;">{{ __('settings.template_name') }}</label>
                                <input type="text" name="name" required placeholder="{{ __('settings.template_name_placeholder') }}"
                                    style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;">
                            </div>
                            <div>
                                <label style="display: block; font-size: 13px; font-weight: 500; color: #374151; margin-bottom: 4px;">{{ __('settings.trigger_event') }}</label>
                                <select name="trigger_event" required style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; background: #fff;">
                                    <option value="missed_call">{{ __('settings.trigger_missed_call') }}</option>
                                    <option value="no_response">{{ __('settings.trigger_no_response') }}</option>
                                    <option value="follow_up">{{ __('settings.trigger_follow_up') }}</option>
                                </select>
                            </div>
                        </div>
                        <div style="margin-bottom: 16px;">
                            <label style="display: block; font-size: 13px; font-weight: 500; color: #374151; margin-bottom: 4px;">{{ __('settings.template_body') }}</label>
                            <textarea name="body" rows="3" required placeholder="{{ __('settings.template_body_placeholder') }}"
                                style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; resize: vertical;"></textarea>
                            <p style="font-size: 12px; color: #6b7280; margin-top: 6px;">
                                {{ __('settings.available_variables') }}:
                                <code style="background: #e0f2fe; padding: 2px 6px; border-radius: 4px; font-size: 11px;">@{{clinic_name}}</code>
                                <code style="background: #e0f2fe; padding: 2px 6px; border-radius: 4px; font-size: 11px;">@{{booking_link}}</code>
                                <code style="background: #e0f2fe; padding: 2px 6px; border-radius: 4px; font-size: 11px;">@{{business_hours}}</code>
                                <code style="background: #e0f2fe; padding: 2px 6px; border-radius: 4px; font-size: 11px;">@{{caller_phone}}</code>
                            </p>
                        </div>
                        <div style="display: flex; gap: 8px;">
                            <button type="submit" style="background: #0ea5e9; color: #fff; padding: 10px 20px; border-radius: 8px; font-size: 14px; font-weight: 500; border: none; cursor: pointer;">
                                {{ __('settings.create_template') }}
                            </button>
                            <button type="button" @click="showNewForm = false" style="background: #fff; border: 1px solid #d1d5db; padding: 10px 20px; border-radius: 8px; font-size: 14px; color: #374151; cursor: pointer;">
                                {{ __('common.cancel') }}
                            </button>
                        </div>
                    </form>
                </div>

                <div>
                    @forelse($templates as $template)
                        <div style="padding: 16px 20px; border-bottom: 1px solid #f3f4f6;" x-data="{ editing: false, confirmDelete: false }">
                            <div x-show="!editing && !confirmDelete" style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 12px;">
                                <div style="flex: 1; min-width: 200px;">
                                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px; flex-wrap: wrap;">
                                        <span style="font-weight: 500; color: #111827;">{{ $template->name }}</span>
                                        @if($template->is_active)
                                            <span style="background: #d1fae5; color: #065f46; font-size: 11px; padding: 2px 8px; border-radius: 9999px; font-weight: 500;">{{ __('settings.template_active') }}</span>
                                        @else
                                            <span style="background: #f3f4f6; color: #6b7280; font-size: 11px; padding: 2px 8px; border-radius: 9999px; font-weight: 500;">{{ __('settings.template_inactive') }}</span>
                                        @endif
                                        <span style="background: #e0f2fe; color: #0369a1; font-size: 10px; padding: 2px 8px; border-radius: 9999px; font-weight: 500; text-transform: uppercase;">
                                            {{ $template->trigger_event }}
                                        </span>
                                    </div>
                                    <p style="font-size: 13px; color: #6b7280; margin: 0; white-space: pre-wrap;">{{ $template->body }}</p>
                                </div>
                                <div style="display: flex; gap: 8px; flex-shrink: 0;">
                                    <button @click="editing = true" style="background: none; border: 1px solid #d1d5db; padding: 6px 12px; border-radius: 6px; font-size: 13px; color: #374151; cursor: pointer;">{{ __('common.edit') }}</button>
                                    <form action="{{ route('settings.toggle.template', $template->id) }}" method="POST" style="margin: 0;">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" style="background: none; border: 1px solid #d1d5db; padding: 6px 12px; border-radius: 6px; font-size: 13px; color: #374151; cursor: pointer;">
                                            {{ $template->is_active ? __('settings.deactivate') : __('settings.activate') }}
                                        </button>
                                    </form>
                                    <button @click="confirmDelete = true" style="background: none; border: 1px solid #fecaca; padding: 6px 12px; border-radius: 6px; font-size: 13px; color: #dc2626; cursor: pointer;">
                                        {{ __('common.delete') }}
                                    </button>
                                </div>
                            </div>

                            <!-- Delete Confirmation -->
                            <div x-show="confirmDelete" x-transition style="background: #fef2f2; padding: 16px; border-radius: 8px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
                                <p style="margin: 0; font-size: 14px; color: #991b1b;">{{ __('settings.delete_template_confirm') }}</p>
                                <div style="display: flex; gap: 8px;">
                                    <button @click="confirmDelete = false" style="background: #fff; border: 1px solid #d1d5db; padding: 8px 16px; border-radius: 6px; font-size: 13px; color: #374151; cursor: pointer;">
                                        {{ __('common.cancel') }}
                                    </button>
                                    <form action="{{ route('settings.destroy.template', $template->id) }}" method="POST" style="margin: 0;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="background: #dc2626; color: #fff; padding: 8px 16px; border-radius: 6px; font-size: 13px; font-weight: 500; border: none; cursor: pointer;">
                                            {{ __('settings.confirm_delete') }}
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <!-- Edit Form -->
                            <form x-show="editing" x-transition action="{{ route('settings.update.template', $template->id) }}" method="POST" style="display: none;" x-bind:style="editing ? 'display: block;' : ''">
                                @csrf
                                @method('PUT')
                                <div style="margin-bottom: 12px;">
                                    <label style="display: block; font-size: 13px; font-weight: 500; color: #374151; margin-bottom: 4px;">{{ __('settings.template_name') }}</label>
                                    <input type="text" name="name" value="{{ $template->name }}" required
                                        style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
                                </div>
                                <div style="margin-bottom: 12px;">
                                    <label style="display: block; font-size: 13px; font-weight: 500; color: #374151; margin-bottom: 4px;">{{ __('settings.template_body') }}</label>
                                    <textarea name="body" rows="3" required
                                        style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; resize: vertical;">{{ $template->body }}</textarea>
                                    <p style="font-size: 12px; color: #9ca3af; margin-top: 4px;">
                                        {{ __('settings.available_variables') }}:
                                        <code style="background: #f3f4f6; padding: 2px 4px; border-radius: 4px;">@{{clinic_name}}</code>
                                        <code style="background: #f3f4f6; padding: 2px 4px; border-radius: 4px;">@{{booking_link}}</code>
                                        <code style="background: #f3f4f6; padding: 2px 4px; border-radius: 4px;">@{{business_hours}}</code>
                                        <code style="background: #f3f4f6; padding: 2px 4px; border-radius: 4px;">@{{caller_phone}}</code>
                                    </p>
                                </div>
                                <div style="display: flex; gap: 8px;">
                                    <button type="submit" style="background: #111827; color: #fff; padding: 8px 16px; border-radius: 6px; font-size: 13px; font-weight: 500; border: none; cursor: pointer;">{{ __('common.save') }}</button>
                                    <button type="button" @click="editing = false" style="background: none; border: 1px solid #d1d5db; padding: 8px 16px; border-radius: 6px; font-size: 13px; color: #374151; cursor: pointer;">{{ __('common.cancel') }}</button>
                                </div>
                            </form>
                        </div>
                    @empty
                        <div style="padding: 48px 20px; text-align: center;">
                            <div style="width: 48px; height: 48px; background: #f3f4f6; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px;">
                                <svg width="24" height="24" fill="none" stroke="#9ca3af" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                                </svg>
                            </div>
                            <p style="color: #6b7280; font-size: 14px; margin: 0 0 16px 0;">{{ __('settings.no_templates') }}</p>
                            <button @click="showNewForm = true" style="background: #0ea5e9; color: #fff; padding: 10px 20px; border-radius: 8px; font-size: 14px; font-weight: 500; border: none; cursor: pointer;">
                                {{ __('settings.create_first_template') }}
                            </button>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Subscription Settings -->
            <div style="background: #fff; border-radius: 12px; border: 1px solid #e5e7eb; margin-bottom: 24px;">
                <div style="padding: 16px 20px; border-bottom: 1px solid #f3f4f6;">
                    <h3 style="font-weight: 600; color: #111827; margin: 0;">{{ __('settings.subscription') }}</h3>
                </div>
                <div style="padding: 20px;">
                    @if($subscription)
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 16px;">
                            <div>
                                <p style="font-size: 14px; color: #6b7280; margin: 0 0 4px 0;">{{ __('subscription.current_plan') }}</p>
                                <p style="font-size: 20px; font-weight: 600; color: #111827; margin: 0;">
                                    {{ $currentPlan->name ?? 'Unknown Plan' }}
                                </p>
                                <div style="margin-top: 8px;">
                                    @if($subscription->onTrial())
                                        <span style="display: inline-flex; align-items: center; background: #dbeafe; color: #1e40af; font-size: 12px; padding: 4px 10px; border-radius: 9999px; font-weight: 500;">
                                            {{ __('subscription.status_trial') }}
                                        </span>
                                        <span style="font-size: 13px; color: #6b7280; margin-left: 8px;">
                                            {{ __('subscription.trial_ends', ['date' => $subscription->trial_ends_at->format('M d, Y')]) }}
                                        </span>
                                    @elseif($subscription->onGracePeriod())
                                        <span style="display: inline-flex; align-items: center; background: #fef3c7; color: #92400e; font-size: 12px; padding: 4px 10px; border-radius: 9999px; font-weight: 500;">
                                            {{ __('subscription.status_cancelling') }}
                                        </span>
                                        <span style="font-size: 13px; color: #6b7280; margin-left: 8px;">
                                            {{ __('subscription.cancels_at', ['date' => $subscription->ends_at->format('M d, Y')]) }}
                                        </span>
                                    @elseif($subscription->isActive())
                                        <span style="display: inline-flex; align-items: center; background: #d1fae5; color: #065f46; font-size: 12px; padding: 4px 10px; border-radius: 9999px; font-weight: 500;">
                                            {{ __('subscription.status_active') }}
                                        </span>
                                        @if($subscription->current_period_end)
                                            <span style="font-size: 13px; color: #6b7280; margin-left: 8px;">
                                                {{ __('subscription.renews_at', ['date' => $subscription->current_period_end->format('M d, Y')]) }}
                                            </span>
                                        @endif
                                    @endif
                                </div>
                            </div>
                            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                <a href="{{ route('subscription.index') }}" style="display: inline-flex; align-items: center; gap: 6px; background: #f3f4f6; color: #374151; padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 500; text-decoration: none;">
                                    {{ __('subscription.manage_billing') }}
                                </a>
                            </div>
                        </div>

                        @if(!$subscription->onGracePeriod())
                            <div style="margin-top: 24px; padding-top: 20px; border-top: 1px solid #f3f4f6;">
                                <h4 style="font-size: 14px; font-weight: 600; color: #991b1b; margin: 0 0 8px 0;">{{ __('settings.danger_zone') }}</h4>
                                <p style="font-size: 13px; color: #6b7280; margin: 0 0 12px 0;">{{ __('subscription.cancel_description') }}</p>
                                <button
                                    type="button"
                                    onclick="document.getElementById('cancel-modal').style.display = 'flex'"
                                    style="background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 500; cursor: pointer;"
                                >
                                    {{ __('subscription.cancel_subscription') }}
                                </button>
                            </div>
                        @else
                            <div style="margin-top: 24px; padding-top: 20px; border-top: 1px solid #f3f4f6;">
                                <p style="font-size: 13px; color: #6b7280; margin: 0 0 12px 0;">{{ __('settings.subscription_cancelled_info') }}</p>
                                <form action="{{ route('subscription.resume') }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" style="background: #10b981; color: #fff; padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 500; border: none; cursor: pointer;">
                                        {{ __('subscription.resume') }}
                                    </button>
                                </form>
                            </div>
                        @endif
                    @else
                        <div style="text-align: center; padding: 20px 0;">
                            <p style="color: #6b7280; margin: 0 0 16px 0;">{{ __('subscription.no_subscription') }}</p>
                            <a href="{{ route('pricing') }}" style="display: inline-flex; align-items: center; gap: 6px; background: #0ea5e9; color: #fff; padding: 10px 20px; border-radius: 8px; font-size: 14px; font-weight: 500; text-decoration: none;">
                                {{ __('subscription.view_plans') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Cancel Subscription Modal -->
            @if($subscription && !$subscription->onGracePeriod())
            <div id="cancel-modal" class="modal-backdrop" style="display: none;" onclick="if(event.target === this) this.style.display = 'none'">
                <div class="modal-content">
                    <div class="modal-icon danger">
                        <svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <h3 class="modal-title">{{ __('settings.cancel_subscription_title') }}</h3>
                    <p class="modal-description">{{ __('settings.cancel_subscription_warning') }}</p>
                    <div class="modal-warning-list">
                        <ul>
                            <li>{{ __('settings.cancel_warning_1') }}</li>
                            <li>{{ __('settings.cancel_warning_2') }}</li>
                            <li>{{ __('settings.cancel_warning_3') }}</li>
                        </ul>
                    </div>
                    <div class="modal-actions">
                        <button type="button" class="btn-cancel-modal" onclick="document.getElementById('cancel-modal').style.display = 'none'">
                            {{ __('settings.keep_subscription') }}
                        </button>
                        <form action="{{ route('subscription.cancel') }}" method="POST" style="flex: 1; margin: 0;">
                            @csrf
                            <button type="submit" class="btn-danger" style="width: 100%;">
                                {{ __('settings.confirm_cancel') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endif

            <!-- Integration Status -->
            <div style="background: #fff; border-radius: 12px; border: 1px solid #e5e7eb;">
                <div style="padding: 16px 20px; border-bottom: 1px solid #f3f4f6;">
                    <h3 style="font-weight: 600; color: #111827; margin: 0;">{{ __('settings.phone_numbers') }}</h3>
                </div>
                <div style="padding: 20px; display: flex; flex-direction: column; gap: 12px;">
                    @php
                        $providers = [
                            ['key' => 'twilio', 'name' => 'Twilio', 'color' => '#ef4444', 'region' => 'USA, Canada, International'],
                            ['key' => 'vonage', 'name' => 'Vonage', 'color' => '#7c3aed', 'region' => 'Europe, Spain, International'],
                            ['key' => 'messagebird', 'name' => 'MessageBird', 'color' => '#2563eb', 'region' => 'Europe, Spain, International'],
                        ];
                    @endphp

                    @foreach($providers as $provider)
                        @php
                            $integration = $integrations[$provider['key']] ?? null;
                            $phones = $activePhones[$provider['key']] ?? collect();
                            $isConnected = $integration && $integration->is_active;
                        @endphp
                        <div style="display: flex; align-items: center; justify-content: space-between; padding: 16px; background: #f9fafb; border-radius: 8px; flex-wrap: wrap; gap: 12px;">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <div style="width: 40px; height: 40px; background: {{ $provider['color'] }}; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    @if($isConnected)
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="white">
                                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                        </svg>
                                    @else
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="white" opacity="0.6">
                                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                                        </svg>
                                    @endif
                                </div>
                                <div>
                                    <p style="font-weight: 500; color: #111827; margin: 0;">{{ $provider['name'] }}</p>
                                    @if($isConnected)
                                        <p style="font-size: 13px; color: #10b981; margin: 0;">{{ app()->getLocale() === 'es' ? 'Conectado' : 'Connected' }}</p>
                                    @else
                                        <p style="font-size: 13px; color: #9ca3af; margin: 0;">{{ $provider['region'] }}</p>
                                    @endif
                                </div>
                            </div>
                            <div style="text-align: right;">
                                @if($phones->count() > 0)
                                    @foreach($phones as $phone)
                                        <p style="font-weight: 500; color: #111827; margin: 0; font-size: 13px;">{{ $phone->phone_number }}</p>
                                    @endforeach
                                @else
                                    <span style="font-size: 12px; color: #9ca3af;">{{ app()->getLocale() === 'es' ? 'Sin número' : 'No number' }}</span>
                                @endif
                            </div>
                        </div>
                    @endforeach

                    <p style="font-size: 13px; color: #6b7280; margin: 8px 0 0 0;">
                        {{ __('settings.add_phone_help') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
