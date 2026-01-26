<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\UseCases\Webhooks\ProcessVonageSmsWebhook;
use App\UseCases\Webhooks\ProcessVonageVoiceWebhook;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class VonageWebhookController extends Controller
{
    public function voice(Request $request, ProcessVonageVoiceWebhook $useCase): Response
    {
        $useCase->execute($request->all());

        return response('', 200);
    }

    public function sms(Request $request, ProcessVonageSmsWebhook $useCase): Response
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
        // Could update message status based on 'status' field
        return response('', 200);
    }
}
