<?php

namespace App\UseCases\Messaging;

use App\Models\Clinic;
use App\Models\MessageTemplate;

class RenderMessageTemplate
{
    public function execute(MessageTemplate $template, Clinic $clinic, ?string $callerPhone = null): string
    {
        $variables = $this->buildVariables($clinic, $callerPhone);

        return $this->replaceVariables($template->body, $variables);
    }

    private function buildVariables(Clinic $clinic, ?string $callerPhone): array
    {
        $settings = $clinic->settings;

        return [
            '{{clinic_name}}' => $clinic->name,
            '{{booking_link}}' => $settings?->booking_link ?? '',
            '{{business_hours}}' => $settings?->business_hours_text ?? '',
            '{{caller_phone}}' => $this->formatPhone($callerPhone),
        ];
    }

    private function formatPhone(?string $phone): string
    {
        if (!$phone) {
            return '';
        }

        // Format E.164 phone to readable format (e.g., +1234567890 -> (123) 456-7890)
        if (preg_match('/^\+1(\d{3})(\d{3})(\d{4})$/', $phone, $matches)) {
            return "({$matches[1]}) {$matches[2]}-{$matches[3]}";
        }

        return $phone;
    }

    private function replaceVariables(string $body, array $variables): string
    {
        return str_replace(
            array_keys($variables),
            array_values($variables),
            $body
        );
    }
}
