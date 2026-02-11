<?php

namespace App\Console\Commands;

use App\Services\MessageBirdService;
use Illuminate\Console\Command;

class MessageBirdNumbers extends Command
{
    protected $signature = 'messagebird:numbers
                            {action=list : Action to perform (list, available, test)}
                            {--country=ES : Country code for available numbers}';

    protected $description = 'Manage MessageBird phone numbers';

    public function __construct(
        private MessageBirdService $messageBirdService,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $action = $this->argument('action');

        if (!config('services.messagebird.access_key')) {
            $this->error('MessageBird access key not configured. Set MESSAGEBIRD_ACCESS_KEY in .env');
            return self::FAILURE;
        }

        return match ($action) {
            'list' => $this->listOwnedNumbers(),
            'available' => $this->listAvailableNumbers(),
            'test' => $this->testConnection(),
            default => $this->showHelp(),
        };
    }

    private function listOwnedNumbers(): int
    {
        $this->info('Fetching owned MessageBird numbers...');

        $numbers = $this->messageBirdService->listOwnedNumbers();

        if ($numbers === null) {
            $this->error('Failed to fetch numbers. Check logs for details.');
            return self::FAILURE;
        }

        if (empty($numbers)) {
            $this->warn('No phone numbers found in your MessageBird account.');
            return self::SUCCESS;
        }

        $rows = [];
        foreach ($numbers as $number) {
            $rows[] = [
                $number['id'] ?? 'N/A',
                $number['number'] ?? 'N/A',
                $number['country'] ?? 'N/A',
                implode(', ', $number['features'] ?? []),
                $number['status'] ?? 'N/A',
            ];
        }

        $this->table(
            ['ID', 'Number', 'Country', 'Features', 'Status'],
            $rows
        );

        return self::SUCCESS;
    }

    private function listAvailableNumbers(): int
    {
        $country = strtoupper($this->option('country'));

        $this->info("Searching available numbers in {$country} with SMS+Voice...");

        $numbers = $this->messageBirdService->listAvailableNumbers($country, ['sms', 'voice']);

        if ($numbers === null) {
            $this->error('Failed to fetch available numbers. Check logs for details.');
            $this->line('');
            $this->warn('Note: SMS+Voice numbers may not be available in all countries.');
            $this->warn('Try: php artisan messagebird:numbers available --country=NL');
            return self::FAILURE;
        }

        if (empty($numbers)) {
            $this->warn("No SMS+Voice numbers available in {$country}.");
            $this->line('');
            $this->info('Tip: Request special numbers via MessageBird support.');
            return self::SUCCESS;
        }

        $rows = [];
        foreach ($numbers as $number) {
            $rows[] = [
                $number['number'] ?? 'N/A',
                $number['country'] ?? 'N/A',
                implode(', ', $number['features'] ?? []),
                $number['type'] ?? 'N/A',
                '€' . number_format($number['price']['amount'] ?? 0, 2),
            ];
        }

        $this->table(
            ['Number', 'Country', 'Features', 'Type', 'Monthly Cost'],
            $rows
        );

        return self::SUCCESS;
    }

    private function testConnection(): int
    {
        $this->info('Testing MessageBird connection...');

        // Try to list numbers as a connection test
        $numbers = $this->messageBirdService->listOwnedNumbers();

        if ($numbers === null) {
            $this->error('Connection failed! Check your MESSAGEBIRD_ACCESS_KEY.');
            return self::FAILURE;
        }

        $this->info('Connection successful!');
        $this->line('');
        $this->line('Account has ' . count($numbers) . ' phone number(s).');
        $this->line('');
        $this->line('Webhook URLs to configure in MessageBird:');
        $this->line('  Voice: ' . config('services.messagebird.voice_webhook_url'));
        $this->line('  SMS:   ' . config('services.messagebird.sms_webhook_url'));

        return self::SUCCESS;
    }

    private function showHelp(): int
    {
        $this->line('');
        $this->line('Available actions:');
        $this->line('  list      - List owned phone numbers');
        $this->line('  available - Search available numbers to purchase');
        $this->line('  test      - Test MessageBird connection');
        $this->line('');
        $this->line('Examples:');
        $this->line('  php artisan messagebird:numbers list');
        $this->line('  php artisan messagebird:numbers available --country=RO');
        $this->line('  php artisan messagebird:numbers test');

        return self::SUCCESS;
    }
}
