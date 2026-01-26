<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClinicSubscription extends Model
{
    use HasFactory;

    const STATUS_ACTIVE = 'active';
    const STATUS_CANCELED = 'canceled';
    const STATUS_PAST_DUE = 'past_due';
    const STATUS_TRIALING = 'trialing';
    const STATUS_PAUSED = 'paused';
    const STATUS_INCOMPLETE = 'incomplete';

    protected $fillable = [
        'clinic_id',
        'plan_id',
        'provider',
        'provider_subscription_id',
        'provider_price_id',
        'status',
        'interval',
        'quantity',
        'trial_starts_at',
        'trial_ends_at',
        'current_period_start',
        'current_period_end',
        'canceled_at',
        'ends_at',
        'provider_data',
    ];

    protected $casts = [
        'provider_data' => 'array',
        'trial_starts_at' => 'datetime',
        'trial_ends_at' => 'datetime',
        'current_period_start' => 'datetime',
        'current_period_end' => 'datetime',
        'canceled_at' => 'datetime',
        'ends_at' => 'datetime',
        'quantity' => 'integer',
    ];

    /**
     * Get the clinic that owns this subscription.
     */
    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    /**
     * Get the plan for this subscription.
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Get the transactions for this subscription.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    /**
     * Check if subscription is active.
     */
    public function isActive(): bool
    {
        return in_array($this->status, [self::STATUS_ACTIVE, self::STATUS_TRIALING]);
    }

    /**
     * Check if subscription is on trial.
     */
    public function onTrial(): bool
    {
        return $this->status === self::STATUS_TRIALING
            && $this->trial_ends_at
            && $this->trial_ends_at->isFuture();
    }

    /**
     * Check if subscription is canceled but still active (grace period).
     */
    public function onGracePeriod(): bool
    {
        return $this->canceled_at !== null
            && $this->ends_at !== null
            && $this->ends_at->isFuture();
    }

    /**
     * Check if subscription has ended.
     */
    public function hasEnded(): bool
    {
        return $this->ends_at !== null && $this->ends_at->isPast();
    }

    /**
     * Check if subscription is past due.
     */
    public function isPastDue(): bool
    {
        return $this->status === self::STATUS_PAST_DUE;
    }

    /**
     * Scope to filter by provider.
     */
    public function scopeForProvider($query, string $provider)
    {
        return $query->where('provider', $provider);
    }

    /**
     * Scope to get active subscriptions.
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', [self::STATUS_ACTIVE, self::STATUS_TRIALING]);
    }

    /**
     * Scope to get recurring (not canceled) subscriptions.
     */
    public function scopeRecurring($query)
    {
        return $query->whereNull('canceled_at');
    }

    /**
     * Find by provider subscription ID.
     */
    public static function findByProviderId(string $provider, string $subscriptionId): ?self
    {
        return static::where('provider', $provider)
            ->where('provider_subscription_id', $subscriptionId)
            ->first();
    }
}
