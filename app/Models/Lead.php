<?php

namespace App\Models;

use App\Enums\LeadOrigin;
use App\Enums\LeadStage;
use App\Traits\BelongsToClinic;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Lead extends Model
{
    use HasFactory, BelongsToClinic;

    protected $fillable = [
        'clinic_id',
        'caller_id',
        'origin',
        'stage',
        'estimated_value',
        'assigned_to',
        'metadata',
    ];

    protected $casts = [
        'origin' => LeadOrigin::class,
        'stage' => LeadStage::class,
        'estimated_value' => 'decimal:2',
        'metadata' => 'array',
    ];

    public function caller(): BelongsTo
    {
        return $this->belongsTo(Caller::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function conversation(): HasOne
    {
        return $this->hasOne(Conversation::class);
    }

    public function missedCall(): HasOne
    {
        return $this->hasOne(MissedCall::class);
    }

    public function outcome(): HasOne
    {
        return $this->hasOne(MissedCallOutcome::class);
    }

    public function transitionTo(LeadStage $newStage): void
    {
        $this->update(['stage' => $newStage]);
    }

    public function isOpen(): bool
    {
        return $this->stage->isOpen();
    }

    public function isClosed(): bool
    {
        return $this->stage->isClosed();
    }
}
