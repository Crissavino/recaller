<x-app-layout>
    <x-slot name="header">
        <div style="display: flex; align-items: center; gap: 16px;">
            <a href="{{ route('inbox.index') }}" style="color: #9ca3af; text-decoration: none;">
                <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 40px; height: 40px; background: #e0f2fe; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                    <span style="font-size: 14px; font-weight: 500; color: #0284c7;">
                        {{ substr($conversation->lead->caller->phone, -2) }}
                    </span>
                </div>
                <div>
                    <h2 style="font-size: 18px; font-weight: 600; color: #111827; margin: 0;">
                        {{ $conversation->lead->caller->phone }}
                    </h2>
                    <div style="display: flex; align-items: center; gap: 8px; margin-top: 2px; flex-wrap: wrap;">
                        @php
                            $stageColors = [
                                'new' => ['bg' => '#f3f4f6', 'text' => '#4b5563'],
                                'contacted' => ['bg' => '#e0f2fe', 'text' => '#0369a1'],
                                'responded' => ['bg' => '#fef3c7', 'text' => '#b45309'],
                                'booked' => ['bg' => '#d1fae5', 'text' => '#047857'],
                                'lost' => ['bg' => '#fee2e2', 'text' => '#b91c1c'],
                            ];
                            $colors = $stageColors[$conversation->lead->stage->value] ?? $stageColors['new'];
                        @endphp
                        <span style="display: inline-flex; align-items: center; padding: 2px 8px; border-radius: 4px; font-size: 12px; font-weight: 500; background: {{ $colors['bg'] }}; color: {{ $colors['text'] }};">
                            {{ __('stages.' . $conversation->lead->stage->value) }}
                        </span>
                        <span id="channel-indicator" style="font-size: 12px; color: #6b7280;">{{ __('inbox.via') }} <span id="current-channel">{{ $conversation->channel->label() }}</span></span>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div style="padding: 32px 0;">
        <div style="max-width: 768px; margin: 0 auto; padding: 0 16px;">
            @if(session('success'))
                <div style="margin-bottom: 16px; background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; padding: 12px 16px; border-radius: 8px; font-size: 14px;">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Messages -->
            <div style="background: #fff; border-radius: 12px; border: 1px solid #e5e7eb; margin-bottom: 24px;">
                <div id="messages-container" style="padding: 20px; display: flex; flex-direction: column; gap: 16px; max-height: 400px; overflow-y: auto;">
                    @forelse($conversation->messages->sortBy('created_at') as $message)
                        <div class="message-item" data-message-id="{{ $message->id }}" style="display: flex; {{ $message->isOutbound() ? 'justify-content: flex-end;' : 'justify-content: flex-start;' }}">
                            <div style="max-width: 85%;">
                                @if($message->isOutbound())
                                    <div style="background: {{ $message->channel->value === 'whatsapp' ? '#25D366' : '#0ea5e9' }}; color: #fff; padding: 10px 16px; border-radius: 18px 18px 4px 18px; position: relative;">
                                        @if($message->channel->value === 'whatsapp')
                                            <span style="position: absolute; top: -8px; right: -8px; background: #25D366; border-radius: 50%; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; border: 2px solid white;">
                                                <svg style="width: 12px; height: 12px;" fill="white" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                            </span>
                                        @endif
                                        <p style="font-size: 14px; margin: 0; line-height: 1.4;">{{ $message->body }}</p>
                                    </div>
                                    <p style="font-size: 11px; color: #9ca3af; margin-top: 4px; text-align: right; padding-right: 4px;">
                                        {{ $message->created_at->format('M d, g:i A') }}
                                        @if($message->sentByUser)
                                            · {{ $message->sentByUser->name }}
                                        @else
                                            · {{ __('inbox.auto') }}
                                        @endif
                                    </p>
                                @else
                                    <div style="background: #f3f4f6; color: #111827; padding: 10px 16px; border-radius: 18px 18px 18px 4px; position: relative;">
                                        @if($message->channel->value === 'whatsapp')
                                            <span style="position: absolute; top: -8px; left: -8px; background: #25D366; border-radius: 50%; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; border: 2px solid white;">
                                                <svg style="width: 12px; height: 12px;" fill="white" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                            </span>
                                        @endif
                                        <p style="font-size: 14px; margin: 0; line-height: 1.4;">{{ $message->body }}</p>
                                    </div>
                                    <p style="font-size: 11px; color: #9ca3af; margin-top: 4px; padding-left: 4px;">
                                        {{ $message->created_at->format('M d, g:i A') }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div id="empty-messages" style="text-align: center; padding: 32px 0;">
                            <div style="width: 48px; height: 48px; background: #f3f4f6; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px;">
                                <svg style="width: 24px; height: 24px; color: #9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                </svg>
                            </div>
                            <p style="font-size: 14px; color: #6b7280;">{{ __('inbox.no_messages') }}</p>
                        </div>
                    @endforelse
                </div>

                <!-- Reply Form -->
                @if($conversation->is_active && !$conversation->lead->outcome)
                    <div style="border-top: 1px solid #f3f4f6; padding: 16px;">
                        <form id="reply-form" action="{{ route('conversations.reply', $conversation->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="channel" id="channel-input" value="whatsapp">
                            <div style="display: flex; gap: 12px;">
                                <input type="text"
                                    name="body"
                                    id="message-input"
                                    placeholder="{{ __('inbox.type_message') }}"
                                    required
                                    autocomplete="off"
                                    style="flex: 1; border: 1px solid #e5e7eb; border-radius: 24px; padding: 10px 16px; font-size: 14px; outline: none;">
                                <button type="submit" id="send-button" style="width: 40px; height: 40px; background: #25D366; color: #fff; border: none; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    <svg style="width: 20px; height: 20px;" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
                                    </svg>
                                </button>
                            </div>
                            <p id="form-error" style="color: #ef4444; font-size: 12px; margin-top: 8px; padding-left: 16px; display: none;"></p>
                        </form>
                    </div>
                @endif
            </div>

            <!-- Outcome Section -->
            @if(!$conversation->lead->outcome)
                <div style="background: #fff; border-radius: 12px; border: 1px solid #e5e7eb;">
                    <div style="padding: 16px 20px; border-bottom: 1px solid #f3f4f6;">
                        <h3 style="font-weight: 600; color: #111827; margin: 0;">{{ __('inbox.mark_outcome') }}</h3>
                        <p style="font-size: 14px; color: #6b7280; margin: 4px 0 0 0;">{{ __('inbox.outcome_question') }}</p>
                    </div>
                    <form action="{{ route('conversations.outcome', $conversation->id) }}" method="POST" style="padding: 20px;">
                        @csrf
                        <!-- Outcome Buttons -->
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(100px, 1fr)); gap: 8px; margin-bottom: 16px;" x-data="{ selected: '' }">
                            @php
                                $outcomes = [
                                    ['value' => 'booked', 'icon' => '✓', 'label' => __('inbox.outcome_booked'), 'color' => '#10b981'],
                                    ['value' => 'callback_requested', 'icon' => '📞', 'label' => __('inbox.outcome_callback'), 'color' => '#0ea5e9'],
                                    ['value' => 'not_interested', 'icon' => '✕', 'label' => __('inbox.outcome_not_interested'), 'color' => '#ef4444'],
                                    ['value' => 'wrong_number', 'icon' => '🚫', 'label' => __('inbox.outcome_wrong_number'), 'color' => '#6b7280'],
                                    ['value' => 'no_response', 'icon' => '💤', 'label' => __('inbox.outcome_no_response'), 'color' => '#f59e0b'],
                                    ['value' => 'needs_manual_call', 'icon' => '📲', 'label' => __('inbox.outcome_needs_call'), 'color' => '#f97316'],
                                ];
                            @endphp
                            @foreach($outcomes as $outcome)
                                <label style="cursor: pointer;">
                                    <input type="radio" name="outcome_type" value="{{ $outcome['value'] }}" required
                                        x-on:change="selected = '{{ $outcome['value'] }}'"
                                        style="position: absolute; opacity: 0; pointer-events: none;">
                                    <div style="border: 2px solid #e5e7eb; border-radius: 8px; padding: 12px; text-align: center; transition: all 0.15s;"
                                        x-bind:style="selected === '{{ $outcome['value'] }}' ? 'border-color: {{ $outcome['color'] }}; background: {{ $outcome['color'] }}10;' : ''"
                                        onmouseover="this.style.borderColor='#d1d5db'" onmouseout="this.style.borderColor = this.classList.contains('selected') ? '{{ $outcome['color'] }}' : '#e5e7eb'">
                                        <div style="font-size: 18px; margin-bottom: 4px;">{{ $outcome['icon'] }}</div>
                                        <div style="font-size: 12px; font-weight: 500; color: #374151;">{{ $outcome['label'] }}</div>
                                    </div>
                                </label>
                            @endforeach
                        </div>

                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 16px;">
                            <div>
                                <label style="display: block; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 6px;">{{ __('inbox.value_label') }}</label>
                                <div style="position: relative;">
                                    <span style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #9ca3af;">$</span>
                                    <input type="number" name="actual_value" step="0.01" min="0" placeholder="0.00"
                                        style="width: 100%; padding: 10px 12px 10px 28px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 14px;">
                                </div>
                            </div>
                            <div>
                                <label style="display: block; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 6px;">{{ __('inbox.notes_label') }}</label>
                                <input type="text" name="notes" placeholder="{{ __('inbox.notes_placeholder') }}"
                                    style="width: 100%; padding: 10px 12px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 14px;">
                            </div>
                        </div>

                        <button type="submit" style="width: 100%; background: #111827; color: #fff; padding: 12px 16px; border-radius: 8px; font-size: 14px; font-weight: 500; border: none; cursor: pointer;">
                            {{ __('inbox.save_outcome') }}
                        </button>
                    </form>
                </div>
            @else
                <div style="background: #fff; border-radius: 12px; border: 1px solid #e5e7eb;">
                    <div style="padding: 20px;">
                        <div style="display: flex; align-items: flex-start; gap: 12px;">
                            <div style="width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: {{ $conversation->lead->outcome->outcome_type->isPositive() ? '#d1fae5' : '#f3f4f6' }}; flex-shrink: 0;">
                                @if($conversation->lead->outcome->outcome_type->value === 'booked')
                                    <span style="color: #059669;">✓</span>
                                @else
                                    <span style="color: #6b7280;">○</span>
                                @endif
                            </div>
                            <div style="flex: 1; min-width: 0;">
                                <p style="font-weight: 500; color: #111827; margin: 0;">
                                    {{ $conversation->lead->outcome->outcome_type->label() }}
                                    @if($conversation->lead->outcome->actual_value)
                                        <span style="color: #059669; margin-left: 4px;">${{ number_format($conversation->lead->outcome->actual_value, 2) }}</span>
                                    @endif
                                </p>
                                @if($conversation->lead->outcome->notes)
                                    <p style="font-size: 14px; color: #6b7280; margin: 4px 0 0 0;">{{ $conversation->lead->outcome->notes }}</p>
                                @endif
                                <p style="font-size: 12px; color: #9ca3af; margin: 8px 0 0 0;">
                                    {{ __('inbox.by') }} {{ $conversation->lead->outcome->resolvedByUser?->name ?? 'Unknown' }}
                                    {{ __('inbox.on') }} {{ $conversation->lead->outcome->resolved_at?->format('M d, Y') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        // Configuration
        const conversationId = {{ $conversation->id }};
        const messagesUrl = '{{ route("conversations.messages", $conversation->id) }}';
        const replyUrl = '{{ route("conversations.reply", $conversation->id) }}';
        const unreadCountUrl = '{{ route("inbox.unread-count") }}';
        const csrfToken = '{{ csrf_token() }}';
        const currentUserId = {{ auth()->id() }};
        const autoText = '{{ __("inbox.auto") }}';

        let lastMessageId = {{ $conversation->messages->max('id') ?? 0 }};
        let selectedChannel = 'whatsapp';
        let pollingInterval = null;
        let notificationInterval = null;

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Start polling for new messages
            startPolling();

            // Start notification polling
            startNotificationPolling();

            // Scroll to bottom of messages
            scrollToBottom();

            // Handle form submission via AJAX
            document.getElementById('reply-form').addEventListener('submit', handleFormSubmit);
        });

        async function handleFormSubmit(e) {
            e.preventDefault();

            const messageInput = document.getElementById('message-input');
            const sendButton = document.getElementById('send-button');
            const errorEl = document.getElementById('form-error');
            const body = messageInput.value.trim();

            if (!body) return;

            // Disable form while sending
            messageInput.disabled = true;
            sendButton.disabled = true;
            errorEl.style.display = 'none';

            try {
                const response = await fetch(replyUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        body: body,
                        channel: selectedChannel
                    })
                });

                const data = await response.json();

                if (data.success) {
                    // Add message to UI
                    addMessageToUI(data.message, true);
                    messageInput.value = '';
                    lastMessageId = Math.max(lastMessageId, data.message.id);
                } else {
                    throw new Error(data.message || 'Error sending message');
                }
            } catch (error) {
                console.error('Error:', error);
                errorEl.textContent = error.message || 'Error sending message';
                errorEl.style.display = 'block';
            } finally {
                messageInput.disabled = false;
                sendButton.disabled = false;
                messageInput.focus();
            }
        }

        function addMessageToUI(message, isOutbound) {
            const container = document.getElementById('messages-container');
            const emptyMessages = document.getElementById('empty-messages');

            // Remove empty state if present
            if (emptyMessages) {
                emptyMessages.remove();
            }

            const isWhatsApp = message.channel === 'whatsapp';
            const bgColor = isOutbound ? (isWhatsApp ? '#25D366' : '#0ea5e9') : '#f3f4f6';
            const textColor = isOutbound ? '#fff' : '#111827';
            const borderRadius = isOutbound ? '18px 18px 4px 18px' : '18px 18px 18px 4px';
            const badgePosition = isOutbound ? 'right: -8px' : 'left: -8px';

            const whatsappBadge = isWhatsApp ? `
                <span style="position: absolute; top: -8px; ${badgePosition}; background: #25D366; border-radius: 50%; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; border: 2px solid white;">
                    <svg style="width: 12px; height: 12px;" fill="white" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                </span>
            ` : '';

            const sentByText = isOutbound ? ` · ${message.sent_by || autoText}` : '';

            const messageHtml = `
                <div class="message-item" data-message-id="${message.id}" style="display: flex; ${isOutbound ? 'justify-content: flex-end;' : 'justify-content: flex-start;'}">
                    <div style="max-width: 85%;">
                        <div style="background: ${bgColor}; color: ${textColor}; padding: 10px 16px; border-radius: ${borderRadius}; position: relative;">
                            ${whatsappBadge}
                            <p style="font-size: 14px; margin: 0; line-height: 1.4;">${escapeHtml(message.body)}</p>
                        </div>
                        <p style="font-size: 11px; color: #9ca3af; margin-top: 4px; ${isOutbound ? 'text-align: right; padding-right: 4px;' : 'padding-left: 4px;'}">
                            ${message.created_at}${sentByText}
                        </p>
                    </div>
                </div>
            `;

            container.insertAdjacentHTML('beforeend', messageHtml);
            scrollToBottom();
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function scrollToBottom() {
            const container = document.getElementById('messages-container');
            container.scrollTop = container.scrollHeight;
        }

        function startPolling() {
            // Poll every 3 seconds
            pollingInterval = setInterval(pollForNewMessages, 3000);
        }

        async function pollForNewMessages() {
            try {
                const response = await fetch(`${messagesUrl}?after_id=${lastMessageId}`, {
                    headers: {
                        'Accept': 'application/json',
                    }
                });

                const data = await response.json();

                if (data.messages && data.messages.length > 0) {
                    data.messages.forEach(message => {
                        // Only add if not already in DOM
                        if (!document.querySelector(`[data-message-id="${message.id}"]`)) {
                            addMessageToUI(message, message.is_outbound);
                        }
                    });
                    lastMessageId = data.last_id;

                    // Update channel indicator if changed
                    if (data.messages.length > 0) {
                        const lastMessage = data.messages[data.messages.length - 1];
                        const channelLabel = lastMessage.channel === 'whatsapp' ? 'WhatsApp' : 'SMS';
                        document.getElementById('current-channel').textContent = channelLabel;
                    }
                }
            } catch (error) {
                console.error('Polling error:', error);
            }
        }

        function startNotificationPolling() {
            // Poll for notifications every 10 seconds
            notificationInterval = setInterval(updateNotificationBadge, 10000);
        }

        async function updateNotificationBadge() {
            try {
                const response = await fetch(unreadCountUrl, {
                    headers: {
                        'Accept': 'application/json',
                    }
                });

                const data = await response.json();
                const badges = document.querySelectorAll('[data-unread-badge]');

                badges.forEach(badge => {
                    if (data.count > 0) {
                        badge.textContent = data.count > 9 ? '9+' : data.count;
                        badge.style.display = 'inline-flex';
                    } else {
                        badge.style.display = 'none';
                    }
                });
            } catch (error) {
                console.error('Notification polling error:', error);
            }
        }

        // Cleanup on page unload
        window.addEventListener('beforeunload', function() {
            if (pollingInterval) clearInterval(pollingInterval);
            if (notificationInterval) clearInterval(notificationInterval);
        });
    </script>
    @endpush
</x-app-layout>
