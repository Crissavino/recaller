<?php

namespace App\Models;

use App\Traits\BelongsToClinic;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClinicSetting extends Model
{
    use HasFactory, BelongsToClinic;

    protected $fillable = [
        'clinic_id',
        'avg_ticket_value',
        'currency',
        'booking_link',
        'business_hours_text',
        'followup_delay_seconds',
        'no_response_timeout_minutes',
    ];

    protected $casts = [
        'avg_ticket_value' => 'decimal:2',
        'followup_delay_seconds' => 'integer',
        'no_response_timeout_minutes' => 'integer',
    ];
}
