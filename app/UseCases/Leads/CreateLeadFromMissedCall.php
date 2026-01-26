<?php

namespace App\UseCases\Leads;

use App\Enums\LeadOrigin;
use App\Enums\LeadStage;
use App\Enums\MessageChannel;
use App\Jobs\SendScheduledFollowUp;
use App\Models\Caller;
use App\Models\Clinic;
use App\Models\Conversation;
use App\Models\Lead;
use App\Models\MissedCall;
use Illuminate\Support\Facades\DB;

class CreateLeadFromMissedCall
{
    public function execute(
        int $clinicId,
        int $clinicPhoneNumberId,
        string $callerPhone,
        ?string $providerCallId = null,
        ?int $ringDurationSeconds = null,
    ): Lead {
        return DB::transaction(function () use ($clinicId, $clinicPhoneNumberId, $callerPhone, $providerCallId, $ringDurationSeconds) {
            $clinic = Clinic::findOrFail($clinicId);
            $caller = $this->findOrCreateCaller($clinicId, $callerPhone);
            $estimatedValue = $clinic->settings?->avg_ticket_value;

            $lead = Lead::create([
                'clinic_id' => $clinicId,
                'caller_id' => $caller->id,
                'origin' => LeadOrigin::MISSED_CALL,
                'stage' => LeadStage::NEW,
                'estimated_value' => $estimatedValue,
            ]);

            MissedCall::create([
                'clinic_id' => $clinicId,
                'lead_id' => $lead->id,
                'clinic_phone_number_id' => $clinicPhoneNumberId,
                'caller_phone' => $callerPhone,
                'provider_call_id' => $providerCallId,
                'ring_duration_seconds' => $ringDurationSeconds,
                'called_at' => now(),
            ]);

            Conversation::create([
                'clinic_id' => $clinicId,
                'lead_id' => $lead->id,
                'channel' => MessageChannel::SMS,
                'is_active' => true,
            ]);

            $this->scheduleFollowUp($lead, $clinic);

            return $lead;
        });
    }

    private function findOrCreateCaller(int $clinicId, string $phone): Caller
    {
        return Caller::firstOrCreate(
            ['clinic_id' => $clinicId, 'phone' => $phone],
            ['clinic_id' => $clinicId, 'phone' => $phone]
        );
    }

    private function scheduleFollowUp(Lead $lead, Clinic $clinic): void
    {
        $delaySeconds = $clinic->settings?->followup_delay_seconds ?? 60;

        SendScheduledFollowUp::dispatch($lead->id)
            ->delay(now()->addSeconds($delaySeconds));
    }
}
