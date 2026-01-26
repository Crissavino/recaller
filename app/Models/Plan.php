<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'name',
        'description',
        'price_monthly_cents',
        'price_annual_cents',
        'features',
        'is_active',
        'is_featured',
        'sort_order',
    ];

    protected $casts = [
        'features' => 'array',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'price_monthly_cents' => 'integer',
        'price_annual_cents' => 'integer',
    ];

    /**
     * Get the prices for this plan across all providers.
     */
    public function prices(): HasMany
    {
        return $this->hasMany(PlanPrice::class);
    }

    /**
     * Get the subscriptions for this plan.
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(ClinicSubscription::class);
    }

    /**
     * Get price for a specific provider and interval.
     */
    public function getPriceFor(string $provider, string $interval): ?PlanPrice
    {
        return $this->prices()
            ->where('provider', $provider)
            ->where('interval', $interval)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Get monthly price in dollars.
     */
    public function getMonthlyPriceAttribute(): float
    {
        return $this->price_monthly_cents / 100;
    }

    /**
     * Get annual price in dollars.
     */
    public function getAnnualPriceAttribute(): float
    {
        return $this->price_annual_cents / 100;
    }

    /**
     * Get annual price per month in dollars.
     */
    public function getAnnualPricePerMonthAttribute(): float
    {
        return round($this->price_annual_cents / 12 / 100, 2);
    }

    /**
     * Scope to get only active plans.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to order by sort order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}
