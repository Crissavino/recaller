<?php

namespace App\Console\Commands;

use App\Models\Clinic;
use App\Models\ClinicPhoneNumber;
use Illuminate\Console\Command;

class ListClinicNumbers extends Command
{
    protected $signature = 'clinic:numbers
                            {--clinic= : Filter by clinic ID}
                            {--country= : Filter by country code}
                            {--type= : Filter by type (voice/whatsapp)}';

    protected $description = 'List all clinic phone numbers';

    public function handle(): int
    {
        $query = ClinicPhoneNumber::with('clinic');

        if ($clinicId = $this->option('clinic')) {
            $query->where('clinic_id', $clinicId);
        }

        if ($country = $this->option('country')) {
            $query->where('country', strtoupper($country));
        }

        if ($type = $this->option('type')) {
            $query->where('type', $type);
        }

        $numbers = $query->orderBy('clinic_id')->orderBy('type')->get();

        if ($numbers->isEmpty()) {
            $this->info('No phone numbers found.');
            return 0;
        }

        $rows = $numbers->map(fn ($n) => [
            $n->id,
            $n->clinic?->name ?? 'N/A',
            $n->phone_number,
            $n->country ?? '-',
            $n->type,
            $n->is_active ? 'Yes' : 'No',
            $n->linked_whatsapp_number_id ?? '-',
        ]);

        $this->table(
            ['ID', 'Clinic', 'Phone Number', 'Country', 'Type', 'Active', 'WhatsApp Link'],
            $rows
        );

        $this->info('');
        $this->info("Total: {$numbers->count()} number(s)");

        return 0;
    }
}
