<?php

namespace App\Models;

use App\Enums\OutcomeType;
use App\Traits\BelongsToClinic;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MissedCallOutcome extends Model
{
    use HasFactory, BelongsToClinic;

    protected $fillable = [
        'clinic_id',
        'lead_id',
        'outcome_type',
        'notes',
        'actual_value',
        'resolved_by_user_id',
        'resolved_at',
    ];

    protected $casts = [
        'outcome_type' => OutcomeType::class,
        'actual_value' => 'decimal:2',
        'resolved_at' => 'datetime',
    ];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function resolvedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by_user_id');
    }

    public function countsAsRecoveredRevenue(): bool
    {
        return $this->outcome_type->countsAsRecoveredRevenue();
    }

    public function getRecoveredValue(): float
    {
        if (!$this->countsAsRecoveredRevenue()) {
            return 0;
        }

        return $this->actual_value ?? $this->lead->estimated_value ?? 0;
    }
}
