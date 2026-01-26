<?php

namespace App\Http\Controllers\Web;

use App\Enums\MessageChannel;
use App\Enums\MessageDirection;
use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\MessageTemplate;
use App\Services\SmsProviderFactory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SetupWizardController extends Controller
{
    protected array $steps = ['welcome', 'clinic', 'provider', 'templates', 'test', 'complete'];

    public function __construct(
        protected SmsProviderFactory $smsProviderFactory
    ) {}

    /**
     * Redirect to the appropriate step.
     */
    public function index(Request $request): RedirectResponse
    {
        $clinic = $request->user()->clinics()->first();

        if ($clinic?->hasCompletedSetup()) {
            return redirect()->route('dashboard');
        }

        return redirect()->route('setup.welcome');
    }

    /**
     * Step 1: Welcome
     */
    public function welcome(Request $request): View|RedirectResponse
    {
        $clinic = $request->user()->clinics()->first();

        if ($clinic?->hasCompletedSetup()) {
            return redirect()->route('dashboard');
        }

        return view('setup.welcome', [
            'step' => 1,
            'totalSteps' => count($this->steps),
            'user' => $request->user(),
        ]);
    }

    /**
     * Step 2: Clinic data
     */
    public function clinic(Request $request): View|RedirectResponse
    {
        $clinic = $request->user()->clinics()->with('settings')->first();

        if ($clinic?->hasCompletedSetup()) {
            return redirect()->route('dashboard');
        }

        return view('setup.clinic', [
            'step' => 2,
            'totalSteps' => count($this->steps),
            'clinic' => $clinic,
        ]);
    }

    /**
     * Save clinic data and proceed.
     */
    public function storeClinic(Request $request): RedirectResponse
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

        return redirect()->route('setup.provider');
    }

    /**
     * Step 3: Phone Number (assigned by Recaller)
     */
    public function provider(Request $request): View|RedirectResponse
    {
        $clinic = $request->user()->clinics()->with('phoneNumbers')->first();

        if (!$clinic) {
            return redirect()->route('setup.welcome');
        }

        if ($clinic->hasCompletedSetup()) {
            return redirect()->route('dashboard');
        }

        // Get the clinic's assigned phone number (if any)
        $phoneNumber = $clinic->phoneNumbers->where('is_active', true)->first();

        return view('setup.provider', [
            'step' => 3,
            'totalSteps' => count($this->steps),
            'clinic' => $clinic,
            'phoneNumber' => $phoneNumber,
        ]);
    }

    /**
     * Step 4: Templates
     */
    public function templates(Request $request): View|RedirectResponse
    {
        $clinic = $request->user()->clinics()->first();

        if (!$clinic) {
            return redirect()->route('setup.welcome');
        }

        if ($clinic->hasCompletedSetup()) {
            return redirect()->route('dashboard');
        }

        $templates = MessageTemplate::where('clinic_id', $clinic->id)
            ->orderBy('sort_order')
            ->get();

        return view('setup.templates', [
            'step' => 4,
            'totalSteps' => count($this->steps),
            'clinic' => $clinic,
            'templates' => $templates,
        ]);
    }

    /**
     * Save template and proceed.
     */
    public function storeTemplate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'body' => 'required|string|max:1000',
        ]);

        $clinic = $request->user()->clinics()->first();

        if (!$clinic) {
            abort(404);
        }

        // Get or create the missed_call template
        $template = MessageTemplate::where('clinic_id', $clinic->id)
            ->where('trigger_event', 'missed_call')
            ->first();

        if ($template) {
            $template->update([
                'name' => $validated['name'],
                'body' => $validated['body'],
                'is_active' => true,
            ]);
        } else {
            MessageTemplate::create([
                'clinic_id' => $clinic->id,
                'name' => $validated['name'],
                'body' => $validated['body'],
                'trigger_event' => 'missed_call',
                'channel' => 'sms',
                'is_active' => true,
                'sort_order' => 1,
            ]);
        }

        return redirect()->route('setup.test');
    }

    /**
     * Skip templates step.
     */
    public function skipTemplates(): RedirectResponse
    {
        return redirect()->route('setup.test');
    }

    /**
     * Step 5: Test SMS
     */
    public function test(Request $request): View|RedirectResponse
    {
        $clinic = $request->user()->clinics()->with(['phoneNumbers', 'messageTemplates', 'settings'])->first();

        if (!$clinic) {
            return redirect()->route('setup.welcome');
        }

        if ($clinic->hasCompletedSetup()) {
            return redirect()->route('dashboard');
        }

        $activePhone = $clinic->phoneNumbers->where('is_active', true)->first();
        $template = $clinic->messageTemplates->where('trigger_event', 'missed_call')->where('is_active', true)->first();

        // Build message preview with variables replaced
        $messagePreview = null;
        if ($template) {
            $messagePreview = str_replace(
                ['{{clinic_name}}', '{{booking_link}}', '{{business_hours}}'],
                [
                    $clinic->name,
                    $clinic->settings?->booking_link ?? '[booking_link]',
                    $clinic->settings?->business_hours_text ?? '[business_hours]',
                ],
                $template->body
            );
        }

        return view('setup.test', [
            'step' => 5,
            'totalSteps' => count($this->steps),
            'clinic' => $clinic,
            'activePhone' => $activePhone,
            'template' => $template,
            'messagePreview' => $messagePreview,
        ]);
    }

    /**
     * Send test SMS.
     */
    public function sendTest(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'phone_number' => 'required|string|max:20',
        ]);

        $clinic = $request->user()->clinics()->with(['phoneNumbers', 'messageTemplates', 'settings'])->first();

        if (!$clinic) {
            abort(404);
        }

        $activePhone = $clinic->phoneNumbers->where('is_active', true)->first();
        $template = $clinic->messageTemplates->where('trigger_event', 'missed_call')->where('is_active', true)->first();

        if (!$activePhone) {
            return back()->with('error', __('setup.no_phone_configured'));
        }

        // Build the message with variables replaced
        $messageBody = $template?->body ?? __('setup.default_test_message', ['clinic' => $clinic->name]);
        $messageBody = str_replace(
            ['{{clinic_name}}', '{{booking_link}}', '{{business_hours}}'],
            [
                $clinic->name,
                $clinic->settings?->booking_link ?? '',
                $clinic->settings?->business_hours_text ?? '',
            ],
            $messageBody
        );

        try {
            // Create a message record
            $message = Message::create([
                'clinic_id' => $clinic->id,
                'conversation_id' => null,
                'channel' => MessageChannel::SMS,
                'direction' => MessageDirection::OUTBOUND,
                'from_phone' => $activePhone->phone_number,
                'to_phone' => $validated['phone_number'],
                'body' => $messageBody,
                'status' => 'pending',
                'sent_by_user_id' => $request->user()->id,
            ]);

            // Get the appropriate SMS provider and send
            $provider = $this->smsProviderFactory->forPhoneNumber($activePhone);
            $success = $provider->sendSms($message);

            if ($success) {
                return back()->with('success', __('setup.test_sent'));
            } else {
                return back()->with('error', __('setup.test_failed'));
            }
        } catch (\Exception $e) {
            return back()->with('error', __('setup.test_failed') . ': ' . $e->getMessage());
        }
    }

    /**
     * Skip test step.
     */
    public function skipTest(): RedirectResponse
    {
        return redirect()->route('setup.complete');
    }

    /**
     * Step 6: Complete
     */
    public function complete(Request $request): View|RedirectResponse
    {
        $clinic = $request->user()->clinics()->first();

        if ($clinic?->hasCompletedSetup()) {
            return redirect()->route('dashboard');
        }

        return view('setup.complete', [
            'step' => 6,
            'totalSteps' => count($this->steps),
            'clinic' => $clinic,
        ]);
    }

    /**
     * Finish the wizard and redirect to dashboard.
     */
    public function finish(Request $request): RedirectResponse
    {
        $clinic = $request->user()->clinics()->first();

        if ($clinic) {
            $clinic->markSetupCompleted();
        }

        return redirect()->route('dashboard')->with('success', __('setup.welcome_complete'));
    }
}
