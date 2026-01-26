<?php

namespace App\UseCases\Messaging;

use App\Enums\LeadStage;
use App\Enums\MessageChannel;
use App\Enums\MessageDirection;
use App\Models\Lead;
use App\Models\Message;
use App\Models\MessageTemplate;
use App\Services\SmsProviderFactory;
use Illuminate\Support\Facades\Log;

class SendFollowUpSms
{
    public function __construct(
        private RenderMessageTemplate $renderMessageTemplate,
        private SmsProviderFactory $smsProviderFactory,
    ) {}

    public function execute(int $leadId): ?Message
    {
        $lead = Lead::with(['clinic.settings', 'conversation', 'caller', 'missedCall.clinicPhoneNumber'])->find($leadId);

        if (!$lead || !$lead->conversation) {
            Log::warning('SendFollowUpSms: Lead or conversation not found', ['lead_id' => $leadId]);
            return null;
        }

        if ($lead->stage !== LeadStage::NEW) {
            Log::info('SendFollowUpSms: Lead no longer in NEW stage, skipping', ['lead_id' => $leadId]);
            return null;
        }

        $template = $this->getActiveTemplate($lead->clinic_id);

        if (!$template) {
            Log::warning('SendFollowUpSms: No active template found', ['clinic_id' => $lead->clinic_id]);
            return null;
        }

        $messageBody = $this->renderMessageTemplate->execute($template, $lead->clinic, $lead->caller->phone);
        $clinicPhoneNumber = $lead->missedCall?->clinicPhoneNumber;

        if (!$clinicPhoneNumber) {
            Log::warning('SendFollowUpSms: No clinic phone number found', ['lead_id' => $leadId]);
            return null;
        }

        $message = Message::create([
            'clinic_id' => $lead->clinic_id,
            'conversation_id' => $lead->conversation->id,
            'channel' => MessageChannel::SMS,
            'direction' => MessageDirection::OUTBOUND,
            'from_phone' => $clinicPhoneNumber->phone_number,
            'to_phone' => $lead->caller->phone,
            'body' => $messageBody,
            'status' => 'pending',
        ]);

        // Use the correct provider based on the phone number's provider
        $provider = $this->smsProviderFactory->forPhoneNumber($clinicPhoneNumber);
        $provider->sendSms($message);

        $lead->transitionTo(LeadStage::CONTACTED);
        $lead->conversation->updateLastMessageTimestamp();

        return $message;
    }

    private function getActiveTemplate(int $clinicId): ?MessageTemplate
    {
        return MessageTemplate::where('clinic_id', $clinicId)
            ->where('channel', MessageChannel::SMS)
            ->where('trigger_event', 'missed_call')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->first();
    }
}
