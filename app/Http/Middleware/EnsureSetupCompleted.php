<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSetupCompleted
{
    /**
     * Routes that are exempt from the setup requirement.
     */
    protected array $except = [
        'setup.*',
        'logout',
        'locale.switch',
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

        // If user has no clinic or clinic hasn't completed setup, redirect to wizard
        if (!$clinic || !$clinic->hasCompletedSetup()) {
            return redirect()->route('setup.welcome');
        }

        return $next($request);
    }

    /**
     * Determine if the request should skip the setup check.
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
