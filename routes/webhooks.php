<?php

use App\Http\Controllers\Webhooks\MessageBirdWebhookController;
use App\Http\Controllers\Webhooks\TwilioWebhookController;
use App\Http\Controllers\Webhooks\VonageWebhookController;
use App\Http\Controllers\Web\StripeWebhookController;
use App\Http\Middleware\VerifyTwilioSignature;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Webhook Routes
|--------------------------------------------------------------------------
|
| These routes handle incoming webhooks from external providers.
| They are stateless and don't require CSRF protection.
|
| Providers: Twilio, Vonage, MessageBird
|
*/

Route::prefix('webhooks')->group(function () {

    // Twilio webhooks
    Route::prefix('twilio')->middleware(VerifyTwilioSignature::class)->group(function () {
        Route::post('/voice', [TwilioWebhookController::class, 'voice'])->name('webhooks.twilio.voice');
        Route::post('/voice/status', [TwilioWebhookController::class, 'voiceStatus'])->name('webhooks.twilio.voice.status');
        Route::post('/sms', [TwilioWebhookController::class, 'sms'])->name('webhooks.twilio.sms');
        Route::post('/sms/status', [TwilioWebhookController::class, 'smsStatus'])->name('webhooks.twilio.sms.status');
    });

    // Vonage webhooks
    Route::prefix('vonage')->group(function () {
        Route::match(['get', 'post'], '/voice', [VonageWebhookController::class, 'voice'])->name('webhooks.vonage.voice');
        Route::match(['get', 'post'], '/voice/status', [VonageWebhookController::class, 'voiceStatus'])->name('webhooks.vonage.voice.status');
        Route::match(['get', 'post'], '/sms', [VonageWebhookController::class, 'sms'])->name('webhooks.vonage.sms');
        Route::match(['get', 'post'], '/sms/status', [VonageWebhookController::class, 'smsStatus'])->name('webhooks.vonage.sms.status');
    });

    // MessageBird webhooks
    Route::prefix('messagebird')->group(function () {
        Route::post('/voice', [MessageBirdWebhookController::class, 'voice'])->name('webhooks.messagebird.voice');
        Route::post('/voice/status', [MessageBirdWebhookController::class, 'voiceStatus'])->name('webhooks.messagebird.voice.status');
        Route::post('/sms', [MessageBirdWebhookController::class, 'sms'])->name('webhooks.messagebird.sms');
        Route::post('/sms/status', [MessageBirdWebhookController::class, 'smsStatus'])->name('webhooks.messagebird.sms.status');
    });

    // Stripe webhooks
    Route::post('/stripe', [StripeWebhookController::class, 'handleWebhook'])->name('webhooks.stripe');

});
