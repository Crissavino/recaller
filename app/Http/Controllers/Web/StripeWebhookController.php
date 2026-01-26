<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\Payment\PaymentService;
use App\Services\Payment\StripePaymentProvider;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function __construct(
        protected PaymentService $paymentService
    ) {}

    /**
     * Handle the incoming Stripe webhook.
     */
    public function handleWebhook(Request $request): Response
    {
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature');
        $webhookSecret = config('services.stripe.webhook_secret');

        // Verify webhook signature if secret is configured
        if ($webhookSecret) {
            try {
                Webhook::constructEvent($payload, $signature, $webhookSecret);
            } catch (SignatureVerificationException $e) {
                Log::warning('Stripe webhook signature verification failed', [
                    'error' => $e->getMessage(),
                ]);
                return response('Invalid signature', 400);
            }
        }

        $event = json_decode($payload, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::warning('Invalid JSON payload in Stripe webhook');
            return response('Invalid payload', 400);
        }

        Log::info('Stripe webhook received', ['type' => $event['type'] ?? 'unknown']);

        try {
            // Use the Stripe provider specifically for webhook handling
            $this->paymentService->using('stripe')->handleWebhook($event);
        } catch (\Exception $e) {
            Log::error('Error handling Stripe webhook', [
                'type' => $event['type'] ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Return 200 to prevent Stripe from retrying
            // We log the error for manual investigation
        }

        return response('Webhook handled', 200);
    }
}
