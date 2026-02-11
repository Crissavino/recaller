<?php

namespace App\Models;

use App\Traits\BelongsToClinic;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ClinicPhoneNumber extends Model
{
    use HasFactory, BelongsToClinic;

    public const TYPE_VOICE = 'voice';
    public const TYPE_WHATSAPP = 'whatsapp';

    protected $fillable = [
        'clinic_id',
        'integration_id',
        'phone_number',
        'country',
        'type',
        'provider',
        'provider_sid',
        'friendly_name',
        'is_active',
        'voice_enabled',
        'sms_enabled',
        'forward_to_phone',
        'forward_timeout_seconds',
        'linked_whatsapp_number_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'voice_enabled' => 'boolean',
        'sms_enabled' => 'boolean',
    ];

    // Relationships

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    public function integration(): BelongsTo
    {
        return $this->belongsTo(Integration::class);
    }

    public function missedCalls(): HasMany
    {
        return $this->hasMany(MissedCall::class);
    }

    public function linkedWhatsAppNumber(): BelongsTo
    {
        return $this->belongsTo(ClinicPhoneNumber::class, 'linked_whatsapp_number_id');
    }

    // Scopes

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeVoice(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_VOICE);
    }

    public function scopeWhatsApp(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_WHATSAPP);
    }

    public function scopeForCountry(Builder $query, string $country): Builder
    {
        return $query->where('country', strtoupper($country));
    }

    // Helper Methods

    public function isVoice(): bool
    {
        return $this->type === self::TYPE_VOICE;
    }

    public function isWhatsApp(): bool
    {
        return $this->type === self::TYPE_WHATSAPP;
    }

    /**
     * Get the WhatsApp number linked to this voice number.
     * If no explicit link, find the clinic's WhatsApp number.
     */
    public function getWhatsAppNumber(): ?self
    {
        if ($this->linked_whatsapp_number_id) {
            return $this->linkedWhatsAppNumber;
        }

        return self::where('clinic_id', $this->clinic_id)
            ->where('type', self::TYPE_WHATSAPP)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Get the clean phone number without whatsapp: prefix.
     */
    public function getCleanPhoneNumber(): string
    {
        return str_replace('whatsapp:', '', $this->phone_number);
    }

    /**
     * Find a clinic phone number by any format of the phone number.
     */
    public static function findByPhoneNumber(string $phoneNumber): ?self
    {
        $normalized = ltrim($phoneNumber, '+');
        $withPlus = '+' . $normalized;
        $withWhatsApp = 'whatsapp:' . $withPlus;

        return self::where('is_active', true)
            ->where(function ($query) use ($phoneNumber, $normalized, $withPlus, $withWhatsApp) {
                $query->where('phone_number', $phoneNumber)
                    ->orWhere('phone_number', $normalized)
                    ->orWhere('phone_number', $withPlus)
                    ->orWhere('phone_number', $withWhatsApp);
            })
            ->first();
    }

    /**
     * Get the primary voice number for a clinic.
     */
    public static function getPrimaryVoiceForClinic(int $clinicId): ?self
    {
        return self::where('clinic_id', $clinicId)
            ->where('type', self::TYPE_VOICE)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Get the WhatsApp number for a clinic.
     */
    public static function getWhatsAppForClinic(int $clinicId): ?self
    {
        return self::where('clinic_id', $clinicId)
            ->where('type', self::TYPE_WHATSAPP)
            ->where('is_active', true)
            ->first();
    }
}
