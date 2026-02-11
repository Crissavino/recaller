<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Clinic;
use App\Models\Plan;
use App\Services\Payment\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SubscriptionController extends Controller
{
    public function __construct(
        protected PaymentService $paymentService
    ) {}

    /**
     * Show the subscription management page.
     */
    public function index()
    {
        $clinic = $this->getClinic();
        $plans = Plan::active()->ordered()->get();
        $subscription = $clinic->activeSubscription($this->paymentService->getProviderName());

        return view('subscription.index', [
            'clinic' => $clinic,
            'plans' => $plans,
            'currentPlan' => $subscription?->plan,
            'subscription' => $subscription,
        ]);
    }

    /**
     * Show the checkout page for a specific plan.
     */
    public function checkout(Request $request, string $planSlug, string $interval = 'monthly')
    {
        $clinic = $this->getClinic();
        $plan = Plan::where('slug', $planSlug)->where('is_active', true)->first();

        if (!$plan) {
            return redirect()->route('pricing')
                ->with('error', __('subscription.plan_not_found', ['plan' => $planSlug]));
        }

        // Validate interval
        if (!in_array($interval, ['monthly', 'annual'])) {
            $interval = 'monthly';
        }

        // If already subscribed, redirect to billing portal
        if ($clinic->hasActiveSubscription($this->paymentService->getProviderName())) {
            return redirect($this->paymentService->getBillingPortalUrl($clinic, route('subscription.index')));
        }

        try {
            $currency = session('currency', config('app.locale_currencies.' . app()->getLocale(), 'eur'));

            $checkoutSession = $this->paymentService->createCheckoutSession($clinic, $plan, $interval, [
                'trial_days' => config('plans.trial_days', 14),
                'currency' => $currency,
            ]);

            return redirect($checkoutSession['url']);
        } catch (\Exception $e) {
            Log::error('Checkout failed', [
                'plan' => $planSlug,
                'interval' => $interval,
                'clinic_id' => $clinic->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('pricing')
                ->with('error', __('subscription.checkout_error') . ' (' . $e->getMessage() . ')');
        }
    }

    /**
     * Handle successful checkout.
     */
    public function success(Request $request)
    {
        $clinic = $this->getClinic();

        // If setup not completed, redirect to setup wizard
        if (!$clinic->hasCompletedSetup()) {
            return redirect()->route('setup.welcome')
                ->with('success', __('subscription.payment_success'));
        }

        // If already set up, show success page or go to dashboard
        return redirect()->route('dashboard')
            ->with('success', __('subscription.payment_success'));
    }

    /**
     * Handle cancelled checkout.
     */
    public function cancel()
    {
        return redirect()->route('pricing')->with('info', __('subscription.checkout_cancelled'));
    }

    /**
     * Redirect to billing portal.
     */
    public function billingPortal()
    {
        $clinic = $this->getClinic();

        try {
            $url = $this->paymentService->getBillingPortalUrl($clinic, route('subscription.index'));
            return redirect($url);
        } catch (\Exception $e) {
            return redirect()->route('subscription.index')
                ->with('error', __('subscription.billing_portal_error'));
        }
    }

    /**
     * Change subscription plan.
     */
    public function changePlan(Request $request)
    {
        $request->validate([
            'plan' => 'required|string|exists:plans,slug',
            'interval' => 'required|string|in:monthly,annual',
        ]);

        $clinic = $this->getClinic();
        $subscription = $clinic->activeSubscription($this->paymentService->getProviderName());

        if (!$subscription) {
            return redirect()->route('subscription.checkout', [
                'plan' => $request->plan,
                'interval' => $request->interval,
            ]);
        }

        $newPlan = Plan::where('slug', $request->plan)->where('is_active', true)->firstOrFail();

        try {
            $this->paymentService->changePlan($subscription, $newPlan, $request->interval);

            return redirect()->route('subscription.index')
                ->with('success', __('subscription.plan_changed'));
        } catch (\Exception $e) {
            return redirect()->route('subscription.index')
                ->with('error', __('subscription.plan_change_error'));
        }
    }

    /**
     * Cancel subscription.
     */
    public function cancelSubscription(Request $request)
    {
        $clinic = $this->getClinic();
        $subscription = $clinic->activeSubscription($this->paymentService->getProviderName());

        if ($subscription) {
            try {
                $this->paymentService->cancelSubscription($subscription);
            } catch (\Exception $e) {
                return redirect()->route('subscription.index')
                    ->with('error', __('subscription.cancel_error'));
            }
        }

        return redirect()->route('subscription.index')
            ->with('success', __('subscription.cancelled'));
    }

    /**
     * Resume a cancelled subscription.
     */
    public function resumeSubscription(Request $request)
    {
        $clinic = $this->getClinic();
        $subscription = $clinic->activeSubscription($this->paymentService->getProviderName());

        if ($subscription?->onGracePeriod()) {
            try {
                $this->paymentService->resumeSubscription($subscription);
            } catch (\Exception $e) {
                return redirect()->route('subscription.index')
                    ->with('error', __('subscription.resume_error'));
            }
        }

        return redirect()->route('subscription.index')
            ->with('success', __('subscription.resumed'));
    }

    /**
     * Show invoice history.
     */
    public function invoices()
    {
        $clinic = $this->getClinic();
        $invoices = $this->paymentService->getInvoices($clinic);

        return view('subscription.invoices', [
            'invoices' => $invoices,
        ]);
    }

    /**
     * Get the current user's clinic.
     */
    private function getClinic(): Clinic
    {
        return Auth::user()->clinics()->firstOrFail();
    }
}
