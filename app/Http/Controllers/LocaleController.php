<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class LocaleController extends Controller
{
    /**
     * Switch the application locale.
     * Also sets the default currency based on locale if not explicitly chosen.
     */
    public function switch(Request $request, string $locale)
    {
        $availableLocales = config('app.available_locales', ['ro', 'es', 'en']);

        if (in_array($locale, $availableLocales)) {
            session(['locale' => $locale]);
            App::setLocale($locale);

            // Auto-set currency based on locale (unless user has explicitly chosen one)
            if (!session('currency_explicit')) {
                $defaultCurrency = config('app.locale_currencies.' . $locale, 'eur');
                session(['currency' => $defaultCurrency]);
            }
        }

        return redirect()->back();
    }

    /**
     * Switch the display currency.
     */
    public function switchCurrency(Request $request, string $currency)
    {
        $availableCurrencies = ['eur', 'ron'];

        if (in_array($currency, $availableCurrencies)) {
            session(['currency' => $currency]);
            session(['currency_explicit' => true]);
        }

        return redirect()->back();
    }
}
