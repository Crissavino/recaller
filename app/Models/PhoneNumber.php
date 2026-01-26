<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PhoneNumber extends Model
{
    use HasFactory;

    const STATUS_AVAILABLE = 'available';
    const STATUS_ASSIGNED = 'assigned';
    const STATUS_RESERVED = 'reserved';
    const STATUS_RELEASED = 'released';

    protected $fillable = [
        'phone_number',
        'provider',
        'provider_sid',
        'friendly_name',
        'country_code',
        'clinic_id',
        'status',
        'voice_enabled',
        'sms_enabled',
        'monthly_cost',
        'purchased_at',
        'assigned_at',
    ];

    protected $casts = [
        'voice_enabled' => 'boolean',
        'sms_enabled' => 'boolean',
        'monthly_cost' => 'decimal:2',
        'purchased_at' => 'datetime',
        'assigned_at' => 'datetime',
    ];

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    public function isAvailable(): bool
    {
        return $this->status === self::STATUS_AVAILABLE;
    }

    public function isAssigned(): bool
    {
        return $this->status === self::STATUS_ASSIGNED;
    }

    public function assignTo(Clinic $clinic): void
    {
        $this->update([
            'clinic_id' => $clinic->id,
            'status' => self::STATUS_ASSIGNED,
            'assigned_at' => now(),
        ]);
    }

    public function unassign(): void
    {
        $this->update([
            'clinic_id' => null,
            'status' => self::STATUS_AVAILABLE,
            'assigned_at' => null,
        ]);
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', self::STATUS_AVAILABLE);
    }

    public function scopeAssigned($query)
    {
        return $query->where('status', self::STATUS_ASSIGNED);
    }

    public function scopeForCountry($query, string $countryCode)
    {
        return $query->where('country_code', $countryCode);
    }
}
