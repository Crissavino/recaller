<?php

namespace App\UseCases\Leads;

use App\Enums\LeadStage;
use App\Enums\OutcomeType;
use App\Models\Lead;
use App\Models\MissedCallOutcome;
use Illuminate\Support\Facades\DB;

class UpdateLeadOutcome
{
    public function execute(
        int $leadId,
        OutcomeType $outcomeType,
        int $resolvedByUserId,
        ?string $notes = null,
        ?float $actualValue = null,
    ): MissedCallOutcome {
        return DB::transaction(function () use ($leadId, $outcomeType, $resolvedByUserId, $notes, $actualValue) {
            $lead = Lead::findOrFail($leadId);

            $outcome = MissedCallOutcome::updateOrCreate(
                ['lead_id' => $leadId],
                [
                    'clinic_id' => $lead->clinic_id,
                    'outcome_type' => $outcomeType,
                    'notes' => $notes,
                    'actual_value' => $actualValue,
                    'resolved_by_user_id' => $resolvedByUserId,
                    'resolved_at' => now(),
                ]
            );

            $newStage = $this->determineLeadStage($outcomeType);
            $lead->transitionTo($newStage);

            if ($lead->conversation) {
                $lead->conversation->update(['is_active' => false]);
            }

            return $outcome;
        });
    }

    private function determineLeadStage(OutcomeType $outcomeType): LeadStage
    {
        return match ($outcomeType) {
            OutcomeType::BOOKED => LeadStage::BOOKED,
            default => LeadStage::LOST,
        };
    }
}
