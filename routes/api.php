<?php

use App\Http\Controllers\WebhookController;

Route::post('/stripe/webhook', [WebhookController::class, 'handle'])->name('stripe.webhook');