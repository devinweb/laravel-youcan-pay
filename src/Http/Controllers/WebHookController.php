<?php

namespace Devinweb\LaravelYoucanPay\Http\Controllers;

use Devinweb\LaravelYoucanPay\Events\WebhookReceived;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

class WebHookController extends Controller
{
    /**
     * Create a new WebhookController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('verify-youcanpay-webhook-signature');
    }

    /**
     * Handle a YouCanPay webhook call.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handleWebhook(Request $request)
    {
        $payload = json_decode($request->getContent(), true);

        WebhookReceived::dispatch($payload);

        return new Response('Webhook Handled', 200);
    }
}
