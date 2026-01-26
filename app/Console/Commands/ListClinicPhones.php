<?php

namespace App\Console\Commands;

use App\Models\Clinic;
use Illuminate\Console\Command;

class ListClinicPhones extends Command
{
    protected $signature = 'clinic:list';

    protected $description = 'List all clinics and their assigned phone numbers';

    public function handle(): int
    {
        $clinics = Clinic::with(['phoneNumber', 'users'])->get();

        if ($clinics->isEmpty()) {
            $this->warn('No clinics found.');
            return self::SUCCESS;
        }

        $rows = $clinics->map(fn ($clinic) => [
            $clinic->id,
            $clinic->name,
            $clinic->users->where('pivot.role', 'owner')->first()?->email ?? 'N/A',
            $clinic->phoneNumber?->phone_number ?? '<fg=yellow>Not assigned</>',
            $clinic->hasCompletedSetup() ? '<fg=green>Yes</>' : '<fg=red>No</>',
            $clinic->hasActiveSubscription() ? '<fg=green>Active</>' : '<fg=red>Inactive</>',
        ]);

        $this->table(
            ['ID', 'Clinic', 'Owner', 'Phone', 'Setup', 'Subscription'],
            $rows
        );

        return self::SUCCESS;
    }
}
