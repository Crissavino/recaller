<?php

namespace Database\Seeders;

use App\Enums\LeadOrigin;
use App\Enums\LeadStage;
use App\Enums\MessageChannel;
use App\Enums\MessageDirection;
use App\Enums\OutcomeType;
use App\Models\Caller;
use App\Models\Clinic;
use App\Models\Conversation;
use App\Models\Lead;
use App\Models\Message;
use App\Models\MissedCall;
use App\Models\MissedCallOutcome;
use App\Models\User;
use Illuminate\Database\Seeder;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        $clinic = Clinic::where('slug', 'demo-dental-clinic')->first();
        $owner = User::where('email', 'owner@demo-clinic.com')->first();
        $phoneNumber = $clinic->phoneNumbers()->first();

        if (!$clinic || !$owner || !$phoneNumber) {
            $this->command->error('Run DemoClinicSeeder first!');
            return;
        }

        // Lead 1: Booked today (success story)
        $this->createBookedLead($clinic, $owner, $phoneNumber);

        // Lead 2: Responded, waiting for staff
        $this->createRespondedLead($clinic, $phoneNumber);

        // Lead 3: Contacted, no response yet
        $this->createContactedLead($clinic, $phoneNumber);

        // Lead 4: New, just came in
        $this->createNewLead($clinic, $phoneNumber);

        // Lead 5: Another responded
        $this->createAnotherRespondedLead($clinic, $phoneNumber);

        // Lead 6: Lost (not interested)
        $this->createLostLead($clinic, $owner, $phoneNumber);

        // Lead 7: Booked yesterday
        $this->createYesterdayBookedLead($clinic, $owner, $phoneNumber);

        // Lead 8: New from 5 minutes ago
        $this->createRecentNewLead($clinic, $phoneNumber);

        // Historical data for monthly comparison
        $this->createLastMonthBookedLeads($clinic, $owner, $phoneNumber);

        $this->command->info('Dummy data created successfully!');
    }

    private function createBookedLead(Clinic $clinic, User $owner, $phoneNumber): void
    {
        $caller = Caller::create([
            'clinic_id' => $clinic->id,
            'phone' => '+15551234001',
            'name' => 'Maria Garcia',
        ]);

        $lead = Lead::create([
            'clinic_id' => $clinic->id,
            'caller_id' => $caller->id,
            'origin' => LeadOrigin::MISSED_CALL,
            'stage' => LeadStage::BOOKED,
            'estimated_value' => 250.00,
            'created_at' => now()->subHours(3),
        ]);

        MissedCall::create([
            'clinic_id' => $clinic->id,
            'lead_id' => $lead->id,
            'clinic_phone_number_id' => $phoneNumber->id,
            'caller_phone' => $caller->phone,
            'called_at' => now()->subHours(3),
        ]);

        $conversation = Conversation::create([
            'clinic_id' => $clinic->id,
            'lead_id' => $lead->id,
            'channel' => MessageChannel::SMS,
            'is_active' => false,
            'last_message_at' => now()->subHours(1),
            'last_staff_reply_at' => now()->subHours(1),
        ]);

        Message::create([
            'clinic_id' => $clinic->id,
            'conversation_id' => $conversation->id,
            'channel' => MessageChannel::SMS,
            'direction' => MessageDirection::OUTBOUND,
            'from_phone' => $phoneNumber->phone_number,
            'to_phone' => $caller->phone,
            'body' => "Hi! This is Demo Dental Clinic. We noticed you called but we couldn't answer. Reply to this message or book online: https://demo-clinic.com/book",
            'status' => 'delivered',
            'sent_at' => now()->subHours(3),
            'created_at' => now()->subHours(3),
        ]);

        Message::create([
            'clinic_id' => $clinic->id,
            'conversation_id' => $conversation->id,
            'channel' => MessageChannel::SMS,
            'direction' => MessageDirection::INBOUND,
            'from_phone' => $caller->phone,
            'to_phone' => $phoneNumber->phone_number,
            'body' => "Hi! Yes I was trying to book an appointment for a cleaning. Do you have anything available this week?",
            'status' => 'received',
            'created_at' => now()->subHours(2)->subMinutes(30),
        ]);

        Message::create([
            'clinic_id' => $clinic->id,
            'conversation_id' => $conversation->id,
            'channel' => MessageChannel::SMS,
            'direction' => MessageDirection::OUTBOUND,
            'from_phone' => $phoneNumber->phone_number,
            'to_phone' => $caller->phone,
            'body' => "Hi Maria! Yes, we have availability Thursday at 2pm or Friday at 10am. Which works better for you?",
            'status' => 'delivered',
            'sent_by_user_id' => $owner->id,
            'sent_at' => now()->subHours(2),
            'created_at' => now()->subHours(2),
        ]);

        Message::create([
            'clinic_id' => $clinic->id,
            'conversation_id' => $conversation->id,
            'channel' => MessageChannel::SMS,
            'direction' => MessageDirection::INBOUND,
            'from_phone' => $caller->phone,
            'to_phone' => $phoneNumber->phone_number,
            'body' => "Thursday at 2pm works perfect!",
            'status' => 'received',
            'created_at' => now()->subHours(1)->subMinutes(30),
        ]);

        Message::create([
            'clinic_id' => $clinic->id,
            'conversation_id' => $conversation->id,
            'channel' => MessageChannel::SMS,
            'direction' => MessageDirection::OUTBOUND,
            'from_phone' => $phoneNumber->phone_number,
            'to_phone' => $caller->phone,
            'body' => "Great! You're all set for Thursday at 2pm. See you then!",
            'status' => 'delivered',
            'sent_by_user_id' => $owner->id,
            'sent_at' => now()->subHours(1),
            'created_at' => now()->subHours(1),
        ]);

        MissedCallOutcome::create([
            'clinic_id' => $clinic->id,
            'lead_id' => $lead->id,
            'outcome_type' => OutcomeType::BOOKED,
            'actual_value' => 275.00,
            'notes' => 'Cleaning appointment Thursday 2pm',
            'resolved_by_user_id' => $owner->id,
            'resolved_at' => now()->subHours(1),
        ]);
    }

    private function createRespondedLead(Clinic $clinic, $phoneNumber): void
    {
        $caller = Caller::create([
            'clinic_id' => $clinic->id,
            'phone' => '+15551234002',
        ]);

        $lead = Lead::create([
            'clinic_id' => $clinic->id,
            'caller_id' => $caller->id,
            'origin' => LeadOrigin::MISSED_CALL,
            'stage' => LeadStage::RESPONDED,
            'estimated_value' => 250.00,
            'created_at' => now()->subMinutes(45),
        ]);

        MissedCall::create([
            'clinic_id' => $clinic->id,
            'lead_id' => $lead->id,
            'clinic_phone_number_id' => $phoneNumber->id,
            'caller_phone' => $caller->phone,
            'called_at' => now()->subMinutes(45),
        ]);

        $conversation = Conversation::create([
            'clinic_id' => $clinic->id,
            'lead_id' => $lead->id,
            'channel' => MessageChannel::SMS,
            'is_active' => true,
            'last_message_at' => now()->subMinutes(15),
        ]);

        Message::create([
            'clinic_id' => $clinic->id,
            'conversation_id' => $conversation->id,
            'channel' => MessageChannel::SMS,
            'direction' => MessageDirection::OUTBOUND,
            'from_phone' => $phoneNumber->phone_number,
            'to_phone' => $caller->phone,
            'body' => "Hi! This is Demo Dental Clinic. We noticed you called but we couldn't answer. Reply to this message or book online: https://demo-clinic.com/book",
            'status' => 'delivered',
            'sent_at' => now()->subMinutes(44),
            'created_at' => now()->subMinutes(44),
        ]);

        Message::create([
            'clinic_id' => $clinic->id,
            'conversation_id' => $conversation->id,
            'channel' => MessageChannel::SMS,
            'direction' => MessageDirection::INBOUND,
            'from_phone' => $caller->phone,
            'to_phone' => $phoneNumber->phone_number,
            'body' => "Hello, I need to schedule a root canal. My dentist referred me. How much does it cost?",
            'status' => 'received',
            'created_at' => now()->subMinutes(15),
        ]);
    }

    private function createContactedLead(Clinic $clinic, $phoneNumber): void
    {
        $caller = Caller::create([
            'clinic_id' => $clinic->id,
            'phone' => '+15551234003',
        ]);

        $lead = Lead::create([
            'clinic_id' => $clinic->id,
            'caller_id' => $caller->id,
            'origin' => LeadOrigin::MISSED_CALL,
            'stage' => LeadStage::CONTACTED,
            'estimated_value' => 250.00,
            'created_at' => now()->subHours(2),
        ]);

        MissedCall::create([
            'clinic_id' => $clinic->id,
            'lead_id' => $lead->id,
            'clinic_phone_number_id' => $phoneNumber->id,
            'caller_phone' => $caller->phone,
            'called_at' => now()->subHours(2),
        ]);

        $conversation = Conversation::create([
            'clinic_id' => $clinic->id,
            'lead_id' => $lead->id,
            'channel' => MessageChannel::SMS,
            'is_active' => true,
            'last_message_at' => now()->subHours(2),
        ]);

        Message::create([
            'clinic_id' => $clinic->id,
            'conversation_id' => $conversation->id,
            'channel' => MessageChannel::SMS,
            'direction' => MessageDirection::OUTBOUND,
            'from_phone' => $phoneNumber->phone_number,
            'to_phone' => $caller->phone,
            'body' => "Hi! This is Demo Dental Clinic. We noticed you called but we couldn't answer. Reply to this message or book online: https://demo-clinic.com/book",
            'status' => 'delivered',
            'sent_at' => now()->subHours(2),
            'created_at' => now()->subHours(2),
        ]);
    }

    private function createNewLead(Clinic $clinic, $phoneNumber): void
    {
        $caller = Caller::create([
            'clinic_id' => $clinic->id,
            'phone' => '+15551234004',
        ]);

        $lead = Lead::create([
            'clinic_id' => $clinic->id,
            'caller_id' => $caller->id,
            'origin' => LeadOrigin::MISSED_CALL,
            'stage' => LeadStage::NEW,
            'estimated_value' => 250.00,
            'created_at' => now()->subMinutes(2),
        ]);

        MissedCall::create([
            'clinic_id' => $clinic->id,
            'lead_id' => $lead->id,
            'clinic_phone_number_id' => $phoneNumber->id,
            'caller_phone' => $caller->phone,
            'called_at' => now()->subMinutes(2),
        ]);

        Conversation::create([
            'clinic_id' => $clinic->id,
            'lead_id' => $lead->id,
            'channel' => MessageChannel::SMS,
            'is_active' => true,
        ]);
    }

    private function createAnotherRespondedLead(Clinic $clinic, $phoneNumber): void
    {
        $caller = Caller::create([
            'clinic_id' => $clinic->id,
            'phone' => '+15551234005',
        ]);

        $lead = Lead::create([
            'clinic_id' => $clinic->id,
            'caller_id' => $caller->id,
            'origin' => LeadOrigin::MISSED_CALL,
            'stage' => LeadStage::RESPONDED,
            'estimated_value' => 250.00,
            'created_at' => now()->subMinutes(30),
        ]);

        MissedCall::create([
            'clinic_id' => $clinic->id,
            'lead_id' => $lead->id,
            'clinic_phone_number_id' => $phoneNumber->id,
            'caller_phone' => $caller->phone,
            'called_at' => now()->subMinutes(30),
        ]);

        $conversation = Conversation::create([
            'clinic_id' => $clinic->id,
            'lead_id' => $lead->id,
            'channel' => MessageChannel::SMS,
            'is_active' => true,
            'last_message_at' => now()->subMinutes(10),
        ]);

        Message::create([
            'clinic_id' => $clinic->id,
            'conversation_id' => $conversation->id,
            'channel' => MessageChannel::SMS,
            'direction' => MessageDirection::OUTBOUND,
            'from_phone' => $phoneNumber->phone_number,
            'to_phone' => $caller->phone,
            'body' => "Hi! This is Demo Dental Clinic. We noticed you called but we couldn't answer. Reply to this message or book online: https://demo-clinic.com/book",
            'status' => 'delivered',
            'sent_at' => now()->subMinutes(29),
            'created_at' => now()->subMinutes(29),
        ]);

        Message::create([
            'clinic_id' => $clinic->id,
            'conversation_id' => $conversation->id,
            'channel' => MessageChannel::SMS,
            'direction' => MessageDirection::INBOUND,
            'from_phone' => $caller->phone,
            'to_phone' => $phoneNumber->phone_number,
            'body' => "Do you accept Delta Dental insurance?",
            'status' => 'received',
            'created_at' => now()->subMinutes(10),
        ]);
    }

    private function createLostLead(Clinic $clinic, User $owner, $phoneNumber): void
    {
        $caller = Caller::create([
            'clinic_id' => $clinic->id,
            'phone' => '+15551234006',
        ]);

        $lead = Lead::create([
            'clinic_id' => $clinic->id,
            'caller_id' => $caller->id,
            'origin' => LeadOrigin::MISSED_CALL,
            'stage' => LeadStage::LOST,
            'estimated_value' => 250.00,
            'created_at' => now()->subHours(5),
        ]);

        MissedCall::create([
            'clinic_id' => $clinic->id,
            'lead_id' => $lead->id,
            'clinic_phone_number_id' => $phoneNumber->id,
            'caller_phone' => $caller->phone,
            'called_at' => now()->subHours(5),
        ]);

        $conversation = Conversation::create([
            'clinic_id' => $clinic->id,
            'lead_id' => $lead->id,
            'channel' => MessageChannel::SMS,
            'is_active' => false,
            'last_message_at' => now()->subHours(4),
            'last_staff_reply_at' => now()->subHours(4)->subMinutes(30),
        ]);

        Message::create([
            'clinic_id' => $clinic->id,
            'conversation_id' => $conversation->id,
            'channel' => MessageChannel::SMS,
            'direction' => MessageDirection::OUTBOUND,
            'from_phone' => $phoneNumber->phone_number,
            'to_phone' => $caller->phone,
            'body' => "Hi! This is Demo Dental Clinic. We noticed you called but we couldn't answer. Reply to this message or book online: https://demo-clinic.com/book",
            'status' => 'delivered',
            'sent_at' => now()->subHours(5),
            'created_at' => now()->subHours(5),
        ]);

        Message::create([
            'clinic_id' => $clinic->id,
            'conversation_id' => $conversation->id,
            'channel' => MessageChannel::SMS,
            'direction' => MessageDirection::INBOUND,
            'from_phone' => $caller->phone,
            'to_phone' => $phoneNumber->phone_number,
            'body' => "Sorry, wrong number",
            'status' => 'received',
            'created_at' => now()->subHours(4),
        ]);

        MissedCallOutcome::create([
            'clinic_id' => $clinic->id,
            'lead_id' => $lead->id,
            'outcome_type' => OutcomeType::WRONG_NUMBER,
            'resolved_by_user_id' => $owner->id,
            'resolved_at' => now()->subHours(4),
        ]);
    }

    private function createYesterdayBookedLead(Clinic $clinic, User $owner, $phoneNumber): void
    {
        $createdAt = now()->subDay();
        $responseAt = now()->subDay()->addHour();
        $resolvedAt = now()->subDay()->addMinutes(90);

        $caller = Caller::create([
            'clinic_id' => $clinic->id,
            'phone' => '+15551234007',
            'name' => 'John Smith',
        ]);

        $lead = Lead::create([
            'clinic_id' => $clinic->id,
            'caller_id' => $caller->id,
            'origin' => LeadOrigin::MISSED_CALL,
            'stage' => LeadStage::BOOKED,
            'estimated_value' => 250.00,
            'created_at' => $createdAt,
        ]);

        MissedCall::create([
            'clinic_id' => $clinic->id,
            'lead_id' => $lead->id,
            'clinic_phone_number_id' => $phoneNumber->id,
            'caller_phone' => $caller->phone,
            'called_at' => $createdAt,
        ]);

        $conversation = Conversation::create([
            'clinic_id' => $clinic->id,
            'lead_id' => $lead->id,
            'channel' => MessageChannel::SMS,
            'is_active' => false,
            'created_at' => $createdAt,
            'last_message_at' => $resolvedAt,
            'last_staff_reply_at' => $resolvedAt,
        ]);

        Message::create([
            'clinic_id' => $clinic->id,
            'conversation_id' => $conversation->id,
            'channel' => MessageChannel::SMS,
            'direction' => MessageDirection::OUTBOUND,
            'from_phone' => $phoneNumber->phone_number,
            'to_phone' => $caller->phone,
            'body' => "Hi! This is Demo Dental Clinic. We noticed you called but we couldn't answer.",
            'status' => 'delivered',
            'sent_at' => $createdAt,
            'created_at' => $createdAt,
        ]);

        Message::create([
            'clinic_id' => $clinic->id,
            'conversation_id' => $conversation->id,
            'channel' => MessageChannel::SMS,
            'direction' => MessageDirection::INBOUND,
            'from_phone' => $caller->phone,
            'to_phone' => $phoneNumber->phone_number,
            'body' => "I need an emergency appointment, I chipped my tooth",
            'status' => 'received',
            'created_at' => $responseAt,
        ]);

        Message::create([
            'clinic_id' => $clinic->id,
            'conversation_id' => $conversation->id,
            'channel' => MessageChannel::SMS,
            'direction' => MessageDirection::OUTBOUND,
            'from_phone' => $phoneNumber->phone_number,
            'to_phone' => $caller->phone,
            'body' => "We can see you today at 4pm for an emergency visit. Does that work?",
            'status' => 'delivered',
            'sent_by_user_id' => $owner->id,
            'sent_at' => $resolvedAt,
            'created_at' => $resolvedAt,
        ]);

        MissedCallOutcome::create([
            'clinic_id' => $clinic->id,
            'lead_id' => $lead->id,
            'outcome_type' => OutcomeType::BOOKED,
            'actual_value' => 450.00,
            'notes' => 'Emergency - chipped tooth repair',
            'resolved_by_user_id' => $owner->id,
            'resolved_at' => $resolvedAt,
        ]);
    }

    private function createRecentNewLead(Clinic $clinic, $phoneNumber): void
    {
        $caller = Caller::create([
            'clinic_id' => $clinic->id,
            'phone' => '+15551234008',
        ]);

        $lead = Lead::create([
            'clinic_id' => $clinic->id,
            'caller_id' => $caller->id,
            'origin' => LeadOrigin::MISSED_CALL,
            'stage' => LeadStage::NEW,
            'estimated_value' => 250.00,
            'created_at' => now()->subMinutes(5),
        ]);

        MissedCall::create([
            'clinic_id' => $clinic->id,
            'lead_id' => $lead->id,
            'clinic_phone_number_id' => $phoneNumber->id,
            'caller_phone' => $caller->phone,
            'called_at' => now()->subMinutes(5),
        ]);

        Conversation::create([
            'clinic_id' => $clinic->id,
            'lead_id' => $lead->id,
            'channel' => MessageChannel::SMS,
            'is_active' => true,
        ]);
    }

    private function createLastMonthBookedLeads(Clinic $clinic, User $owner, $phoneNumber): void
    {
        // Create 3 booked leads from last month for comparison
        $bookings = [
            ['phone' => '+15551234101', 'value' => 320.00, 'days_ago' => 35],
            ['phone' => '+15551234102', 'value' => 185.00, 'days_ago' => 40],
            ['phone' => '+15551234103', 'value' => 275.00, 'days_ago' => 45],
        ];

        foreach ($bookings as $booking) {
            $createdAt = now()->subDays($booking['days_ago']);
            $resolvedAt = now()->subDays($booking['days_ago'])->addMinutes(45);

            $caller = Caller::create([
                'clinic_id' => $clinic->id,
                'phone' => $booking['phone'],
            ]);

            $lead = Lead::create([
                'clinic_id' => $clinic->id,
                'caller_id' => $caller->id,
                'origin' => LeadOrigin::MISSED_CALL,
                'stage' => LeadStage::BOOKED,
                'estimated_value' => 250.00,
                'created_at' => $createdAt,
            ]);

            MissedCall::create([
                'clinic_id' => $clinic->id,
                'lead_id' => $lead->id,
                'clinic_phone_number_id' => $phoneNumber->id,
                'caller_phone' => $caller->phone,
                'called_at' => $createdAt,
            ]);

            Conversation::create([
                'clinic_id' => $clinic->id,
                'lead_id' => $lead->id,
                'channel' => MessageChannel::SMS,
                'is_active' => false,
                'created_at' => $createdAt,
                'last_message_at' => $resolvedAt,
                'last_staff_reply_at' => $resolvedAt,
            ]);

            MissedCallOutcome::create([
                'clinic_id' => $clinic->id,
                'lead_id' => $lead->id,
                'outcome_type' => OutcomeType::BOOKED,
                'actual_value' => $booking['value'],
                'resolved_by_user_id' => $owner->id,
                'resolved_at' => $resolvedAt,
            ]);
        }

        // Add more booked leads from THIS month to show growth (spread for weekly chart)
        $thisMonthBookings = [
            ['phone' => '+15551234201', 'value' => 380.00, 'days_ago' => 5],  // Mon
            ['phone' => '+15551234202', 'value' => 290.00, 'days_ago' => 4],  // Tue
            ['phone' => '+15551234203', 'value' => 195.00, 'days_ago' => 3],  // Wed
            ['phone' => '+15551234204', 'value' => 420.00, 'days_ago' => 2],  // Thu
            ['phone' => '+15551234205', 'value' => 175.00, 'days_ago' => 10],
            ['phone' => '+15551234206', 'value' => 520.00, 'days_ago' => 15],
            ['phone' => '+15551234207', 'value' => 245.00, 'days_ago' => 20],
        ];

        foreach ($thisMonthBookings as $booking) {
            $createdAt = now()->subDays($booking['days_ago']);
            $resolvedAt = now()->subDays($booking['days_ago'])->addMinutes(30);

            $caller = Caller::create([
                'clinic_id' => $clinic->id,
                'phone' => $booking['phone'],
            ]);

            $lead = Lead::create([
                'clinic_id' => $clinic->id,
                'caller_id' => $caller->id,
                'origin' => LeadOrigin::MISSED_CALL,
                'stage' => LeadStage::BOOKED,
                'estimated_value' => 250.00,
                'created_at' => $createdAt,
            ]);

            MissedCall::create([
                'clinic_id' => $clinic->id,
                'lead_id' => $lead->id,
                'clinic_phone_number_id' => $phoneNumber->id,
                'caller_phone' => $caller->phone,
                'called_at' => $createdAt,
            ]);

            Conversation::create([
                'clinic_id' => $clinic->id,
                'lead_id' => $lead->id,
                'channel' => MessageChannel::SMS,
                'is_active' => false,
                'created_at' => $createdAt,
                'last_message_at' => $resolvedAt,
                'last_staff_reply_at' => $resolvedAt,
            ]);

            MissedCallOutcome::create([
                'clinic_id' => $clinic->id,
                'lead_id' => $lead->id,
                'outcome_type' => OutcomeType::BOOKED,
                'actual_value' => $booking['value'],
                'resolved_by_user_id' => $owner->id,
                'resolved_at' => $resolvedAt,
            ]);
        }
    }
}
