<?php

namespace App\Models;

use App\Traits\BelongsToClinic;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebhookEvent extends Model
{
    use HasFactory, BelongsToClinic;

    protected $fillable = [
        'clinic_id',
        'provider',
        'provider_event_id',
        'event_type',
        'payload',
        'is_processed',
        'processed_at',
        'processing_error',
    ];

    protected $casts = [
        'payload' => 'array',
        'is_processed' => 'boolean',
        'processed_at' => 'datetime',
    ];

    public function markAsProcessed(): void
    {
        $this->update([
            'is_processed' => true,
            'processed_at' => now(),
        ]);
    }

    public function markAsFailed(string $error): void
    {
        $this->update([
            'processing_error' => $error,
        ]);
    }
}
