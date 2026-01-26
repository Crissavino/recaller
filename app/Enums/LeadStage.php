<?php

namespace App\Enums;

enum LeadStage: string
{
    case NEW = 'new';
    case CONTACTED = 'contacted';
    case RESPONDED = 'responded';
    case BOOKED = 'booked';
    case LOST = 'lost';

    public function label(): string
    {
        return match ($this) {
            self::NEW => 'New',
            self::CONTACTED => 'Contacted',
            self::RESPONDED => 'Responded',
            self::BOOKED => 'Booked',
            self::LOST => 'Lost',
        };
    }

    public function isOpen(): bool
    {
        return in_array($this, [self::NEW, self::CONTACTED, self::RESPONDED]);
    }

    public function isClosed(): bool
    {
        return in_array($this, [self::BOOKED, self::LOST]);
    }

    public function isWon(): bool
    {
        return $this === self::BOOKED;
    }
}
