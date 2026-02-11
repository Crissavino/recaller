<?php

namespace App\Jobs;

use App\UseCases\Messaging\SendFollowUpMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendScheduledFollowUp implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $leadId,
    ) {}

    public function handle(SendFollowUpMessage $sendFollowUpMessage): void
    {
        $sendFollowUpMessage->execute($this->leadId);
    }
}
