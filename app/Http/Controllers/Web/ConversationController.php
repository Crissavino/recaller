<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\UseCases\Messaging\SendReplyMessage;
use App\UseCases\Leads\UpdateLeadOutcome;
use App\Enums\OutcomeType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ConversationController extends Controller
{
    public function show(Request $request, int $id): View
    {
        $clinicId = $this->getClinicId($request);

        $conversation = Conversation::forClinic($clinicId)
            ->with(['lead.caller', 'lead.outcome', 'messages'])
            ->findOrFail($id);

        return view('inbox.show', compact('conversation'));
    }

    public function reply(Request $request, int $id, SendReplyMessage $sendReplyMessage): RedirectResponse
    {
        $request->validate([
            'body' => 'required|string|max:1600',
        ]);

        $clinicId = $this->getClinicId($request);

        $conversation = Conversation::forClinic($clinicId)->findOrFail($id);

        $sendReplyMessage->execute(
            conversationId: $conversation->id,
            body: $request->input('body'),
            sentByUserId: $request->user()->id,
        );

        return redirect()->route('conversations.show', $id)
            ->with('success', 'Message sent');
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
