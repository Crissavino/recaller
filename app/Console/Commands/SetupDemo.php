<?php

namespace App\Console\Commands;

use Database\Seeders\DemoClinicSeeder;
use Database\Seeders\DummyDataSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class SetupDemo extends Command
{
    protected $signature = 'demo:setup
                            {--fresh : Run fresh migrations before seeding}
                            {--data : Include dummy data (leads, conversations, etc)}';

    protected $description = 'Set up demo environment with clinic and optional dummy data';

    public function handle(): int
    {
        $this->info('');
        $this->info('Revenue Recovery - Demo Setup');
        $this->info('==============================');
        $this->info('');

        // Fresh migrations if requested
        if ($this->option('fresh')) {
            $this->warn('Running fresh migrations...');

            if (!$this->confirm('This will DELETE ALL DATA. Continue?', false)) {
                $this->error('Aborted.');
                return 1;
            }

            Artisan::call('migrate:fresh', ['--force' => true]);
            $this->info('Migrations completed.');
            $this->info('');
        }

        // Run demo clinic seeder
        $this->info('Creating demo clinic...');

        try {
            Artisan::call('db:seed', [
                '--class' => DemoClinicSeeder::class,
                '--force' => true,
            ]);
            $this->info('Demo clinic created.');
        } catch (\Exception $e) {
            if (str_contains($e->getMessage(), 'Duplicate') || str_contains($e->getMessage(), 'Integrity constraint')) {
                $this->warn('Demo clinic already exists. Skipping...');
            } else {
                throw $e;
            }
        }

        // Run dummy data seeder if requested
        if ($this->option('data')) {
            $this->info('');
            $this->info('Creating dummy data...');

            try {
                Artisan::call('db:seed', [
                    '--class' => DummyDataSeeder::class,
                    '--force' => true,
                ]);
                $this->info('Dummy data created.');
            } catch (\Exception $e) {
                $this->error('Error creating dummy data: ' . $e->getMessage());
            }
        }

        // Summary
        $this->info('');
        $this->info('Setup Complete!');
        $this->info('===============');
        $this->info('');
        $this->table(
            ['Setting', 'Value'],
            [
                ['Clinic', 'Demo Dental Clinic'],
                ['Email', 'owner@demo-clinic.com'],
                ['Password', 'password'],
                ['Phone Number', '+15559876543'],
                ['Provider', 'twilio'],
            ]
        );

        $this->info('');
        $this->info('You can now:');
        $this->line('  1. Visit /login and sign in with the demo credentials');
        $this->line('  2. Simulate a missed call: php artisan simulate:missed-call');
        $this->info('');

        return 0;
    }
}
