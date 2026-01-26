<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentCustomer extends Model
{
    use HasFactory;

    protected $fillable = [
        'clinic_id',
        'provider',
        'provider_customer_id',
        'email',
        'name',
        'provider_data',
    ];

    protected $casts = [
        'provider_data' => 'array',
    ];

    /**
     * Get the clinic that owns this payment customer.
     */
    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    /**
     * Scope to filter by provider.
     */
    public function scopeForProvider($query, string $provider)
    {
        return $query->where('provider', $provider);
    }

    /**
     * Find by provider customer ID.
     */
    public static function findByProviderId(string $provider, string $customerId): ?self
    {
        return static::where('provider', $provider)
            ->where('provider_customer_id', $customerId)
            ->first();
    }
}
