<?php

namespace App\Services\Payment;

use App\Models\Clinic;
use App\Models\ClinicSubscription;
use App\Models\PaymentCustomer;
use App\Models\PaymentMethod;
use App\Models\Plan;
use Illuminate\Support\Facades\App;

class PaymentService
{
    protected PaymentProviderInterface $provider;

    public function __construct(?PaymentProviderInterface $provider = null)
    {
        $this->provider = $provider ?? $this->resolveDefaultProvider();
    }

    /**
     * Get the current provider.
     */
    public function getProvider(): PaymentProviderInterface
    {
        return $this->provider;
    }

    /**
     * Get the provider name.
     */
    public function getProviderName(): string
    {
        return $this->provider->getName();
    }

    /**
     * Use a specific provider.
     */
    public function using(string $provider): self
    {
        $this->provider = $this->resolveProvider($provider);
        return $this;
    }

    /**
     * Create or get a customer.
     */
    public function getOrCreateCustomer(Clinic $clinic): PaymentCustomer
    {
        return $this->provider->getOrCreateCustomer($clinic);
    }

    /**
     * Create a checkout session for subscription.
     */
    public function createCheckoutSession(
        Clinic $clinic,
        Plan $plan,
        string $interval = 'monthly',
        array $options = []
    ): array {
        return $this->provider->createCheckoutSession($clinic, $plan, $interval, $options);
    }

    /**
     * Create a subscription directly.
     */
    public function createSubscription(
        Clinic $clinic,
        Plan $plan,
        string $interval = 'monthly',
        array $options = []
    ): ClinicSubscription {
        return $this->provider->createSubscription($clinic, $plan, $interval, $options);
    }

    /**
     * Cancel a subscription.
     */
    public function cancelSubscription(ClinicSubscription $subscription, bool $immediately = false): ClinicSubscription
    {
        return $this->provider->cancelSubscription($subscription, $immediately);
    }

    /**
     * Resume a canceled subscription.
     */
    public function resumeSubscription(ClinicSubscription $subscription): ClinicSubscription
    {
        return $this->provider->resumeSubscription($subscription);
    }

    /**
     * Change subscription plan.
     */
    public function changePlan(ClinicSubscription $subscription, Plan $newPlan, string $interval): ClinicSubscription
    {
        return $this->provider->changePlan($subscription, $newPlan, $interval);
    }

    /**
     * Get billing portal URL.
     */
    public function getBillingPortalUrl(Clinic $clinic, string $returnUrl): string
    {
        return $this->provider->getBillingPortalUrl($clinic, $returnUrl);
    }

    /**
     * Sync subscription from provider.
     */
    public function syncSubscription(string $providerSubscriptionId): ?ClinicSubscription
    {
        return $this->provider->syncSubscription($providerSubscriptionId);
    }

    /**
     * Add a payment method.
     */
    public function addPaymentMethod(Clinic $clinic, string $providerPaymentMethodId): PaymentMethod
    {
        return $this->provider->addPaymentMethod($clinic, $providerPaymentMethodId);
    }

    /**
     * Set default payment method.
     */
    public function setDefaultPaymentMethod(PaymentMethod $paymentMethod): PaymentMethod
    {
        return $this->provider->setDefaultPaymentMethod($paymentMethod);
    }

    /**
     * Remove a payment method.
     */
    public function removePaymentMethod(PaymentMethod $paymentMethod): bool
    {
        return $this->provider->removePaymentMethod($paymentMethod);
    }

    /**
     * Handle webhook.
     */
    public function handleWebhook(array $payload): void
    {
        $this->provider->handleWebhook($payload);
    }

    /**
     * Get the active subscription for a clinic.
     */
    public function getActiveSubscription(Clinic $clinic): ?ClinicSubscription
    {
        return ClinicSubscription::where('clinic_id', $clinic->id)
            ->where('provider', $this->getProviderName())
            ->active()
            ->first();
    }

    /**
     * Check if clinic has an active subscription.
     */
    public function hasActiveSubscription(Clinic $clinic): bool
    {
        return $this->getActiveSubscription($clinic) !== null;
    }

    /**
     * Check if clinic is on trial.
     */
    public function isOnTrial(Clinic $clinic): bool
    {
        $subscription = $this->getActiveSubscription($clinic);
        return $subscription?->onTrial() ?? false;
    }

    /**
     * Get invoices for a clinic.
     */
    public function getInvoices(Clinic $clinic, int $limit = 24): array
    {
        return $this->provider->getInvoices($clinic, $limit);
    }

    /**
     * Resolve the default payment provider.
     */
    protected function resolveDefaultProvider(): PaymentProviderInterface
    {
        $default = config('services.payment.default', 'stripe');
        return $this->resolveProvider($default);
    }

    /**
     * Resolve a specific provider by name.
     */
    protected function resolveProvider(string $name): PaymentProviderInterface
    {
        return match ($name) {
            'stripe' => App::make(StripePaymentProvider::class),
            // Add more providers here as needed:
            // 'paypal' => App::make(PayPalPaymentProvider::class),
            // 'mercadopago' => App::make(MercadoPagoPaymentProvider::class),
            default => throw new \InvalidArgumentException("Unknown payment provider: {$name}"),
        };
    }
}
