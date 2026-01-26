<?php

namespace App\Providers;

use App\Services\Payment\PaymentProviderInterface;
use App\Services\Payment\PaymentService;
use App\Services\Payment\StripePaymentProvider;
use Illuminate\Support\ServiceProvider;

class PaymentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register the Stripe provider
        $this->app->singleton(StripePaymentProvider::class, function ($app) {
            return new StripePaymentProvider();
        });

        // Register the default provider interface binding
        $this->app->bind(PaymentProviderInterface::class, function ($app) {
            $default = config('services.payment.default', 'stripe');

            return match ($default) {
                'stripe' => $app->make(StripePaymentProvider::class),
                default => throw new \InvalidArgumentException("Unknown payment provider: {$default}"),
            };
        });

        // Register the main payment service
        $this->app->singleton(PaymentService::class, function ($app) {
            return new PaymentService($app->make(PaymentProviderInterface::class));
        });

        // Register an alias for easy access
        $this->app->alias(PaymentService::class, 'payment');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
