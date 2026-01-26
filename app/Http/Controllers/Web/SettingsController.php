<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\MessageTemplate;
use App\Services\Payment\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function __construct(
        protected PaymentService $paymentService
    ) {}

    public function index(Request $request): View
    {
        $clinic = $request->user()->clinics()->with(['settings', 'integrations', 'phoneNumbers', 'clinicSubscriptions.plan'])->first();

        if (!$clinic) {
            abort(404, 'No clinic found');
        }

        $templates = MessageTemplate::where('clinic_id', $clinic->id)
            ->orderBy('sort_order')
            ->get();

        $integrations = [
            'twilio' => $clinic->integrations->where('provider', 'twilio')->first(),
            'vonage' => $clinic->integrations->where('provider', 'vonage')->first(),
            'messagebird' => $clinic->integrations->where('provider', 'messagebird')->first(),
        ];
        $activePhones = $clinic->phoneNumbers->where('is_active', true)->groupBy('provider');

        // Keep backwards compat
        $twilioIntegration = $integrations['twilio'];
        $activePhone = $clinic->phoneNumbers->where('is_active', true)->first();

        // Get subscription info
        $subscription = $clinic->activeSubscription($this->paymentService->getProviderName());
        $currentPlan = $subscription?->plan;

        return view('settings.index', compact('clinic', 'templates', 'twilioIntegration', 'activePhone', 'integrations', 'activePhones', 'subscription', 'currentPlan'));
    }

    public function updateGeneral(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'booking_link' => 'nullable|url|max:500',
            'business_hours_text' => 'nullable|string|max:255',
        ]);

        $clinic = $request->user()->clinics()->first();

        if (!$clinic) {
            abort(404);
        }

        $clinic->update(['name' => $validated['name']]);

        $clinic->settings()->updateOrCreate(
            ['clinic_id' => $clinic->id],
            [
                'booking_link' => $validated['booking_link'],
                'business_hours_text' => $validated['business_hours_text'],
            ]
        );

        return back()->with('success', 'Settings updated successfully.');
    }

    public function updateFollowup(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'followup_delay_seconds' => 'required|integer|min:30|max:3600',
            'avg_ticket_value' => 'required|numeric|min:0|max:99999',
        ]);

        $clinic = $request->user()->clinics()->first();

        if (!$clinic) {
            abort(404);
        }

        $clinic->settings()->updateOrCreate(
            ['clinic_id' => $clinic->id],
            $validated
        );

        return back()->with('success', 'Follow-up settings updated.');
    }

    public function updateTemplate(Request $request, int $templateId): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'body' => 'required|string|max:1000',
        ]);

        $clinic = $request->user()->clinics()->first();
        $template = MessageTemplate::where('clinic_id', $clinic->id)->findOrFail($templateId);

        $template->update($validated);

        return back()->with('success', 'Template updated.');
    }

    public function toggleTemplate(Request $request, int $templateId): RedirectResponse
    {
        $clinic = $request->user()->clinics()->first();
        $template = MessageTemplate::where('clinic_id', $clinic->id)->findOrFail($templateId);

        // If activating this template, deactivate others with same trigger_event
        if (!$template->is_active) {
            MessageTemplate::where('clinic_id', $clinic->id)
                ->where('trigger_event', $template->trigger_event)
                ->where('id', '!=', $template->id)
                ->update(['is_active' => false]);
        }

        $template->update(['is_active' => !$template->is_active]);

        return back()->with('success', $template->is_active ? 'Template activated.' : 'Template deactivated.');
    }

    public function storeTemplate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'body' => 'required|string|max:1000',
            'trigger_event' => 'required|string|in:missed_call,no_response,follow_up',
        ]);

        $clinic = $request->user()->clinics()->first();

        if (!$clinic) {
            abort(404);
        }

        // Get max sort order
        $maxSortOrder = MessageTemplate::where('clinic_id', $clinic->id)->max('sort_order') ?? 0;

        MessageTemplate::create([
            'clinic_id' => $clinic->id,
            'name' => $validated['name'],
            'body' => $validated['body'],
            'trigger_event' => $validated['trigger_event'],
            'channel' => 'sms',
            'is_active' => false,
            'sort_order' => $maxSortOrder + 1,
        ]);

        return back()->with('success', __('settings.template_created'));
    }

    public function destroyTemplate(Request $request, int $templateId): RedirectResponse
    {
        $clinic = $request->user()->clinics()->first();
        $template = MessageTemplate::where('clinic_id', $clinic->id)->findOrFail($templateId);

        $template->delete();

        return back()->with('success', __('settings.template_deleted'));
    }
}
