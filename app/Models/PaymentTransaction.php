<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentTransaction extends Model
{
    use HasFactory;

    const TYPE_CHARGE = 'charge';
    const TYPE_REFUND = 'refund';
    const TYPE_INVOICE = 'invoice';

    const STATUS_SUCCEEDED = 'succeeded';
    const STATUS_PENDING = 'pending';
    const STATUS_FAILED = 'failed';

    protected $fillable = [
        'clinic_id',
        'clinic_subscription_id',
        'provider',
        'provider_transaction_id',
        'type',
        'status',
        'amount_cents',
        'currency',
        'description',
        'provider_data',
        'paid_at',
    ];

    protected $casts = [
        'provider_data' => 'array',
        'amount_cents' => 'integer',
        'paid_at' => 'datetime',
    ];

    /**
     * Get the clinic that owns this transaction.
     */
    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    /**
     * Get the subscription for this transaction.
     */
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(ClinicSubscription::class, 'clinic_subscription_id');
    }

    /**
     * Get amount in dollars.
     */
    public function getAmountAttribute(): float
    {
        return $this->amount_cents / 100;
    }

    /**
     * Get formatted amount with currency.
     */
    public function getFormattedAmountAttribute(): string
    {
        $symbol = match(strtoupper($this->currency)) {
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            default => $this->currency . ' ',
        };

        return $symbol . number_format($this->amount, 2);
    }

    /**
     * Check if transaction succeeded.
     */
    public function isSuccessful(): bool
    {
        return $this->status === self::STATUS_SUCCEEDED;
    }

    /**
     * Scope to filter by provider.
     */
    public function scopeForProvider($query, string $provider)
    {
        return $query->where('provider', $provider);
    }

    /**
     * Scope to get successful transactions.
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', self::STATUS_SUCCEEDED);
    }

    /**
     * Scope to filter by type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }
}
