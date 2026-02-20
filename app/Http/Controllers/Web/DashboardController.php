<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ClinicPhoneNumber;
use App\Models\Conversation;
use App\Models\Lead;
use App\Models\MissedCall;
use App\Models\MissedCallOutcome;
use App\Models\MessageTemplate;
use App\Enums\LeadStage;
use App\Enums\OutcomeType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $clinic = $request->user()->clinics()->with('settings')->first();
        $clinicId = $clinic?->id ?? 0;

        $stats = $this->calculateStats($clinicId);
        $salesMetrics = $this->calculateSalesMetrics($clinicId);
        $weeklyData = $this->calculateWeeklyRevenue($clinicId);
        $needsAttention = $this->getNeedsAttention($clinicId);
        $recentLeads = $this->getRecentLeads($clinicId);
        $configStatus = $this->checkConfiguration($clinicId);

        return view('dashboard', compact(
            'clinic',
            'stats',
            'salesMetrics',
            'needsAttention',
            'recentLeads',
            'configStatus'
        ))->with($weeklyData);
    }

    private function calculateStats(int $clinicId): array
    {
        $missedCallsCount = MissedCall::forClinic($clinicId)
            ->whereDate('called_at', today())
            ->count();

        $followUpsSentCount = Lead::forClinic($clinicId)
            ->whereIn('stage', [LeadStage::CONTACTED, LeadStage::RESPONDED, LeadStage::BOOKED, LeadStage::LOST])
            ->whereDate('created_at', today())
            ->count();

        $responsesCount = Lead::forClinic($clinicId)
            ->whereIn('stage', [LeadStage::RESPONDED, LeadStage::BOOKED])
            ->whereDate('updated_at', today())
            ->count();

        $bookedCount = MissedCallOutcome::forClinic($clinicId)
            ->where('outcome_type', OutcomeType::BOOKED)
            ->whereDate('resolved_at', today())
            ->count();

        $recoveredRevenue = MissedCallOutcome::forClinic($clinicId)
            ->where('outcome_type', OutcomeType::BOOKED)
            ->whereDate('resolved_at', today())
            ->sum('actual_value');

        return [
            'missed_calls' => $missedCallsCount,
            'followups_sent' => $followUpsSentCount,
            'responses' => $responsesCount,
            'booked' => $bookedCount,
            'recovered_revenue' => $recoveredRevenue,
        ];
    }

    private function calculateSalesMetrics(int $clinicId): array
    {
        // Money at Risk - leads without outcome (potential lost revenue)
        $leadsAtRisk = Lead::forClinic($clinicId)
            ->whereIn('stage', [LeadStage::NEW, LeadStage::CONTACTED, LeadStage::RESPONDED])
            ->doesntHave('outcome')
            ->count();

        // Estimated value at risk (using average booking value from clinic's history)
        $avgBookingValue = MissedCallOutcome::forClinic($clinicId)
            ->where('outcome_type', OutcomeType::BOOKED)
            ->whereNotNull('actual_value')
            ->avg('actual_value') ?? 150; // Default $150 if no history

        $moneyAtRisk = $leadsAtRisk * $avgBookingValue;

        // Conversion Rate (all time)
        $totalMissedCalls = MissedCall::forClinic($clinicId)->count();
        $totalBooked = MissedCallOutcome::forClinic($clinicId)
            ->where('outcome_type', OutcomeType::BOOKED)
            ->count();

        $conversionRate = $totalMissedCalls > 0
            ? round(($totalBooked / $totalMissedCalls) * 100, 1)
            : 0;

        // Average Response Time (conversations with replies)
        $avgResponseMinutes = Conversation::forClinic($clinicId)
            ->whereNotNull('last_staff_reply_at')
            ->whereNotNull('created_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, created_at, last_staff_reply_at)) as avg_minutes')
            ->value('avg_minutes') ?? 0;

        // Monthly comparison
        $thisMonth = MissedCallOutcome::forClinic($clinicId)
            ->where('outcome_type', OutcomeType::BOOKED)
            ->whereMonth('resolved_at', now()->month)
            ->whereYear('resolved_at', now()->year)
            ->sum('actual_value');

        $lastMonth = MissedCallOutcome::forClinic($clinicId)
            ->where('outcome_type', OutcomeType::BOOKED)
            ->whereMonth('resolved_at', now()->subMonth()->month)
            ->whereYear('resolved_at', now()->subMonth()->year)
            ->sum('actual_value');

        $monthlyGrowth = $lastMonth > 0
            ? round((($thisMonth - $lastMonth) / $lastMonth) * 100, 0)
            : ($thisMonth > 0 ? 100 : 0);

        // Total recovered all time
        $totalRecovered = MissedCallOutcome::forClinic($clinicId)
            ->where('outcome_type', OutcomeType::BOOKED)
            ->sum('actual_value');

        return [
            'leads_at_risk' => $leadsAtRisk,
            'money_at_risk' => $moneyAtRisk,
            'conversion_rate' => $conversionRate,
            'avg_response_minutes' => round($avgResponseMinutes),
            'this_month_revenue' => $thisMonth,
            'last_month_revenue' => $lastMonth,
            'monthly_growth' => $monthlyGrowth,
            'total_recovered' => $totalRecovered,
        ];
    }

    private function getNeedsAttention(int $clinicId): \Illuminate\Database\Eloquent\Collection
    {
        return Conversation::forClinic($clinicId)
            ->with(['lead.caller'])
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('last_staff_reply_at')
                    ->orWhere('last_message_at', '>', 'last_staff_reply_at');
            })
            ->orderBy('last_message_at', 'desc')
            ->limit(5)
            ->get();
    }

    private function getRecentLeads(int $clinicId): \Illuminate\Database\Eloquent\Collection
    {
        return Lead::forClinic($clinicId)
            ->with(['caller', 'conversation'])
            ->latest()
            ->limit(5)
            ->get();
    }

    private function calculateWeeklyRevenue(int $clinicId): array
    {
        $days = collect();
        $maxRevenue = 0;
        $bestDayName = '';
        $bestDayRevenue = 0;

        // Get last 7 days
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $revenue = MissedCallOutcome::forClinic($clinicId)
                ->where('outcome_type', OutcomeType::BOOKED)
                ->whereDate('resolved_at', $date->toDateString())
                ->sum('actual_value');

            $days->push([
                'date' => $date,
                'label' => $date->format('D'),
                'revenue' => $revenue,
            ]);

            if ($revenue > $maxRevenue) {
                $maxRevenue = $revenue;
            }

            if ($revenue > $bestDayRevenue) {
                $bestDayRevenue = $revenue;
                $bestDayName = $date->format('l');
            }
        }

        // Calculate percentages
        $weeklyRevenue = $days->map(function ($day) use ($maxRevenue) {
            return [
                'label' => $day['label'],
                'revenue' => $day['revenue'],
                'percentage' => $maxRevenue > 0 ? round(($day['revenue'] / $maxRevenue) * 100) : 0,
            ];
        });

        return [
            'weeklyRevenue' => $weeklyRevenue,
            'weeklyTotal' => $days->sum('revenue'),
            'bestDay' => $bestDayName ?: 'N/A',
        ];
    }

    private function checkConfiguration(int $clinicId): array
    {
        $issues = [];

        // Check phone numbers (Recaller number assigned by admin)
        $hasActivePhone = ClinicPhoneNumber::where('clinic_id', $clinicId)
            ->where('is_active', true)
            ->exists();

        if (!$hasActivePhone) {
            $issues[] = [
                'type' => 'phone_pending',
                'severity' => 'info',
            ];
        }

        // Check SMS templates (user can fix this)
        $hasActiveTemplate = MessageTemplate::where('clinic_id', $clinicId)
            ->where('is_active', true)
            ->exists();

        if (!$hasActiveTemplate) {
            $issues[] = [
                'type' => 'template',
                'severity' => 'warning',
            ];
        }

        return [
            'is_complete' => empty($issues),
            'issues' => $issues,
        ];
    }
}
