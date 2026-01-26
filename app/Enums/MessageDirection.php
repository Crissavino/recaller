<?php

namespace App\Enums;

enum MessageDirection: string
{
    case INBOUND = 'inbound';
    case OUTBOUND = 'outbound';

    public function label(): string
    {
        return match ($this) {
            self::INBOUND => 'Inbound',
            self::OUTBOUND => 'Outbound',
        };
    }

    public function isFromPatient(): bool
    {
        return $this === self::INBOUND;
    }

    public function isFromClinic(): bool
    {
        return $this === self::OUTBOUND;
    }
}
