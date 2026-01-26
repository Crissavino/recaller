<?php

namespace App\Jobs;

use App\UseCases\Messaging\SendFollowUpSms;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendScheduledFollowUp implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $leadId,
    ) {}

    public function handle(SendFollowUpSms $sendFollowUpSms): void
    {
        $sendFollowUpSms->execute($this->leadId);
    }
}
