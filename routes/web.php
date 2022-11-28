<?php

use Devinweb\LaravelYouCanPay\Http\Controllers\WebHookController;
use Illuminate\Support\Facades\Route;

Route::post('webhook', [WebHookController::class, 'handleWebhook'])->name('webhook');
