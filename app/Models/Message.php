<?php

namespace App\Models;

use App\Enums\MessageChannel;
use App\Enums\MessageDirection;
use App\Traits\BelongsToClinic;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasFactory, BelongsToClinic;

    protected $fillable = [
        'clinic_id',
        'conversation_id',
        'channel',
        'direction',
        'from_phone',
        'to_phone',
        'body',
        'provider_message_id',
        'status',
        'sent_by_user_id',
        'sent_at',
        'delivered_at',
        'read_at',
    ];

    protected $casts = [
        'channel' => MessageChannel::class,
        'direction' => MessageDirection::class,
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
        'read_at' => 'datetime',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function sentByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sent_by_user_id');
    }

    public function isInbound(): bool
    {
        return $this->direction === MessageDirection::INBOUND;
    }

    public function isOutbound(): bool
    {
        return $this->direction === MessageDirection::OUTBOUND;
    }
}
