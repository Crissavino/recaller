<?php

namespace App\Enums;

enum LeadOrigin: string
{
    case MISSED_CALL = 'missed_call';
    case WEB_FORM = 'web_form';
    case CHAT_WIDGET = 'chat_widget';
    case WHATSAPP_INBOUND = 'whatsapp_inbound';
    case MANUAL = 'manual';

    public function label(): string
    {
        return match ($this) {
            self::MISSED_CALL => 'Missed Call',
            self::WEB_FORM => 'Web Form',
            self::CHAT_WIDGET => 'Chat Widget',
            self::WHATSAPP_INBOUND => 'WhatsApp Inbound',
            self::MANUAL => 'Manual Entry',
        };
    }
}
