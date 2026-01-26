<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'clinic_id',
        'provider',
        'provider_payment_method_id',
        'type',
        'brand',
        'last_four',
        'exp_month',
        'exp_year',
        'is_default',
        'provider_data',
    ];

    protected $casts = [
        'provider_data' => 'array',
        'is_default' => 'boolean',
        'exp_month' => 'integer',
        'exp_year' => 'integer',
    ];

    /**
     * Get the clinic that owns this payment method.
     */
    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    /**
     * Get formatted expiration date.
     */
    public function getExpirationAttribute(): ?string
    {
        if ($this->exp_month && $this->exp_year) {
            return sprintf('%02d/%d', $this->exp_month, $this->exp_year);
        }
        return null;
    }

    /**
     * Check if card is expired.
     */
    public function isExpired(): bool
    {
        if (!$this->exp_month || !$this->exp_year) {
            return false;
        }

        $now = now();
        $expDate = now()->setYear($this->exp_year)->setMonth($this->exp_month)->endOfMonth();

        return $now->greaterThan($expDate);
    }

    /**
     * Scope to filter by provider.
     */
    public function scopeForProvider($query, string $provider)
    {
        return $query->where('provider', $provider);
    }

    /**
     * Scope to get default payment method.
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }
}
