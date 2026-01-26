<?php

namespace App\Http\Middleware;

use App\Models\ClinicPhoneNumber;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Twilio\Security\RequestValidator;

class VerifyTwilioSignature
{
    public function handle(Request $request, Closure $next): Response
    {
        if (app()->environment('local', 'testing')) {
            return $next($request);
        }

        $signature = $request->header('X-Twilio-Signature');

        if (!$signature) {
            return response('Unauthorized', 401);
        }

        $clinicPhoneNumber = $this->findClinicPhoneNumber($request);

        if (!$clinicPhoneNumber) {
            return response('Phone number not found', 404);
        }

        $authToken = $this->getAuthToken($clinicPhoneNumber);

        if (!$authToken) {
            return response('Integration not configured', 500);
        }

        $validator = new RequestValidator($authToken);
        $url = $request->fullUrl();
        $params = $request->all();

        if (!$validator->validate($signature, $url, $params)) {
            return response('Invalid signature', 403);
        }

        return $next($request);
    }

    private function findClinicPhoneNumber(Request $request): ?ClinicPhoneNumber
    {
        $phoneNumber = $request->input('Called') ?? $request->input('To');

        if (!$phoneNumber) {
            return null;
        }

        return ClinicPhoneNumber::where('phone_number', $phoneNumber)
            ->where('is_active', true)
            ->first();
    }

    private function getAuthToken(ClinicPhoneNumber $phoneNumber): ?string
    {
        $integration = $phoneNumber->integration;

        if (!$integration || !$integration->is_active) {
            return null;
        }

        return $integration->credentials['auth_token'] ?? null;
    }
}
