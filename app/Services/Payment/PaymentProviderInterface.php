<?php

namespace App\Services\Payment;

use App\Models\Clinic;
use App\Models\ClinicSubscription;
use App\Models\PaymentCustomer;
use App\Models\PaymentMethod;
use App\Models\Plan;

interface PaymentProviderInterface
{
    /**
     * Get the provider name identifier.
     */
    public function getName(): string;

    /**
     * Create a customer in the payment provider.
     */
    public function createCustomer(Clinic $clinic, array $options = []): PaymentCustomer;

    /**
     * Get or create a customer for the clinic.
     */
    public function getOrCreateCustomer(Clinic $clinic): PaymentCustomer;

    /**
     * Update customer information in the payment provider.
     */
    public function updateCustomer(PaymentCustomer $customer, array $data): PaymentCustomer;

    /**
     * Delete a customer from the payment provider.
     */
    public function deleteCustomer(PaymentCustomer $customer): bool;

    /**
     * Create a checkout session for subscription.
     */
    public function createCheckoutSession(
        Clinic $clinic,
        Plan $plan,
        string $interval,
        array $options = []
    ): array;

    /**
     * Create a subscription directly (without checkout).
     */
    public function createSubscription(
        Clinic $clinic,
        Plan $plan,
        string $interval,
        array $options = []
    ): ClinicSubscription;

    /**
     * Cancel a subscription.
     */
    public function cancelSubscription(ClinicSubscription $subscription, bool $immediately = false): ClinicSubscription;

    /**
     * Resume a canceled subscription.
     */
    public function resumeSubscription(ClinicSubscription $subscription): ClinicSubscription;

    /**
     * Change subscription plan.
     */
    public function changePlan(ClinicSubscription $subscription, Plan $newPlan, string $interval): ClinicSubscription;

    /**
     * Get the billing portal URL.
     */
    public function getBillingPortalUrl(Clinic $clinic, string $returnUrl): string;

    /**
     * Sync subscription data from provider.
     */
    public function syncSubscription(string $providerSubscriptionId): ?ClinicSubscription;

    /**
     * Add a payment method.
     */
    public function addPaymentMethod(Clinic $clinic, string $providerPaymentMethodId): PaymentMethod;

    /**
     * Set default payment method.
     */
    public function setDefaultPaymentMethod(PaymentMethod $paymentMethod): PaymentMethod;

    /**
     * Remove a payment method.
     */
    public function removePaymentMethod(PaymentMethod $paymentMethod): bool;

    /**
     * Handle webhook event.
     */
    public function handleWebhook(array $payload): void;

    /**
     * Get invoices for a clinic.
     */
    public function getInvoices(Clinic $clinic, int $limit = 24): array;
}
