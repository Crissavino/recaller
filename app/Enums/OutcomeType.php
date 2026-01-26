<?php

namespace App\Enums;

enum OutcomeType: string
{
    case BOOKED = 'booked';
    case CALLBACK_REQUESTED = 'callback_requested';
    case NOT_INTERESTED = 'not_interested';
    case WRONG_NUMBER = 'wrong_number';
    case NO_RESPONSE = 'no_response';
    case NEEDS_MANUAL_CALL = 'needs_manual_call';

    public function label(): string
    {
        return match ($this) {
            self::BOOKED => 'Booked',
            self::CALLBACK_REQUESTED => 'Callback Requested',
            self::NOT_INTERESTED => 'Not Interested',
            self::WRONG_NUMBER => 'Wrong Number',
            self::NO_RESPONSE => 'No Response',
            self::NEEDS_MANUAL_CALL => 'Needs Manual Call',
        };
    }

    public function isPositive(): bool
    {
        return in_array($this, [self::BOOKED, self::CALLBACK_REQUESTED]);
    }

    public function countsAsRecoveredRevenue(): bool
    {
        return $this === self::BOOKED;
    }
}
