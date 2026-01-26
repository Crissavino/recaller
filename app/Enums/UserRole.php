<?php

namespace App\Enums;

enum UserRole: string
{
    case OWNER = 'owner';
    case MANAGER = 'manager';
    case STAFF = 'staff';
    case READ_ONLY = 'read_only';

    public function label(): string
    {
        return match ($this) {
            self::OWNER => 'Owner',
            self::MANAGER => 'Manager',
            self::STAFF => 'Staff',
            self::READ_ONLY => 'Read Only',
        };
    }

    public function canManageSettings(): bool
    {
        return in_array($this, [self::OWNER, self::MANAGER]);
    }

    public function canReplyToConversations(): bool
    {
        return in_array($this, [self::OWNER, self::MANAGER, self::STAFF]);
    }

    public function canViewDashboard(): bool
    {
        return true;
    }
}
