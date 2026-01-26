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
                        <span style="font-size: 12px; color: #6b7280;">{{ __('inbox.via') }} {{ $conversation->channel->label() }}</span>
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
                <div style="padding: 20px; display: flex; flex-direction: column; gap: 16px; max-height: 400px; overflow-y: auto;">
                    @forelse($conversation->messages->sortBy('created_at') as $message)
                        <div style="display: flex; {{ $message->isOutbound() ? 'justify-content: flex-end;' : 'justify-content: flex-start;' }}">
                            <div style="max-width: 85%;">
                                @if($message->isOutbound())
                                    <div style="background: #0ea5e9; color: #fff; padding: 10px 16px; border-radius: 18px 18px 4px 18px;">
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
                                    <div style="background: #f3f4f6; color: #111827; padding: 10px 16px; border-radius: 18px 18px 18px 4px;">
                                        <p style="font-size: 14px; margin: 0; line-height: 1.4;">{{ $message->body }}</p>
                                    </div>
                                    <p style="font-size: 11px; color: #9ca3af; margin-top: 4px; padding-left: 4px;">
                                        {{ $message->created_at->format('M d, g:i A') }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div style="text-align: center; padding: 32px 0;">
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
                        <form action="{{ route('conversations.reply', $conversation->id) }}" method="POST">
                            @csrf
                            <div style="display: flex; gap: 12px;">
                                <input type="text"
                                    name="body"
                                    placeholder="{{ __('inbox.type_message') }}"
                                    required
                                    style="flex: 1; border: 1px solid #e5e7eb; border-radius: 24px; padding: 10px 16px; font-size: 14px; outline: none;">
                                <button type="submit" style="width: 40px; height: 40px; background: #0ea5e9; color: #fff; border: none; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                    </svg>
                                </button>
                            </div>
                            @error('body')
                                <p style="color: #ef4444; font-size: 12px; margin-top: 8px; padding-left: 16px;">{{ $message }}</p>
                            @enderror
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
</x-app-layout>
