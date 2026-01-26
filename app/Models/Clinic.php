<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Cashier\Billable;

class Clinic extends Model
{
    use HasFactory, SoftDeletes, Billable;

    protected $fillable = [
        'name',
        'slug',
        'timezone',
        'is_active',
        'setup_completed_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'trial_ends_at' => 'datetime',
        'setup_completed_at' => 'datetime',
    ];

    /**
     * Check if the clinic has completed the setup wizard.
     */
    public function hasCompletedSetup(): bool
    {
        return $this->setup_completed_at !== null;
    }

    /**
     * Mark the setup wizard as completed.
     */
    public function markSetupCompleted(): void
    {
        $this->update(['setup_completed_at' => now()]);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    public function settings(): HasOne
    {
        return $this->hasOne(ClinicSetting::class);
    }

    public function integrations(): HasMany
    {
        return $this->hasMany(Integration::class);
    }

    public function phoneNumbers(): HasMany
    {
        return $this->hasMany(ClinicPhoneNumber::class);
    }

    /**
     * Get the assigned phone number for this clinic.
     */
    public function phoneNumber(): HasOne
    {
        return $this->hasOne(PhoneNumber::class)->where('status', PhoneNumber::STATUS_ASSIGNED);
    }

    public function webhookEvents(): HasMany
    {
        return $this->hasMany(WebhookEvent::class);
    }

    public function callers(): HasMany
    {
        return $this->hasMany(Caller::class);
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class);
    }

    public function missedCalls(): HasMany
    {
        return $this->hasMany(MissedCall::class);
    }

    public function messageTemplates(): HasMany
    {
        return $this->hasMany(MessageTemplate::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function outcomes(): HasMany
    {
        return $this->hasMany(MissedCallOutcome::class);
    }

    // Payment-related relationships

    public function paymentCustomers(): HasMany
    {
        return $this->hasMany(PaymentCustomer::class);
    }

    public function paymentMethods(): HasMany
    {
        return $this->hasMany(PaymentMethod::class);
    }

    public function clinicSubscriptions(): HasMany
    {
        return $this->hasMany(ClinicSubscription::class);
    }

    public function paymentTransactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    /**
     * Get the active subscription for a specific provider.
     */
    public function activeSubscription(?string $provider = null): ?ClinicSubscription
    {
        $query = $this->clinicSubscriptions()->active();

        if ($provider) {
            $query->where('provider', $provider);
        }

        return $query->first();
    }

    /**
     * Check if clinic has an active subscription.
     */
    public function hasActiveSubscription(?string $provider = null): bool
    {
        return $this->activeSubscription($provider) !== null;
    }

    /**
     * Get the payment customer for a specific provider.
     */
    public function paymentCustomerFor(string $provider): ?PaymentCustomer
    {
        return $this->paymentCustomers()->where('provider', $provider)->first();
    }

    /**
     * Get the default payment method for a specific provider.
     */
    public function defaultPaymentMethodFor(string $provider): ?PaymentMethod
    {
        return $this->paymentMethods()
            ->where('provider', $provider)
            ->where('is_default', true)
            ->first();
    }

    /**
     * Check if the clinic's trial has expired without an active subscription.
     */
    public function hasExpiredTrial(): bool
    {
        $subscription = $this->clinicSubscriptions()->latest()->first();

        if (!$subscription) {
            return false;
        }

        // Trial expired if status is trialing but trial_ends_at is past
        if ($subscription->status === ClinicSubscription::STATUS_TRIALING
            && $subscription->trial_ends_at
            && $subscription->trial_ends_at->isPast()) {
            return true;
        }

        // Also check for canceled/incomplete status after trial
        if (in_array($subscription->status, [
            ClinicSubscription::STATUS_CANCELED,
            ClinicSubscription::STATUS_INCOMPLETE,
            ClinicSubscription::STATUS_PAST_DUE,
        ])) {
            return true;
        }

        return false;
    }

    /**
     * Check if clinic is currently on an active trial.
     */
    public function isOnTrial(): bool
    {
        $subscription = $this->activeSubscription();
        return $subscription?->onTrial() ?? false;
    }

    /**
     * Get trial days remaining.
     */
    public function trialDaysRemaining(): ?int
    {
        $subscription = $this->activeSubscription();

        if (!$subscription || !$subscription->onTrial()) {
            return null;
        }

        return (int) now()->diffInDays($subscription->trial_ends_at, false);
    }
}
