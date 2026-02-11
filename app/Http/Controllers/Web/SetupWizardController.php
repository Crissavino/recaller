<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SetupWizardController extends Controller
{
    protected array $steps = ['welcome', 'clinic', 'phone', 'complete'];

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
            'phone' => 'required|string|max:20',
            'booking_link' => 'nullable|url|max:500',
            'business_hours_text' => 'nullable|string|max:255',
        ]);

        $clinic = $request->user()->clinics()->first();

        if (!$clinic) {
            abort(404);
        }

        $clinic->update([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
        ]);

        $clinic->settings()->updateOrCreate(
            ['clinic_id' => $clinic->id],
            [
                'booking_link' => $validated['booking_link'],
                'business_hours_text' => $validated['business_hours_text'],
            ]
        );

        return redirect()->route('setup.phone');
    }

    /**
     * Step 3: Phone Setup
     */
    public function phone(Request $request): View|RedirectResponse
    {
        $clinic = $request->user()->clinics()->with('phoneNumbers')->first();

        if (!$clinic) {
            return redirect()->route('setup.welcome');
        }

        if ($clinic->hasCompletedSetup()) {
            return redirect()->route('dashboard');
        }

        $recallerNumber = $clinic->phoneNumbers->first();

        return view('setup.provider', [
            'step' => 3,
            'totalSteps' => count($this->steps),
            'clinic' => $clinic,
            'recallerNumber' => $recallerNumber,
        ]);
    }

    /**
     * Step 4: Complete
     */
    public function complete(Request $request): View|RedirectResponse
    {
        $clinic = $request->user()->clinics()->first();

        if ($clinic?->hasCompletedSetup()) {
            return redirect()->route('dashboard');
        }

        return view('setup.complete', [
            'step' => 4,
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
