<?php

namespace App\Console\Commands;

use App\Models\Clinic;
use App\Models\PhoneNumber;
use Illuminate\Console\Command;

class AssignPhoneToClinic extends Command
{
    protected $signature = 'clinic:assign-phone
                            {clinic_id : The clinic ID}
                            {phone_id : The phone number ID from our inventory}';

    protected $description = 'Assign a phone number from inventory to a clinic';

    public function handle(): int
    {
        $clinicId = $this->argument('clinic_id');
        $phoneId = $this->argument('phone_id');

        $clinic = Clinic::find($clinicId);
        if (!$clinic) {
            $this->error("Clinic with ID {$clinicId} not found.");
            return self::FAILURE;
        }

        $phone = PhoneNumber::find($phoneId);
        if (!$phone) {
            $this->error("Phone number with ID {$phoneId} not found.");
            return self::FAILURE;
        }

        if (!$phone->isAvailable()) {
            if ($phone->clinic_id === (int) $clinicId) {
                $this->warn("This phone is already assigned to this clinic.");
                return self::SUCCESS;
            }
            $this->error("This phone number is not available (status: {$phone->status}).");
            return self::FAILURE;
        }

        // Unassign any existing phone from this clinic
        PhoneNumber::where('clinic_id', $clinicId)
            ->where('status', PhoneNumber::STATUS_ASSIGNED)
            ->each(fn ($p) => $p->unassign());

        // Assign the new phone
        $phone->assignTo($clinic);

        $this->info("Phone number assigned successfully!");
        $this->table(
            ['Field', 'Value'],
            [
                ['Clinic', $clinic->name],
                ['Phone', $phone->phone_number],
                ['Provider', $phone->provider],
                ['Status', 'Assigned'],
            ]
        );

        return self::SUCCESS;
    }
}
