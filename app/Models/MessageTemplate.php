<?php

namespace App\Models;

use App\Enums\MessageChannel;
use App\Traits\BelongsToClinic;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageTemplate extends Model
{
    use HasFactory, BelongsToClinic;

    protected $fillable = [
        'clinic_id',
        'name',
        'channel',
        'trigger_event',
        'body',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'channel' => MessageChannel::class,
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];
}
