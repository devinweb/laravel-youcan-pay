<?php

use Devinweb\LaravelYoucanPay\Http\Controllers\WebHookController;
use Illuminate\Support\Facades\Route;

Route::post('webhook', [WebHookController::class, 'handleWebhook'])->name('webhook');
