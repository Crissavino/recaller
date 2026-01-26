<?php

namespace App\Console\Commands;

use App\Models\PhoneNumber;
use Illuminate\Console\Command;

class ListPhoneInventory extends Command
{
    protected $signature = 'phone:list
                            {--status= : Filter by status (available, assigned, reserved, released)}
                            {--country= : Filter by country code}';

    protected $description = 'List all phone numbers in Recaller inventory';

    public function handle(): int
    {
        $query = PhoneNumber::with('clinic');

        if ($status = $this->option('status')) {
            $query->where('status', $status);
        }

        if ($country = $this->option('country')) {
            $query->where('country_code', strtoupper($country));
        }

        $phones = $query->orderBy('status')->orderBy('country_code')->get();

        if ($phones->isEmpty()) {
            $this->warn('No phone numbers found.');
            return self::SUCCESS;
        }

        $rows = $phones->map(fn ($p) => [
            $p->id,
            $p->phone_number,
            $p->country_code,
            $p->provider,
            $this->formatStatus($p->status),
            $p->clinic?->name ?? '-',
        ]);

        $this->table(
            ['ID', 'Phone', 'Country', 'Provider', 'Status', 'Assigned To'],
            $rows
        );

        // Summary
        $this->newLine();
        $this->info("Total: {$phones->count()} | " .
            "Available: {$phones->where('status', 'available')->count()} | " .
            "Assigned: {$phones->where('status', 'assigned')->count()}");

        return self::SUCCESS;
    }

    private function formatStatus(string $status): string
    {
        return match ($status) {
            'available' => '<fg=green>Available</>',
            'assigned' => '<fg=blue>Assigned</>',
            'reserved' => '<fg=yellow>Reserved</>',
            'released' => '<fg=gray>Released</>',
            default => $status,
        };
    }
}
