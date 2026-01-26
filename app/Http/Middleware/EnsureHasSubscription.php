<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureHasSubscription
{
    /**
     * Routes that are exempt from the subscription requirement.
     */
    protected array $except = [
        'subscription.checkout',
        'subscription.success',
        'subscription.checkout-cancelled',
        'subscription.index',
        'subscription.billing-portal',
        'subscription.invoices',
        'subscription.change-plan',
        'subscription.cancel',
        'subscription.resume',
        'logout',
        'locale.switch',
        'pricing',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($this->shouldSkip($request)) {
            return $next($request);
        }

        $user = $request->user();

        if (!$user) {
            return $next($request);
        }

        $clinic = $user->clinics()->first();

        // If clinic has no active subscription
        if ($clinic && !$clinic->hasActiveSubscription()) {
            // If setup not completed, send to pricing (new user flow)
            if (!$clinic->hasCompletedSetup()) {
                return redirect()->route('pricing')
                    ->with('info', __('subscription.select_plan_first'));
            }

            // If setup completed, send to subscription management (returning user)
            return redirect()->route('subscription.index')
                ->with('info', __('subscription.subscription_required'));
        }

        return $next($request);
    }

    /**
     * Determine if the request should skip the subscription check.
     */
    protected function shouldSkip(Request $request): bool
    {
        foreach ($this->except as $pattern) {
            if ($request->routeIs($pattern)) {
                return true;
            }
        }

        return false;
    }
}
