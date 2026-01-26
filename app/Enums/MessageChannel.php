<?php

namespace App\Enums;

enum MessageChannel: string
{
    case SMS = 'sms';
    case WHATSAPP = 'whatsapp';
    case VOICE = 'voice';

    public function label(): string
    {
        return match ($this) {
            self::SMS => 'SMS',
            self::WHATSAPP => 'WhatsApp',
            self::VOICE => 'Voice',
        };
    }
}
