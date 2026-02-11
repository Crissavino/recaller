<?php

namespace App\Console\Commands;

use App\Models\PhoneNumber;
use Illuminate\Console\Command;

class AddPhoneToInventory extends Command
{
    protected $signature = 'phone:add
                            {phone : The phone number (e.g. +1234567890)}
                            {--provider=twilio : The SMS provider (twilio, vonage, messagebird)}
                            {--sid= : The provider SID/ID (e.g. PNxxxxx for Twilio)}
                            {--country=ES : Country code (e.g. ES, RO, US)}
                            {--cost= : Monthly cost}';

    protected $description = 'Add a phone number to Recaller inventory';

    public function handle(): int
    {
        $phone = $this->argument('phone');

        // Check if already exists
        if (PhoneNumber::where('phone_number', $phone)->exists()) {
            $this->error("Phone number {$phone} already exists in inventory.");
            return self::FAILURE;
        }

        $phoneNumber = PhoneNumber::create([
            'phone_number' => $phone,
            'provider' => $this->option('provider'),
            'provider_sid' => $this->option('sid'),
            'country_code' => strtoupper($this->option('country')),
            'monthly_cost' => $this->option('cost'),
            'status' => PhoneNumber::STATUS_AVAILABLE,
            'purchased_at' => now(),
        ]);

        $this->info("Phone number added to inventory!");
        $this->table(
            ['Field', 'Value'],
            [
                ['ID', $phoneNumber->id],
                ['Phone', $phoneNumber->phone_number],
                ['Provider', $phoneNumber->provider],
                ['Country', $phoneNumber->country_code],
                ['Status', 'Available'],
            ]
        );

        return self::SUCCESS;
    }
}
