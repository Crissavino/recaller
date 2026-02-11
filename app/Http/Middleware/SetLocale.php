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
        $availableLocales = config('app.available_locales', ['ro', 'es', 'en']);

        if (session()->has('locale')) {
            $locale = session('locale');
        } else {
            $locale = $this->detectLocaleFromBrowser($request, $availableLocales);
            session(['locale' => $locale]);
        }

        if (in_array($locale, $availableLocales)) {
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

    private function detectLocaleFromBrowser(Request $request, array $available): string
    {
        $header = $request->header('Accept-Language', '');

        // Parse Accept-Language: "ro-RO,ro;q=0.9,en-US;q=0.8,en;q=0.7,es;q=0.6"
        $languages = [];
        foreach (explode(',', $header) as $part) {
            $part = trim($part);
            if (str_contains($part, ';q=')) {
                [$lang, $q] = explode(';q=', $part);
                $languages[trim($lang)] = (float) $q;
            } else {
                $languages[$part] = 1.0;
            }
        }

        arsort($languages);

        // Map browser language codes to our locales
        $mapping = [
            'ro' => 'ro', 'ro-ro' => 'ro', 'ro-md' => 'ro',
            'es' => 'es', 'es-es' => 'es', 'es-ar' => 'es', 'es-mx' => 'es', 'es-co' => 'es', 'es-cl' => 'es',
            'en' => 'en', 'en-us' => 'en', 'en-gb' => 'en', 'en-au' => 'en',
        ];

        foreach ($languages as $lang => $priority) {
            $lang = strtolower(trim($lang));
            if (isset($mapping[$lang])) {
                return $mapping[$lang];
            }
            // Try base language (e.g. "es-419" -> "es")
            $base = explode('-', $lang)[0];
            if (in_array($base, $available)) {
                return $base;
            }
        }

        return 'en';
    }
}
