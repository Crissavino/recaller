<?php

namespace App\Console\Commands;

use App\Models\Clinic;
use App\Models\ClinicPhoneNumber;
use Illuminate\Console\Command;

class AssignClinicNumber extends Command
{
    protected $signature = 'clinic:assign-number
                            {clinic_id : The clinic ID}
                            {phone_number : The phone number to assign}
                            {--country= : Country code (RO, ES, NL)}
                            {--type=voice : Number type (voice or whatsapp)}
                            {--name= : Friendly name}
                            {--link-whatsapp= : Link to WhatsApp number ID}';

    protected $description = 'Manually assign a phone number to a clinic (for numbers already in Twilio)';

    public function handle(): int
    {
        $clinicId = $this->argument('clinic_id');
        $phoneNumber = $this->argument('phone_number');
        $country = $this->option('country');
        $type = $this->option('type');
        $friendlyName = $this->option('name');
        $linkWhatsApp = $this->option('link-whatsapp');

        // Validate clinic
        $clinic = Clinic::find($clinicId);
        if (!$clinic) {
            $this->error("Clinic with ID {$clinicId} not found.");
            return 1;
        }

        // Check if number already exists
        $existing = ClinicPhoneNumber::where('phone_number', $phoneNumber)->first();
        if ($existing) {
            $this->error("Phone number {$phoneNumber} is already assigned to clinic ID {$existing->clinic_id}.");
            return 1;
        }

        // Format for WhatsApp if needed
        if ($type === 'whatsapp' && !str_starts_with($phoneNumber, 'whatsapp:')) {
            $phoneNumber = 'whatsapp:' . $phoneNumber;
        }

        // Create the record
        $clinicPhoneNumber = ClinicPhoneNumber::create([
            'clinic_id' => $clinicId,
            'phone_number' => $phoneNumber,
            'country' => $country ? strtoupper($country) : null,
            'type' => $type,
            'provider' => 'twilio',
            'friendly_name' => $friendlyName ?? $clinic->name,
            'is_active' => true,
            'voice_enabled' => $type === 'voice',
            'sms_enabled' => true,
            'linked_whatsapp_number_id' => $linkWhatsApp,
        ]);

        $this->info('Number assigned successfully!');
        $this->table(
            ['Field', 'Value'],
            [
                ['ID', $clinicPhoneNumber->id],
                ['Phone', $clinicPhoneNumber->phone_number],
                ['Country', $clinicPhoneNumber->country ?? '-'],
                ['Type', $clinicPhoneNumber->type],
                ['Clinic', $clinic->name],
                ['WhatsApp Link', $clinicPhoneNumber->linked_whatsapp_number_id ?? '-'],
            ]
        );

        return 0;
    }
}
