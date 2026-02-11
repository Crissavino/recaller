<?php

namespace App\Console\Commands;

use App\Models\Clinic;
use App\Models\ClinicPhoneNumber;
use Illuminate\Console\Command;
use Twilio\Rest\Client;

class ProvisionClinicNumber extends Command
{
    protected $signature = 'clinic:provision-number
                            {clinic_id : The clinic ID to provision for}
                            {country : Country code (RO, ES, NL, etc.)}
                            {--type=voice : Number type (voice or whatsapp)}
                            {--phone= : Specific phone number to purchase (optional)}
                            {--search= : Search pattern for area code (optional)}';

    protected $description = 'Provision a Twilio phone number for a clinic';

    public function handle(): int
    {
        $clinicId = $this->argument('clinic_id');
        $country = strtoupper($this->argument('country'));
        $type = $this->option('type');
        $specificPhone = $this->option('phone');
        $searchPattern = $this->option('search');

        // Validate clinic
        $clinic = Clinic::find($clinicId);
        if (!$clinic) {
            $this->error("Clinic with ID {$clinicId} not found.");
            return 1;
        }

        $this->info("Provisioning {$type} number for: {$clinic->name}");
        $this->info("Country: {$country}");

        // Get Twilio client
        $twilio = $this->getTwilioClient();
        if (!$twilio) {
            $this->error('Twilio credentials not configured.');
            return 1;
        }

        try {
            if ($specificPhone) {
                // Purchase specific number
                $phoneNumber = $this->purchaseSpecificNumber($twilio, $specificPhone, $country);
            } else {
                // Search and purchase available number
                $phoneNumber = $this->searchAndPurchaseNumber($twilio, $country, $searchPattern);
            }

            if (!$phoneNumber) {
                $this->error('Could not provision phone number.');
                return 1;
            }

            // Create database record
            $clinicPhoneNumber = ClinicPhoneNumber::create([
                'clinic_id' => $clinicId,
                'phone_number' => $type === 'whatsapp'
                    ? 'whatsapp:' . $phoneNumber['phone_number']
                    : $phoneNumber['phone_number'],
                'country' => $country,
                'type' => $type,
                'provider' => 'twilio',
                'provider_sid' => $phoneNumber['sid'],
                'friendly_name' => $phoneNumber['friendly_name'] ?? $clinic->name,
                'is_active' => true,
                'voice_enabled' => $phoneNumber['voice_enabled'] ?? true,
                'sms_enabled' => $phoneNumber['sms_enabled'] ?? true,
            ]);

            $this->info('');
            $this->info('Number provisioned successfully!');
            $this->table(
                ['Field', 'Value'],
                [
                    ['ID', $clinicPhoneNumber->id],
                    ['Phone', $clinicPhoneNumber->phone_number],
                    ['Country', $clinicPhoneNumber->country],
                    ['Type', $clinicPhoneNumber->type],
                    ['Clinic', $clinic->name],
                    ['Provider SID', $clinicPhoneNumber->provider_sid],
                ]
            );

            // Remind to configure webhooks
            $this->warn('');
            $this->warn('Remember to configure webhooks in Twilio Console:');
            $this->line("Voice URL: " . route('webhooks.twilio.voice'));
            $this->line("Voice Status URL: " . route('webhooks.twilio.voice.status'));

            return 0;

        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return 1;
        }
    }

    private function getTwilioClient(): ?Client
    {
        $accountSid = config('services.twilio.sid');
        $authToken = config('services.twilio.token');

        if (!$accountSid || !$authToken) {
            return null;
        }

        return new Client($accountSid, $authToken);
    }

    private function searchAndPurchaseNumber(Client $twilio, string $country, ?string $searchPattern): ?array
    {
        $this->info('Searching for available numbers...');

        $options = [
            'voiceEnabled' => true,
            'smsEnabled' => true,
        ];

        if ($searchPattern) {
            $options['contains'] = $searchPattern;
        }

        try {
            // Try mobile numbers first
            $numbers = $twilio->availablePhoneNumbers($country)
                ->mobile
                ->read($options, 5);

            if (empty($numbers)) {
                // Try local numbers
                $numbers = $twilio->availablePhoneNumbers($country)
                    ->local
                    ->read($options, 5);
            }

            if (empty($numbers)) {
                $this->error("No available numbers found for country: {$country}");
                return null;
            }

            // Display available numbers
            $this->info('Available numbers:');
            foreach ($numbers as $i => $number) {
                $this->line("[{$i}] {$number->phoneNumber} - {$number->friendlyName}");
            }

            $choice = $this->ask('Select number to purchase (0-' . (count($numbers) - 1) . ')', '0');
            $selectedNumber = $numbers[(int)$choice];

            return $this->purchaseNumber($twilio, $selectedNumber->phoneNumber);

        } catch (\Exception $e) {
            $this->error('Search error: ' . $e->getMessage());
            return null;
        }
    }

    private function purchaseSpecificNumber(Client $twilio, string $phoneNumber, string $country): ?array
    {
        $this->info("Purchasing specific number: {$phoneNumber}");
        return $this->purchaseNumber($twilio, $phoneNumber);
    }

    private function purchaseNumber(Client $twilio, string $phoneNumber): ?array
    {
        $this->info("Purchasing: {$phoneNumber}");

        if (!$this->confirm('Confirm purchase?', true)) {
            return null;
        }

        $purchased = $twilio->incomingPhoneNumbers->create([
            'phoneNumber' => $phoneNumber,
            'voiceUrl' => route('webhooks.twilio.voice'),
            'voiceMethod' => 'POST',
            'statusCallback' => route('webhooks.twilio.voice.status'),
            'statusCallbackMethod' => 'POST',
            'smsUrl' => route('webhooks.twilio.sms'),
            'smsMethod' => 'POST',
        ]);

        return [
            'sid' => $purchased->sid,
            'phone_number' => $purchased->phoneNumber,
            'friendly_name' => $purchased->friendlyName,
            'voice_enabled' => $purchased->capabilities['voice'] ?? true,
            'sms_enabled' => $purchased->capabilities['sms'] ?? true,
        ];
    }
}
