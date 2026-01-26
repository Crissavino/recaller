<?php

namespace App\Services\Payment;

use App\Models\Clinic;
use App\Models\ClinicSubscription;
use App\Models\PaymentCustomer;
use App\Models\PaymentMethod;
use App\Models\PaymentTransaction;
use App\Models\Plan;
use App\Models\PlanPrice;
use Illuminate\Support\Facades\Log;
use Stripe\BillingPortal\Session as BillingPortalSession;
use Stripe\Checkout\Session as CheckoutSession;
use Stripe\Customer;
use Stripe\Invoice;
use Stripe\PaymentMethod as StripePaymentMethod;
use Stripe\Stripe;
use Stripe\Subscription;
use Stripe\Webhook;

class StripePaymentProvider implements PaymentProviderInterface
{
    public const NAME = 'stripe';

    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function createCustomer(Clinic $clinic, array $options = []): PaymentCustomer
    {
        $user = $clinic->users()->first();

        $stripeCustomer = Customer::create([
            'email' => $user?->email ?? $options['email'] ?? null,
            'name' => $clinic->name,
            'metadata' => [
                'clinic_id' => $clinic->id,
            ],
            ...$options,
        ]);

        return PaymentCustomer::create([
            'clinic_id' => $clinic->id,
            'provider' => self::NAME,
            'provider_customer_id' => $stripeCustomer->id,
            'email' => $stripeCustomer->email,
            'name' => $stripeCustomer->name,
            'provider_data' => [
                'created' => $stripeCustomer->created,
                'livemode' => $stripeCustomer->livemode,
            ],
        ]);
    }

    public function getOrCreateCustomer(Clinic $clinic): PaymentCustomer
    {
        $customer = PaymentCustomer::where('clinic_id', $clinic->id)
            ->where('provider', self::NAME)
            ->first();

        if (!$customer) {
            $customer = $this->createCustomer($clinic);
        }

        return $customer;
    }

    public function updateCustomer(PaymentCustomer $customer, array $data): PaymentCustomer
    {
        Customer::update($customer->provider_customer_id, $data);

        $customer->update([
            'email' => $data['email'] ?? $customer->email,
            'name' => $data['name'] ?? $customer->name,
        ]);

        return $customer->fresh();
    }

    public function deleteCustomer(PaymentCustomer $customer): bool
    {
        try {
            Customer::retrieve($customer->provider_customer_id)->delete();
            $customer->delete();
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to delete Stripe customer', [
                'customer_id' => $customer->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    public function createCheckoutSession(
        Clinic $clinic,
        Plan $plan,
        string $interval,
        array $options = []
    ): array {
        $customer = $this->getOrCreateCustomer($clinic);
        $planPrice = $plan->getPriceFor(self::NAME, $interval);

        if (!$planPrice) {
            throw new \RuntimeException("No Stripe price found for plan {$plan->slug} with interval {$interval}");
        }

        $sessionParams = [
            'customer' => $customer->provider_customer_id,
            'mode' => 'subscription',
            'line_items' => [[
                'price' => $planPrice->provider_price_id,
                'quantity' => 1,
            ]],
            'success_url' => $options['success_url'] ?? route('subscription.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => $options['cancel_url'] ?? route('subscription.checkout-cancelled'),
            'subscription_data' => [
                'metadata' => [
                    'clinic_id' => $clinic->id,
                    'plan_id' => $plan->id,
                ],
            ],
            'allow_promotion_codes' => $options['allow_promotion_codes'] ?? true,
            'billing_address_collection' => $options['billing_address_collection'] ?? 'required',
        ];

        // Add trial if specified
        $trialDays = $options['trial_days'] ?? config('plans.trial_days', 0);
        if ($trialDays > 0) {
            $sessionParams['subscription_data']['trial_period_days'] = $trialDays;
        }

        $session = CheckoutSession::create($sessionParams);

        return [
            'session_id' => $session->id,
            'url' => $session->url,
        ];
    }

    public function createSubscription(
        Clinic $clinic,
        Plan $plan,
        string $interval,
        array $options = []
    ): ClinicSubscription {
        $customer = $this->getOrCreateCustomer($clinic);
        $planPrice = $plan->getPriceFor(self::NAME, $interval);

        if (!$planPrice) {
            throw new \RuntimeException("No Stripe price found for plan {$plan->slug} with interval {$interval}");
        }

        $subscriptionParams = [
            'customer' => $customer->provider_customer_id,
            'items' => [[
                'price' => $planPrice->provider_price_id,
            ]],
            'metadata' => [
                'clinic_id' => $clinic->id,
                'plan_id' => $plan->id,
            ],
        ];

        // Add trial if specified
        $trialDays = $options['trial_days'] ?? config('plans.trial_days', 0);
        if ($trialDays > 0) {
            $subscriptionParams['trial_period_days'] = $trialDays;
        }

        // Set default payment method if provided
        if (isset($options['payment_method'])) {
            $subscriptionParams['default_payment_method'] = $options['payment_method'];
        }

        $stripeSubscription = Subscription::create($subscriptionParams);

        return $this->createLocalSubscription($clinic, $plan, $stripeSubscription, $interval);
    }

    public function cancelSubscription(ClinicSubscription $subscription, bool $immediately = false): ClinicSubscription
    {
        $stripeSubscription = Subscription::retrieve($subscription->provider_subscription_id);

        if ($immediately) {
            $stripeSubscription->cancel();
            $subscription->update([
                'status' => ClinicSubscription::STATUS_CANCELED,
                'canceled_at' => now(),
                'ends_at' => now(),
            ]);
        } else {
            $stripeSubscription->cancel_at_period_end = true;
            $stripeSubscription->save();

            $subscription->update([
                'canceled_at' => now(),
                'ends_at' => $subscription->current_period_end,
            ]);
        }

        return $subscription->fresh();
    }

    public function resumeSubscription(ClinicSubscription $subscription): ClinicSubscription
    {
        if (!$subscription->onGracePeriod()) {
            throw new \RuntimeException('Subscription is not on grace period');
        }

        $stripeSubscription = Subscription::retrieve($subscription->provider_subscription_id);
        $stripeSubscription->cancel_at_period_end = false;
        $stripeSubscription->save();

        $subscription->update([
            'canceled_at' => null,
            'ends_at' => null,
        ]);

        return $subscription->fresh();
    }

    public function changePlan(ClinicSubscription $subscription, Plan $newPlan, string $interval): ClinicSubscription
    {
        $planPrice = $newPlan->getPriceFor(self::NAME, $interval);

        if (!$planPrice) {
            throw new \RuntimeException("No Stripe price found for plan {$newPlan->slug} with interval {$interval}");
        }

        $stripeSubscription = Subscription::retrieve($subscription->provider_subscription_id);

        Subscription::update($subscription->provider_subscription_id, [
            'items' => [[
                'id' => $stripeSubscription->items->data[0]->id,
                'price' => $planPrice->provider_price_id,
            ]],
            'metadata' => [
                'clinic_id' => $subscription->clinic_id,
                'plan_id' => $newPlan->id,
            ],
            'proration_behavior' => 'create_prorations',
        ]);

        $subscription->update([
            'plan_id' => $newPlan->id,
            'provider_price_id' => $planPrice->provider_price_id,
            'interval' => $interval,
        ]);

        return $subscription->fresh();
    }

    public function getBillingPortalUrl(Clinic $clinic, string $returnUrl): string
    {
        $customer = $this->getOrCreateCustomer($clinic);

        $session = BillingPortalSession::create([
            'customer' => $customer->provider_customer_id,
            'return_url' => $returnUrl,
        ]);

        return $session->url;
    }

    public function syncSubscription(string $providerSubscriptionId): ?ClinicSubscription
    {
        try {
            $stripeSubscription = Subscription::retrieve($providerSubscriptionId);
            $subscription = ClinicSubscription::findByProviderId(self::NAME, $providerSubscriptionId);

            if (!$subscription) {
                // Find clinic from metadata or customer
                $clinicId = $stripeSubscription->metadata['clinic_id'] ?? null;

                if (!$clinicId) {
                    $customer = PaymentCustomer::findByProviderId(self::NAME, $stripeSubscription->customer);
                    $clinicId = $customer?->clinic_id;
                }

                if (!$clinicId) {
                    Log::warning('Could not find clinic for Stripe subscription', [
                        'subscription_id' => $providerSubscriptionId,
                    ]);
                    return null;
                }

                $clinic = Clinic::find($clinicId);
                $planPrice = PlanPrice::findByProviderPriceId(
                    self::NAME,
                    $stripeSubscription->items->data[0]->price->id
                );

                if (!$planPrice) {
                    Log::warning('Could not find plan for Stripe price', [
                        'price_id' => $stripeSubscription->items->data[0]->price->id,
                    ]);
                    return null;
                }

                $subscription = $this->createLocalSubscription(
                    $clinic,
                    $planPrice->plan,
                    $stripeSubscription,
                    $stripeSubscription->items->data[0]->price->recurring->interval === 'year' ? 'annual' : 'monthly'
                );
            } else {
                $this->updateLocalSubscription($subscription, $stripeSubscription);
            }

            return $subscription;
        } catch (\Exception $e) {
            Log::error('Failed to sync Stripe subscription', [
                'subscription_id' => $providerSubscriptionId,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    public function addPaymentMethod(Clinic $clinic, string $providerPaymentMethodId): PaymentMethod
    {
        $customer = $this->getOrCreateCustomer($clinic);

        // Attach payment method to customer
        $stripePaymentMethod = StripePaymentMethod::retrieve($providerPaymentMethodId);
        $stripePaymentMethod->attach(['customer' => $customer->provider_customer_id]);

        $isFirstPaymentMethod = PaymentMethod::where('clinic_id', $clinic->id)
            ->where('provider', self::NAME)
            ->count() === 0;

        $paymentMethod = PaymentMethod::create([
            'clinic_id' => $clinic->id,
            'provider' => self::NAME,
            'provider_payment_method_id' => $providerPaymentMethodId,
            'type' => $stripePaymentMethod->type,
            'brand' => $stripePaymentMethod->card?->brand,
            'last_four' => $stripePaymentMethod->card?->last4,
            'exp_month' => $stripePaymentMethod->card?->exp_month,
            'exp_year' => $stripePaymentMethod->card?->exp_year,
            'is_default' => $isFirstPaymentMethod,
            'provider_data' => [
                'fingerprint' => $stripePaymentMethod->card?->fingerprint,
                'funding' => $stripePaymentMethod->card?->funding,
            ],
        ]);

        if ($isFirstPaymentMethod) {
            Customer::update($customer->provider_customer_id, [
                'invoice_settings' => ['default_payment_method' => $providerPaymentMethodId],
            ]);
        }

        return $paymentMethod;
    }

    public function setDefaultPaymentMethod(PaymentMethod $paymentMethod): PaymentMethod
    {
        $customer = PaymentCustomer::where('clinic_id', $paymentMethod->clinic_id)
            ->where('provider', self::NAME)
            ->firstOrFail();

        Customer::update($customer->provider_customer_id, [
            'invoice_settings' => ['default_payment_method' => $paymentMethod->provider_payment_method_id],
        ]);

        // Update local records
        PaymentMethod::where('clinic_id', $paymentMethod->clinic_id)
            ->where('provider', self::NAME)
            ->update(['is_default' => false]);

        $paymentMethod->update(['is_default' => true]);

        return $paymentMethod->fresh();
    }

    public function removePaymentMethod(PaymentMethod $paymentMethod): bool
    {
        try {
            $stripePaymentMethod = StripePaymentMethod::retrieve($paymentMethod->provider_payment_method_id);
            $stripePaymentMethod->detach();
            $paymentMethod->delete();
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to remove Stripe payment method', [
                'payment_method_id' => $paymentMethod->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    public function handleWebhook(array $payload): void
    {
        $eventType = $payload['type'] ?? null;
        $data = $payload['data']['object'] ?? [];

        Log::info('Processing Stripe webhook', ['type' => $eventType]);

        match ($eventType) {
            'customer.subscription.created',
            'customer.subscription.updated' => $this->handleSubscriptionUpdated($data),
            'customer.subscription.deleted' => $this->handleSubscriptionDeleted($data),
            'invoice.payment_succeeded' => $this->handleInvoicePaymentSucceeded($data),
            'invoice.payment_failed' => $this->handleInvoicePaymentFailed($data),
            'checkout.session.completed' => $this->handleCheckoutCompleted($data),
            'payment_method.attached' => $this->handlePaymentMethodAttached($data),
            'customer.updated' => $this->handleCustomerUpdated($data),
            default => Log::info('Unhandled Stripe webhook event', ['type' => $eventType]),
        };
    }

    protected function handleSubscriptionUpdated(array $data): void
    {
        $this->syncSubscription($data['id']);
    }

    protected function handleSubscriptionDeleted(array $data): void
    {
        $subscription = ClinicSubscription::findByProviderId(self::NAME, $data['id']);

        if ($subscription) {
            $subscription->update([
                'status' => ClinicSubscription::STATUS_CANCELED,
                'ends_at' => now(),
            ]);
        }
    }

    protected function handleInvoicePaymentSucceeded(array $data): void
    {
        $customerId = $data['customer'] ?? null;
        $customer = PaymentCustomer::findByProviderId(self::NAME, $customerId);

        if (!$customer) {
            return;
        }

        $subscriptionId = $data['subscription'] ?? null;
        $subscription = $subscriptionId
            ? ClinicSubscription::findByProviderId(self::NAME, $subscriptionId)
            : null;

        PaymentTransaction::create([
            'clinic_id' => $customer->clinic_id,
            'clinic_subscription_id' => $subscription?->id,
            'provider' => self::NAME,
            'provider_transaction_id' => $data['id'],
            'type' => PaymentTransaction::TYPE_INVOICE,
            'status' => PaymentTransaction::STATUS_SUCCEEDED,
            'amount_cents' => $data['amount_paid'] ?? 0,
            'currency' => $data['currency'] ?? 'usd',
            'description' => $data['description'] ?? 'Subscription payment',
            'paid_at' => isset($data['status_transitions']['paid_at'])
                ? \Carbon\Carbon::createFromTimestamp($data['status_transitions']['paid_at'])
                : now(),
            'provider_data' => [
                'invoice_number' => $data['number'] ?? null,
                'invoice_pdf' => $data['invoice_pdf'] ?? null,
                'hosted_invoice_url' => $data['hosted_invoice_url'] ?? null,
            ],
        ]);
    }

    protected function handleInvoicePaymentFailed(array $data): void
    {
        $subscriptionId = $data['subscription'] ?? null;

        if ($subscriptionId) {
            $subscription = ClinicSubscription::findByProviderId(self::NAME, $subscriptionId);

            if ($subscription) {
                $subscription->update(['status' => ClinicSubscription::STATUS_PAST_DUE]);
            }
        }

        $customerId = $data['customer'] ?? null;
        $customer = PaymentCustomer::findByProviderId(self::NAME, $customerId);

        if ($customer) {
            PaymentTransaction::create([
                'clinic_id' => $customer->clinic_id,
                'clinic_subscription_id' => $subscription?->id ?? null,
                'provider' => self::NAME,
                'provider_transaction_id' => $data['id'],
                'type' => PaymentTransaction::TYPE_INVOICE,
                'status' => PaymentTransaction::STATUS_FAILED,
                'amount_cents' => $data['amount_due'] ?? 0,
                'currency' => $data['currency'] ?? 'usd',
                'description' => 'Failed payment',
                'provider_data' => [
                    'attempt_count' => $data['attempt_count'] ?? null,
                    'next_payment_attempt' => $data['next_payment_attempt'] ?? null,
                ],
            ]);
        }
    }

    protected function handleCheckoutCompleted(array $data): void
    {
        if ($data['mode'] !== 'subscription') {
            return;
        }

        $subscriptionId = $data['subscription'] ?? null;

        if ($subscriptionId) {
            $this->syncSubscription($subscriptionId);
        }
    }

    protected function handlePaymentMethodAttached(array $data): void
    {
        $customerId = $data['customer'] ?? null;

        if (!$customerId) {
            return;
        }

        $customer = PaymentCustomer::findByProviderId(self::NAME, $customerId);

        if (!$customer) {
            Log::info('Payment method attached but customer not found', ['customer_id' => $customerId]);
            return;
        }

        // Check if this payment method already exists
        $existingMethod = PaymentMethod::where('provider', self::NAME)
            ->where('provider_payment_method_id', $data['id'])
            ->first();

        if ($existingMethod) {
            return;
        }

        // Determine if this should be the default
        $isFirstPaymentMethod = PaymentMethod::where('clinic_id', $customer->clinic_id)
            ->where('provider', self::NAME)
            ->doesntExist();

        PaymentMethod::create([
            'clinic_id' => $customer->clinic_id,
            'provider' => self::NAME,
            'provider_payment_method_id' => $data['id'],
            'type' => $data['type'] ?? 'card',
            'brand' => $data['card']['brand'] ?? null,
            'last_four' => $data['card']['last4'] ?? null,
            'exp_month' => $data['card']['exp_month'] ?? null,
            'exp_year' => $data['card']['exp_year'] ?? null,
            'is_default' => $isFirstPaymentMethod,
            'provider_data' => [
                'fingerprint' => $data['card']['fingerprint'] ?? null,
                'funding' => $data['card']['funding'] ?? null,
            ],
        ]);

        Log::info('Payment method synced from webhook', [
            'clinic_id' => $customer->clinic_id,
            'payment_method_id' => $data['id'],
        ]);
    }

    protected function handleCustomerUpdated(array $data): void
    {
        $customerId = $data['id'] ?? null;
        $defaultPaymentMethodId = $data['invoice_settings']['default_payment_method'] ?? null;

        if (!$customerId || !$defaultPaymentMethodId) {
            return;
        }

        $customer = PaymentCustomer::findByProviderId(self::NAME, $customerId);

        if (!$customer) {
            return;
        }

        // Update default payment method
        PaymentMethod::where('clinic_id', $customer->clinic_id)
            ->where('provider', self::NAME)
            ->update(['is_default' => false]);

        PaymentMethod::where('clinic_id', $customer->clinic_id)
            ->where('provider', self::NAME)
            ->where('provider_payment_method_id', $defaultPaymentMethodId)
            ->update(['is_default' => true]);

        Log::info('Default payment method updated from webhook', [
            'clinic_id' => $customer->clinic_id,
            'payment_method_id' => $defaultPaymentMethodId,
        ]);
    }

    protected function createLocalSubscription(
        Clinic $clinic,
        Plan $plan,
        Subscription $stripeSubscription,
        string $interval
    ): ClinicSubscription {
        $status = match ($stripeSubscription->status) {
            'trialing' => ClinicSubscription::STATUS_TRIALING,
            'active' => ClinicSubscription::STATUS_ACTIVE,
            'past_due' => ClinicSubscription::STATUS_PAST_DUE,
            'canceled' => ClinicSubscription::STATUS_CANCELED,
            'incomplete' => ClinicSubscription::STATUS_INCOMPLETE,
            default => $stripeSubscription->status,
        };

        return ClinicSubscription::create([
            'clinic_id' => $clinic->id,
            'plan_id' => $plan->id,
            'provider' => self::NAME,
            'provider_subscription_id' => $stripeSubscription->id,
            'provider_price_id' => $stripeSubscription->items->data[0]->price->id,
            'status' => $status,
            'interval' => $interval,
            'quantity' => $stripeSubscription->items->data[0]->quantity ?? 1,
            'trial_starts_at' => $stripeSubscription->trial_start
                ? \Carbon\Carbon::createFromTimestamp($stripeSubscription->trial_start)
                : null,
            'trial_ends_at' => $stripeSubscription->trial_end
                ? \Carbon\Carbon::createFromTimestamp($stripeSubscription->trial_end)
                : null,
            'current_period_start' => $stripeSubscription->current_period_start
                ? \Carbon\Carbon::createFromTimestamp($stripeSubscription->current_period_start)
                : null,
            'current_period_end' => $stripeSubscription->current_period_end
                ? \Carbon\Carbon::createFromTimestamp($stripeSubscription->current_period_end)
                : null,
            'canceled_at' => $stripeSubscription->canceled_at
                ? \Carbon\Carbon::createFromTimestamp($stripeSubscription->canceled_at)
                : null,
            'ends_at' => $stripeSubscription->cancel_at
                ? \Carbon\Carbon::createFromTimestamp($stripeSubscription->cancel_at)
                : null,
            'provider_data' => [
                'default_payment_method' => $stripeSubscription->default_payment_method,
                'latest_invoice' => $stripeSubscription->latest_invoice,
            ],
        ]);
    }

    protected function updateLocalSubscription(ClinicSubscription $subscription, Subscription $stripeSubscription): void
    {
        $status = match ($stripeSubscription->status) {
            'trialing' => ClinicSubscription::STATUS_TRIALING,
            'active' => ClinicSubscription::STATUS_ACTIVE,
            'past_due' => ClinicSubscription::STATUS_PAST_DUE,
            'canceled' => ClinicSubscription::STATUS_CANCELED,
            'incomplete' => ClinicSubscription::STATUS_INCOMPLETE,
            default => $stripeSubscription->status,
        };

        $subscription->update([
            'status' => $status,
            'provider_price_id' => $stripeSubscription->items->data[0]->price->id,
            'quantity' => $stripeSubscription->items->data[0]->quantity ?? 1,
            'trial_ends_at' => $stripeSubscription->trial_end
                ? \Carbon\Carbon::createFromTimestamp($stripeSubscription->trial_end)
                : null,
            'current_period_start' => $stripeSubscription->current_period_start
                ? \Carbon\Carbon::createFromTimestamp($stripeSubscription->current_period_start)
                : null,
            'current_period_end' => $stripeSubscription->current_period_end
                ? \Carbon\Carbon::createFromTimestamp($stripeSubscription->current_period_end)
                : null,
            'canceled_at' => $stripeSubscription->canceled_at
                ? \Carbon\Carbon::createFromTimestamp($stripeSubscription->canceled_at)
                : null,
            'ends_at' => $stripeSubscription->cancel_at
                ? \Carbon\Carbon::createFromTimestamp($stripeSubscription->cancel_at)
                : null,
            'provider_data' => [
                'default_payment_method' => $stripeSubscription->default_payment_method,
                'latest_invoice' => $stripeSubscription->latest_invoice,
            ],
        ]);
    }

    public function getInvoices(Clinic $clinic, int $limit = 24): array
    {
        $customer = $clinic->paymentCustomerFor(self::NAME);

        if (!$customer) {
            return [];
        }

        try {
            $invoices = Invoice::all([
                'customer' => $customer->provider_customer_id,
                'limit' => $limit,
                'expand' => ['data.subscription'],
            ]);

            return collect($invoices->data)->map(function ($invoice) {
                return [
                    'id' => $invoice->id,
                    'number' => $invoice->number,
                    'status' => $invoice->status,
                    'amount' => $invoice->amount_paid / 100,
                    'currency' => strtoupper($invoice->currency),
                    'date' => \Carbon\Carbon::createFromTimestamp($invoice->created),
                    'period_start' => $invoice->period_start
                        ? \Carbon\Carbon::createFromTimestamp($invoice->period_start)
                        : null,
                    'period_end' => $invoice->period_end
                        ? \Carbon\Carbon::createFromTimestamp($invoice->period_end)
                        : null,
                    'pdf_url' => $invoice->invoice_pdf,
                    'hosted_url' => $invoice->hosted_invoice_url,
                    'description' => $invoice->description ?? $invoice->lines->data[0]->description ?? 'Subscription',
                    'plan_name' => $invoice->subscription?->items?->data[0]?->price?->nickname
                        ?? $invoice->lines->data[0]->description
                        ?? 'Subscription',
                ];
            })->toArray();
        } catch (\Exception $e) {
            Log::error('Failed to fetch invoices from Stripe', [
                'clinic_id' => $clinic->id,
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }
}
