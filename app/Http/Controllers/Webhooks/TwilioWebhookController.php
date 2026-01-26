<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\UseCases\Messaging\UpdateMessageStatus;
use App\UseCases\Webhooks\ProcessTwilioSmsWebhook;
use App\UseCases\Webhooks\ProcessTwilioVoiceWebhook;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TwilioWebhookController extends Controller
{
    public function voice(Request $request, ProcessTwilioVoiceWebhook $useCase): Response
    {
        $useCase->execute($request->all());

        return response('', 200);
    }

    public function sms(Request $request, ProcessTwilioSmsWebhook $useCase): Response
    {
        $useCase->execute($request->all());

        return response('', 200);
    }

    public function voiceStatus(Request $request): Response
    {
        // Voice status callbacks are logged but not processed for now
        // Could be used to track call duration, recording URLs, etc.
        return response('', 200);
    }

    public function smsStatus(Request $request, UpdateMessageStatus $useCase): Response
    {
        $messageSid = $request->input('MessageSid') ?? $request->input('SmsSid');
        $status = $request->input('MessageStatus') ?? $request->input('SmsStatus');

        if ($messageSid && $status) {
            $useCase->execute($messageSid, $status);
        }

        return response('', 200);
    }
}
