<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('inbox.title') }}
            </h2>
            <span class="text-sm text-gray-500">{{ $conversations->total() }} {{ __('inbox.no_conversations') !== 'Sin conversaciones' ? 'conversations' : 'conversaciones' }}</span>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                @if($conversations->isEmpty())
                    <div class="p-12 text-center">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-1">{{ __('inbox.no_conversations') }}</h3>
                        <p class="text-gray-500">{{ __('inbox.no_conversations_text') }}</p>
                    </div>
                @else
                    <ul class="divide-y divide-gray-100">
                        @foreach($conversations as $conversation)
                            @php
                                $needsReply = !$conversation->last_staff_reply_at ||
                                    ($conversation->last_message_at && $conversation->last_message_at > $conversation->last_staff_reply_at);
                            @endphp
                            <li>
                                <a href="{{ route('conversations.show', $conversation->id) }}" class="block hover:bg-gray-50 transition-colors">
                                    <div class="px-5 py-4">
                                        <div class="flex items-center gap-4">
                                            <!-- Avatar -->
                                            <div class="flex-shrink-0">
                                                <div class="w-10 h-10 {{ $needsReply ? 'bg-sky-100' : 'bg-gray-100' }} rounded-full flex items-center justify-center">
                                                    <span class="text-sm font-medium {{ $needsReply ? 'text-sky-600' : 'text-gray-500' }}">
                                                        {{ substr($conversation->lead->caller->phone, -2) }}
                                                    </span>
                                                </div>
                                            </div>

                                            <!-- Content -->
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-2">
                                                    @if($needsReply)
                                                        <span class="w-2 h-2 bg-sky-500 rounded-full"></span>
                                                    @endif
                                                    <p class="text-sm font-medium text-gray-900">
                                                        {{ $conversation->lead->caller->phone }}
                                                    </p>
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                                        @if($conversation->lead->stage->value === 'new') bg-gray-100 text-gray-600
                                                        @elseif($conversation->lead->stage->value === 'contacted') bg-sky-50 text-sky-700
                                                        @elseif($conversation->lead->stage->value === 'responded') bg-yellow-50 text-yellow-700
                                                        @elseif($conversation->lead->stage->value === 'booked') bg-emerald-50 text-emerald-700
                                                        @else bg-red-50 text-red-700
                                                        @endif">
                                                        {{ __('stages.' . $conversation->lead->stage->value) }}
                                                    </span>
                                                </div>
                                                @if($conversation->messages->first())
                                                    <p class="mt-1 text-sm text-gray-500 truncate">
                                                        {{ Str::limit($conversation->messages->first()->body, 60) }}
                                                    </p>
                                                @endif
                                            </div>

                                            <!-- Time -->
                                            <div class="flex-shrink-0 text-right hidden sm:block">
                                                <p class="text-xs text-gray-500">
                                                    {{ $conversation->last_message_at?->diffForHumans(short: true) ?? '-' }}
                                                </p>
                                            </div>

                                            <!-- Arrow -->
                                            <div class="flex-shrink-0">
                                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </li>
                        @endforeach
                    </ul>

                    @if($conversations->hasPages())
                        <div class="px-5 py-4 border-t border-gray-100">
                            {{ $conversations->links() }}
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
