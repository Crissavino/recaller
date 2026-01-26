<?php

namespace App\Models;

use App\Traits\BelongsToClinic;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MissedCall extends Model
{
    use HasFactory, BelongsToClinic;

    protected $fillable = [
        'clinic_id',
        'lead_id',
        'clinic_phone_number_id',
        'caller_phone',
        'provider_call_id',
        'ring_duration_seconds',
        'called_at',
    ];

    protected $casts = [
        'ring_duration_seconds' => 'integer',
        'called_at' => 'datetime',
    ];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function clinicPhoneNumber(): BelongsTo
    {
        return $this->belongsTo(ClinicPhoneNumber::class);
    }
}
