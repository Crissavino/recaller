<?php

use App\Http\Controllers\Webhooks\TwilioWebhookController;
use Illuminate\Support\Facades\Route;

Route::prefix('webhooks/twilio')->group(function () {
    Route::post('/voice', [TwilioWebhookController::class, 'voice']);
    Route::post('/voice/status', [TwilioWebhookController::class, 'voiceStatus']);
    Route::post('/sms', [TwilioWebhookController::class, 'sms']);
    Route::post('/sms/status', [TwilioWebhookController::class, 'smsStatus']);
});
