<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = session('locale', config('app.locale'));

        if (in_array($locale, config('app.available_locales', ['ro', 'es', 'en']))) {
            App::setLocale($locale);
        }

        // Resolve currency: session > locale default
        $currency = session('currency', config('app.locale_currencies.' . $locale, 'eur'));
        $currencySymbol = match ($currency) {
            'ron' => 'RON ',
            default => '€',
        };

        View::share('currentCurrency', $currency);
        View::share('currencySymbol', $currencySymbol);

        return $next($request);
    }
}
