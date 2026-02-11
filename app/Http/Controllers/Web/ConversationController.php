<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\UseCases\Messaging\SendReplyMessage;
use App\UseCases\Leads\UpdateLeadOutcome;
use App\Enums\MessageChannel;
use App\Enums\OutcomeType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ConversationController extends Controller
{
    public function show(Request $request, int $id): View
    {
        $clinicId = $this->getClinicId($request);

        $conversation = Conversation::forClinic($clinicId)
            ->with(['lead.caller', 'lead.outcome', 'messages.sentByUser'])
            ->findOrFail($id);

        return view('inbox.show', compact('conversation'));
    }

    public function reply(Request $request, int $id, SendReplyMessage $sendReplyMessage): JsonResponse|RedirectResponse
    {
        $request->validate([
            'body' => 'required|string|max:1600',
            'channel' => 'nullable|string|in:sms,whatsapp',
        ]);

        $clinicId = $this->getClinicId($request);

        $conversation = Conversation::forClinic($clinicId)->findOrFail($id);

        $channel = $request->input('channel')
            ? MessageChannel::from($request->input('channel'))
            : null;

        $message = $sendReplyMessage->execute(
            conversationId: $conversation->id,
            body: $request->input('body'),
            sentByUserId: $request->user()->id,
            channel: $channel,
        );

        // Return JSON for AJAX requests
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => [
                    'id' => $message->id,
                    'body' => $message->body,
                    'channel' => $message->channel->value,
                    'direction' => $message->direction->value,
                    'status' => $message->status,
                    'created_at' => $message->created_at->format('M d, g:i A'),
                    'sent_by' => $message->sentByUser?->name ?? __('inbox.auto'),
                ],
            ]);
        }

        return redirect()->route('conversations.show', $id)
            ->with('success', 'Message sent');
    }

    public function messages(Request $request, int $id): JsonResponse
    {
        $clinicId = $this->getClinicId($request);

        $conversation = Conversation::forClinic($clinicId)
            ->with(['messages.sentByUser'])
            ->findOrFail($id);

        // Get messages after a certain ID if provided (for polling)
        $afterId = $request->query('after_id', 0);

        $messages = $conversation->messages
            ->where('id', '>', $afterId)
            ->map(fn ($m) => [
                'id' => $m->id,
                'body' => $m->body,
                'channel' => $m->channel->value,
                'direction' => $m->direction->value,
                'status' => $m->status,
                'created_at' => $m->created_at->format('M d, g:i A'),
                'sent_by' => $m->sentByUser?->name ?? ($m->isOutbound() ? __('inbox.auto') : null),
                'is_outbound' => $m->isOutbound(),
            ])
            ->values();

        return response()->json([
            'messages' => $messages,
            'last_id' => $conversation->messages->max('id') ?? 0,
        ]);
    }

    public function outcome(Request $request, int $id, UpdateLeadOutcome $updateLeadOutcome): RedirectResponse
    {
        $request->validate([
            'outcome_type' => 'required|string',
            'notes' => 'nullable|string|max:1000',
            'actual_value' => 'nullable|numeric|min:0',
        ]);

        $clinicId = $this->getClinicId($request);

        $conversation = Conversation::forClinic($clinicId)
            ->with('lead')
            ->findOrFail($id);

        $updateLeadOutcome->execute(
            leadId: $conversation->lead->id,
            outcomeType: OutcomeType::from($request->input('outcome_type')),
            resolvedByUserId: $request->user()->id,
            notes: $request->input('notes'),
            actualValue: $request->input('actual_value'),
        );

        return redirect()->route('inbox.index')
            ->with('success', 'Outcome saved');
    }

    private function getClinicId(Request $request): int
    {
        return $request->user()->clinics()->first()?->id ?? 0;
    }
}
