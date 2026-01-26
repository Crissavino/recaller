<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Enums\LeadStage;
use App\Enums\OutcomeType;
use App\Models\Lead;
use App\Models\MissedCall;
use App\Models\MissedCallOutcome;
use App\Models\Message;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportsController extends Controller
{
    public function index(Request $request): View
    {
        $clinic = $request->user()->clinics()->first();
        $clinicId = $clinic?->id ?? 0;

        $period = $request->get('period', '30'); // days
        $startDate = now()->subDays((int) $period)->startOfDay();

        $stats = $this->calculatePeriodStats($clinicId, $startDate);
        $leadsByStage = $this->getLeadsByStage($clinicId, $startDate);
        $revenueByDay = $this->getRevenueByDay($clinicId, $startDate);
        $outcomeBreakdown = $this->getOutcomeBreakdown($clinicId, $startDate);

        return view('reports.index', compact(
            'clinic',
            'period',
            'stats',
            'leadsByStage',
            'revenueByDay',
            'outcomeBreakdown'
        ));
    }

    private function calculatePeriodStats(int $clinicId, Carbon $startDate): array
    {
        $missedCalls = MissedCall::forClinic($clinicId)
            ->where('called_at', '>=', $startDate)
            ->count();

        $leadsCreated = Lead::forClinic($clinicId)
            ->where('created_at', '>=', $startDate)
            ->count();

        $booked = MissedCallOutcome::forClinic($clinicId)
            ->where('outcome_type', OutcomeType::BOOKED)
            ->where('resolved_at', '>=', $startDate)
            ->count();

        $revenue = MissedCallOutcome::forClinic($clinicId)
            ->where('outcome_type', OutcomeType::BOOKED)
            ->where('resolved_at', '>=', $startDate)
            ->sum('actual_value');

        $conversionRate = $missedCalls > 0 ? round(($booked / $missedCalls) * 100, 1) : 0;

        $messagesSent = Message::forClinic($clinicId)
            ->where('direction', 'outbound')
            ->where('created_at', '>=', $startDate)
            ->count();

        $messagesReceived = Message::forClinic($clinicId)
            ->where('direction', 'inbound')
            ->where('created_at', '>=', $startDate)
            ->count();

        $responseRate = $messagesSent > 0 ? round(($messagesReceived / $messagesSent) * 100, 1) : 0;

        return [
            'missed_calls' => $missedCalls,
            'leads_created' => $leadsCreated,
            'booked' => $booked,
            'revenue' => $revenue,
            'conversion_rate' => $conversionRate,
            'messages_sent' => $messagesSent,
            'messages_received' => $messagesReceived,
            'response_rate' => $responseRate,
        ];
    }

    private function getLeadsByStage(int $clinicId, Carbon $startDate): array
    {
        $stages = Lead::forClinic($clinicId)
            ->where('created_at', '>=', $startDate)
            ->selectRaw('stage, COUNT(*) as count')
            ->groupBy('stage')
            ->pluck('count', 'stage')
            ->toArray();

        return [
            'new' => $stages[LeadStage::NEW->value] ?? 0,
            'contacted' => $stages[LeadStage::CONTACTED->value] ?? 0,
            'responded' => $stages[LeadStage::RESPONDED->value] ?? 0,
            'booked' => $stages[LeadStage::BOOKED->value] ?? 0,
            'lost' => $stages[LeadStage::LOST->value] ?? 0,
        ];
    }

    private function getRevenueByDay(int $clinicId, Carbon $startDate): array
    {
        $results = MissedCallOutcome::forClinic($clinicId)
            ->where('outcome_type', OutcomeType::BOOKED)
            ->where('resolved_at', '>=', $startDate)
            ->selectRaw('DATE(resolved_at) as date, SUM(actual_value) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $days = [];
        $current = $startDate->copy();
        $maxRevenue = 0;

        while ($current <= now()) {
            $dateKey = $current->format('Y-m-d');
            $revenue = $results->get($dateKey)?->total ?? 0;
            $maxRevenue = max($maxRevenue, $revenue);

            $days[] = [
                'date' => $current->format('M d'),
                'revenue' => $revenue,
            ];

            $current->addDay();
        }

        // Add percentage for chart
        foreach ($days as &$day) {
            $day['percentage'] = $maxRevenue > 0 ? round(($day['revenue'] / $maxRevenue) * 100) : 0;
        }

        return $days;
    }

    private function getOutcomeBreakdown(int $clinicId, Carbon $startDate): array
    {
        $outcomes = MissedCallOutcome::forClinic($clinicId)
            ->where('resolved_at', '>=', $startDate)
            ->selectRaw('outcome_type, COUNT(*) as count')
            ->groupBy('outcome_type')
            ->pluck('count', 'outcome_type')
            ->toArray();

        $total = array_sum($outcomes);

        return [
            'booked' => [
                'count' => $outcomes[OutcomeType::BOOKED->value] ?? 0,
                'percentage' => $total > 0 ? round((($outcomes[OutcomeType::BOOKED->value] ?? 0) / $total) * 100) : 0,
            ],
            'callback_requested' => [
                'count' => $outcomes[OutcomeType::CALLBACK_REQUESTED->value] ?? 0,
                'percentage' => $total > 0 ? round((($outcomes[OutcomeType::CALLBACK_REQUESTED->value] ?? 0) / $total) * 100) : 0,
            ],
            'not_interested' => [
                'count' => $outcomes[OutcomeType::NOT_INTERESTED->value] ?? 0,
                'percentage' => $total > 0 ? round((($outcomes[OutcomeType::NOT_INTERESTED->value] ?? 0) / $total) * 100) : 0,
            ],
            'wrong_number' => [
                'count' => $outcomes[OutcomeType::WRONG_NUMBER->value] ?? 0,
                'percentage' => $total > 0 ? round((($outcomes[OutcomeType::WRONG_NUMBER->value] ?? 0) / $total) * 100) : 0,
            ],
            'no_response' => [
                'count' => $outcomes[OutcomeType::NO_RESPONSE->value] ?? 0,
                'percentage' => $total > 0 ? round((($outcomes[OutcomeType::NO_RESPONSE->value] ?? 0) / $total) * 100) : 0,
            ],
            'total' => $total,
        ];
    }
}
