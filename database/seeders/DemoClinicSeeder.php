<?php

namespace Database\Seeders;

use App\Enums\MessageChannel;
use App\Enums\UserRole;
use App\Models\Clinic;
use App\Models\ClinicPhoneNumber;
use App\Models\ClinicSetting;
use App\Models\Integration;
use App\Models\MessageTemplate;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoClinicSeeder extends Seeder
{
    public function run(): void
    {
        $clinic = $this->createDemoClinic();
        $owner = $this->createDemoOwner($clinic);
        $this->createClinicSettings($clinic);
        $integration = $this->createTwilioIntegration($clinic);
        $this->createDemoPhoneNumber($clinic, $integration);
        $this->createDefaultMessageTemplates($clinic);
    }

    private function createDemoClinic(): Clinic
    {
        return Clinic::create([
            'name' => 'Demo Dental Clinic',
            'slug' => 'demo-dental-clinic',
            'timezone' => 'America/New_York',
            'is_active' => true,
        ]);
    }

    private function createDemoOwner(Clinic $clinic): User
    {
        $user = User::create([
            'name' => 'Demo Owner',
            'email' => 'owner@demo-clinic.com',
            'password' => Hash::make('password'),
            'phone' => '+15551234567',
            'is_active' => true,
        ]);

        $clinic->users()->attach($user->id, ['role' => UserRole::OWNER->value]);

        return $user;
    }

    private function createClinicSettings(Clinic $clinic): ClinicSetting
    {
        return ClinicSetting::create([
            'clinic_id' => $clinic->id,
            'avg_ticket_value' => 250.00,
            'currency' => 'USD',
            'booking_link' => 'https://demo-clinic.com/book',
            'business_hours_text' => 'Mon-Fri 9am-6pm, Sat 9am-2pm',
            'followup_delay_seconds' => 60,
            'no_response_timeout_minutes' => 30,
        ]);
    }

    private function createTwilioIntegration(Clinic $clinic): Integration
    {
        return Integration::create([
            'clinic_id' => $clinic->id,
            'provider' => 'twilio',
            'credentials' => [
                'account_sid' => 'DEMO_ACCOUNT_SID',
                'auth_token' => 'DEMO_AUTH_TOKEN',
            ],
            'is_active' => true,
        ]);
    }

    private function createDemoPhoneNumber(Clinic $clinic, Integration $integration): ClinicPhoneNumber
    {
        return ClinicPhoneNumber::create([
            'clinic_id' => $clinic->id,
            'integration_id' => $integration->id,
            'phone_number' => '+15559876543',
            'provider' => 'twilio',
            'friendly_name' => 'Main Line',
            'is_active' => true,
            'voice_enabled' => true,
            'sms_enabled' => true,
        ]);
    }

    private function createDefaultMessageTemplates(Clinic $clinic): void
    {
        MessageTemplate::create([
            'clinic_id' => $clinic->id,
            'name' => 'Missed Call Follow-up',
            'channel' => MessageChannel::SMS,
            'trigger_event' => 'missed_call',
            'body' => "Hi! This is {{clinic_name}}. We noticed you called but we couldn't answer. Reply to this message or book online: {{booking_link}}. Hours: {{business_hours}}",
            'is_active' => true,
            'sort_order' => 1,
        ]);

        MessageTemplate::create([
            'clinic_id' => $clinic->id,
            'name' => 'Missed Call Follow-up (Spanish)',
            'channel' => MessageChannel::SMS,
            'trigger_event' => 'missed_call',
            'body' => "Hola! Somos {{clinic_name}}. Vimos tu llamada y no pudimos atenderte. Podes responder este mensaje o reservar aca: {{booking_link}}. Horarios: {{business_hours}}",
            'is_active' => false,
            'sort_order' => 2,
        ]);

        MessageTemplate::create([
            'clinic_id' => $clinic->id,
            'name' => 'No Response Reminder',
            'channel' => MessageChannel::SMS,
            'trigger_event' => 'no_response_reminder',
            'body' => "Hi again from {{clinic_name}}! Just checking in - would you still like to schedule an appointment? Book here: {{booking_link}}",
            'is_active' => true,
            'sort_order' => 3,
        ]);
    }
}
