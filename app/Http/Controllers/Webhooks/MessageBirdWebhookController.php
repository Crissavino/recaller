<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\UseCases\Webhooks\ProcessMessageBirdSmsWebhook;
use App\UseCases\Webhooks\ProcessMessageBirdVoiceWebhook;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class MessageBirdWebhookController extends Controller
{
    public function voice(Request $request, ProcessMessageBirdVoiceWebhook $useCase): Response
    {
        Log::info('MessageBird voice webhook received', $request->all());

        $useCase->execute($request->all());

        return response('', 200);
    }

    public function sms(Request $request, ProcessMessageBirdSmsWebhook $useCase): Response
    {
        Log::info('MessageBird SMS webhook received', $request->all());

        $useCase->execute($request->all());

        return response('', 200);
    }

    public function voiceStatus(Request $request): Response
    {
        Log::info('MessageBird voice status webhook', $request->all());

        // Voice status callbacks (ringing, answered, ended, etc.)
        // Can be used for call duration tracking
        return response('', 200);
    }

    public function smsStatus(Request $request): Response
    {
        Log::info('MessageBird SMS status webhook', $request->all());

        // SMS delivery status updates
        $messageId = $request->input('id');
        $status = $request->input('status');
        $statusDatetime = $request->input('statusDatetime');

        if (!$messageId || !$status) {
            return response('', 200);
        }

        $message = Message::where('provider_message_id', $messageId)->first();

        if (!$message) {
            Log::warning('MessageBird SMS status for unknown message', [
                'provider_message_id' => $messageId,
            ]);
            return response('', 200);
        }

        // Map MessageBird statuses to our statuses
        // MessageBird: scheduled, sent, buffered, delivered, expired, delivery_failed
        $statusMap = [
            'scheduled' => 'pending',
            'sent' => 'sent',
            'buffered' => 'sent',
            'delivered' => 'delivered',
            'expired' => 'failed',
            'delivery_failed' => 'failed',
        ];

        $newStatus = $statusMap[$status] ?? $message->status;

        $updateData = ['status' => $newStatus];

        if ($newStatus === 'delivered' && $statusDatetime) {
            $updateData['delivered_at'] = $statusDatetime;
        }

        $message->update($updateData);

        return response('', 200);
    }
}
