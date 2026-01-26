<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'plan_id',
        'provider',
        'interval',
        'provider_price_id',
        'provider_product_id',
        'provider_data',
        'is_active',
    ];

    protected $casts = [
        'provider_data' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get the plan that owns this price.
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Scope to filter by provider.
     */
    public function scopeForProvider($query, string $provider)
    {
        return $query->where('provider', $provider);
    }

    /**
     * Scope to filter by interval.
     */
    public function scopeForInterval($query, string $interval)
    {
        return $query->where('interval', $interval);
    }

    /**
     * Scope to get only active prices.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Find a price by provider price ID.
     */
    public static function findByProviderPriceId(string $provider, string $priceId): ?self
    {
        return static::where('provider', $provider)
            ->where('provider_price_id', $priceId)
            ->first();
    }
}
