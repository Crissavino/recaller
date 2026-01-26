<?php

namespace App\Models;

use App\Traits\BelongsToClinic;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClinicPhoneNumber extends Model
{
    use HasFactory, BelongsToClinic;

    protected $fillable = [
        'clinic_id',
        'integration_id',
        'phone_number',
        'provider',
        'friendly_name',
        'is_active',
        'voice_enabled',
        'sms_enabled',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'voice_enabled' => 'boolean',
        'sms_enabled' => 'boolean',
    ];

    public function integration(): BelongsTo
    {
        return $this->belongsTo(Integration::class);
    }

    public function missedCalls(): HasMany
    {
        return $this->hasMany(MissedCall::class);
    }
}
