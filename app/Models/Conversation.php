<?php

namespace App\Models;

use App\Enums\MessageChannel;
use App\Traits\BelongsToClinic;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    use HasFactory, BelongsToClinic;

    protected $fillable = [
        'clinic_id',
        'lead_id',
        'channel',
        'is_active',
        'last_message_at',
        'last_staff_reply_at',
    ];

    protected $casts = [
        'channel' => MessageChannel::class,
        'is_active' => 'boolean',
        'last_message_at' => 'datetime',
        'last_staff_reply_at' => 'datetime',
    ];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class)->orderBy('created_at', 'asc');
    }

    public function updateLastMessageTimestamp(): void
    {
        $this->update(['last_message_at' => now()]);
    }

    public function updateLastStaffReplyTimestamp(): void
    {
        $this->update(['last_staff_reply_at' => now()]);
    }
}
