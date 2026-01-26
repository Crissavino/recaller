<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\UseCases\Webhooks\ProcessMessageBirdSmsWebhook;
use App\UseCases\Webhooks\ProcessMessageBirdVoiceWebhook;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MessageBirdWebhookController extends Controller
{
    public function voice(Request $request, ProcessMessageBirdVoiceWebhook $useCase): Response
    {
        $useCase->execute($request->all());

        return response('', 200);
    }

    public function sms(Request $request, ProcessMessageBirdSmsWebhook $useCase): Response
    {
        $useCase->execute($request->all());

        return response('', 200);
    }

    public function voiceStatus(Request $request): Response
    {
        // Voice status callbacks logged but not processed
        return response('', 200);
    }

    public function smsStatus(Request $request): Response
    {
        // SMS delivery receipts
        return response('', 200);
    }
}
