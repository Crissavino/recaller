<?php

namespace App\UseCases\Messaging;

use App\Enums\LeadStage;
use App\Enums\MessageChannel;
use App\Enums\MessageDirection;
use App\Models\ClinicPhoneNumber;
use App\Models\Lead;
use App\Models\Message;
use App\Models\MessageTemplate;
use App\Services\SmsProviderFactory;
use App\Services\TwilioService;
use Illuminate\Support\Facades\Log;

class SendFollowUpMessage
{
    public function __construct(
        private RenderMessageTemplate $renderMessageTemplate,
        private SmsProviderFactory $smsProviderFactory,
        private TwilioService $twilioService,
    ) {}

    public function execute(int $leadId): ?Message
    {
        $lead = Lead::with(['clinic.settings', 'conversation', 'caller', 'missedCall.clinicPhoneNumber'])->find($leadId);

        if (!$lead || !$lead->conversation) {
            Log::warning('SendFollowUpMessage: Lead or conversation not found', ['lead_id' => $leadId]);
            return null;
        }

        if ($lead->stage !== LeadStage::NEW) {
            Log::info('SendFollowUpMessage: Lead no longer in NEW stage, skipping', ['lead_id' => $leadId]);
            return null;
        }

        // Determine the best channel - prefer WhatsApp if available
        $channel = $this->determineChannel($lead);

        $template = $this->getActiveTemplate($lead->clinic_id, $channel);

        if (!$template) {
            // Fall back to SMS if no WhatsApp template
            if ($channel === MessageChannel::WHATSAPP) {
                $channel = MessageChannel::SMS;
                $template = $this->getActiveTemplate($lead->clinic_id, $channel);
            }

            if (!$template) {
                // As last resort, use any active template
                $template = MessageTemplate::where('clinic_id', $lead->clinic_id)
                    ->where('trigger_event', 'missed_call')
                    ->where('is_active', true)
                    ->orderBy('sort_order')
                    ->first();
            }

            if (!$template) {
                Log::warning('SendFollowUpMessage: No active template found', ['clinic_id' => $lead->clinic_id]);
                return null;
            }
        }

        $messageBody = $this->renderMessageTemplate->execute($template, $lead->clinic, $lead->caller->phone);

        // Get the appropriate from phone based on channel
        $fromPhone = $this->getFromPhone($lead, $channel);

        if (!$fromPhone) {
            Log::warning('SendFollowUpMessage: No from phone found', ['lead_id' => $leadId, 'channel' => $channel->value]);
            return null;
        }

        $message = Message::create([
            'clinic_id' => $lead->clinic_id,
            'conversation_id' => $lead->conversation->id,
            'channel' => $channel,
            'direction' => MessageDirection::OUTBOUND,
            'from_phone' => $fromPhone,
            'to_phone' => $lead->caller->phone,
            'body' => $messageBody,
            'status' => 'pending',
        ]);

        // Send via appropriate channel
        $this->sendMessage($message, $channel, $lead, $template);

        // Update conversation channel
        $lead->conversation->update(['channel' => $channel]);

        $lead->transitionTo(LeadStage::CONTACTED);
        $lead->conversation->updateLastMessageTimestamp();

        Log::info('SendFollowUpMessage: Message sent', [
            'lead_id' => $leadId,
            'channel' => $channel->value,
            'message_id' => $message->id,
        ]);

        return $message;
    }

    private function determineChannel(Lead $lead): MessageChannel
    {
        // First check if the voice number has a linked WhatsApp number
        $voiceNumber = $lead->missedCall?->clinicPhoneNumber;
        if ($voiceNumber) {
            $whatsAppNumber = $voiceNumber->getWhatsAppNumber();
            if ($whatsAppNumber) {
                return MessageChannel::WHATSAPP;
            }
        }

        // Fall back to checking if clinic has any WhatsApp number
        $hasWhatsApp = ClinicPhoneNumber::getWhatsAppForClinic($lead->clinic_id) !== null;

        return $hasWhatsApp ? MessageChannel::WHATSAPP : MessageChannel::SMS;
    }

    private function getFromPhone(Lead $lead, MessageChannel $channel): ?string
    {
        if ($channel === MessageChannel::WHATSAPP) {
            // First try linked WhatsApp number from the voice number
            $voiceNumber = $lead->missedCall?->clinicPhoneNumber;
            if ($voiceNumber) {
                $whatsAppNumber = $voiceNumber->getWhatsAppNumber();
                if ($whatsAppNumber) {
                    return $whatsAppNumber->getCleanPhoneNumber();
                }
            }

            // Fall back to clinic's WhatsApp number
            $whatsAppNumber = ClinicPhoneNumber::getWhatsAppForClinic($lead->clinic_id);
            if ($whatsAppNumber) {
                return $whatsAppNumber->getCleanPhoneNumber();
            }
        }

        return $lead->missedCall?->clinicPhoneNumber?->phone_number;
    }

    private function sendMessage(Message $message, MessageChannel $channel, Lead $lead, MessageTemplate $template): void
    {
        if ($channel === MessageChannel::WHATSAPP) {
            $contentVariables = $template->content_sid ? ['1' => $lead->clinic->name] : [];
            $this->twilioService->sendWhatsApp($message, $template->content_sid, $contentVariables);
            return;
        }

        // SMS
        $clinicPhoneNumber = $lead->missedCall?->clinicPhoneNumber;
        if ($clinicPhoneNumber) {
            $provider = $this->smsProviderFactory->forPhoneNumber($clinicPhoneNumber);
            $provider->sendSms($message);
        }
    }

    private function getActiveTemplate(int $clinicId, MessageChannel $channel): ?MessageTemplate
    {
        return MessageTemplate::where('clinic_id', $clinicId)
            ->where('channel', $channel)
            ->where('trigger_event', 'missed_call')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->first();
    }
}
