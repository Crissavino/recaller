<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\ClinicPhoneNumber;
use App\UseCases\Messaging\UpdateMessageStatus;
use App\UseCases\Webhooks\ProcessTwilioSmsWebhook;
use App\UseCases\Webhooks\ProcessTwilioVoiceWebhook;
use App\UseCases\Webhooks\ProcessTwilioWhatsAppWebhook;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class TwilioWebhookController extends Controller
{
    public function voice(Request $request, ProcessTwilioVoiceWebhook $useCase): Response
    {
        $callStatus = $request->input('CallStatus');
        $calledNumber = $request->input('Called') ?? $request->input('To');

        Log::info('Twilio voice webhook', [
            'status' => $callStatus,
            'called' => $calledNumber,
            'from' => $request->input('From'),
        ]);

        // Only process incoming calls (ringing status)
        if ($callStatus !== 'ringing') {
            return response('', 200);
        }

        // Find the clinic phone number to check for forwarding
        $clinicPhoneNumber = $this->findClinicPhoneNumber($calledNumber);

        if (!$clinicPhoneNumber) {
            Log::warning('Twilio voice webhook for unknown number', ['called' => $calledNumber]);
            return response('', 200);
        }

        // If forwarding is configured, try to forward the call first
        if ($clinicPhoneNumber->forward_to_phone) {
            return $this->forwardCall($clinicPhoneNumber, $request->input('From'));
        }

        // No forwarding - treat as missed call immediately
        $useCase->execute($request->all());

        return $this->missedCallResponse();
    }

    private function forwardCall(ClinicPhoneNumber $clinicPhoneNumber, string $callerPhone): Response
    {
        $forwardTo = $clinicPhoneNumber->forward_to_phone;
        $timeout = $clinicPhoneNumber->forward_timeout_seconds ?? 20;
        $actionUrl = route('webhooks.twilio.voice.forward-result');

        Log::info('Forwarding call', [
            'forward_to' => $forwardTo,
            'timeout' => $timeout,
            'caller' => $callerPhone,
        ]);

        $twiml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<Response>
    <Dial timeout="{$timeout}" action="{$actionUrl}" callerId="{$callerPhone}">
        <Number>{$forwardTo}</Number>
    </Dial>
</Response>
XML;

        return response($twiml, 200)->header('Content-Type', 'text/xml');
    }

    public function forwardResult(Request $request, ProcessTwilioVoiceWebhook $useCase): Response
    {
        $dialCallStatus = $request->input('DialCallStatus');
        $callSid = $request->input('CallSid');

        Log::info('Forward result', [
            'dial_status' => $dialCallStatus,
            'call_sid' => $callSid,
        ]);

        // If the call was answered, just hang up (call completed successfully)
        if ($dialCallStatus === 'completed') {
            $twiml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<Response>
    <Hangup/>
</Response>
XML;
            return response($twiml, 200)->header('Content-Type', 'text/xml');
        }

        // Call was not answered (no-answer, busy, failed, canceled)
        // Process as missed call
        $useCase->execute($request->all());

        return $this->missedCallResponse();
    }

    private function missedCallResponse(): Response
    {
        $twiml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<Response>
    <Say language="es-ES">Gracias por llamar. En este momento no podemos atenderte, pero te enviaremos un mensaje para coordinar tu cita.</Say>
    <Hangup/>
</Response>
XML;

        return response($twiml, 200)->header('Content-Type', 'text/xml');
    }

    private function findClinicPhoneNumber(?string $phoneNumber): ?ClinicPhoneNumber
    {
        if (!$phoneNumber) {
            return null;
        }

        $normalized = ltrim($phoneNumber, '+');
        $withPlus = '+' . $normalized;

        return ClinicPhoneNumber::where('is_active', true)
            ->where(function ($query) use ($phoneNumber, $normalized, $withPlus) {
                $query->where('phone_number', $phoneNumber)
                    ->orWhere('phone_number', $normalized)
                    ->orWhere('phone_number', $withPlus);
            })
            ->first();
    }

    public function sms(Request $request, ProcessTwilioSmsWebhook $useCase): Response
    {
        $useCase->execute($request->all());

        return response('', 200);
    }

    public function voiceStatus(Request $request, ProcessTwilioVoiceWebhook $useCase): Response
    {
        // Process the final call status (no-answer, completed, busy, etc.)
        $useCase->execute($request->all());

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

    public function whatsapp(Request $request, ProcessTwilioWhatsAppWebhook $useCase): Response
    {
        $useCase->execute($request->all());

        return response('', 200);
    }
}
