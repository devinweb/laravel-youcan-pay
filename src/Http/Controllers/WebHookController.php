<?php

namespace Devinweb\LaravelYoucanPay\Http\Controllers;

use Devinweb\LaravelYoucanPay\Enums\YouCanPayStatus;
use Devinweb\LaravelYoucanPay\Events\WebhookReceived;
use Devinweb\LaravelYoucanPay\LaravelYoucanPay;
use Devinweb\LaravelYoucanPay\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;

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

        $method = 'handle'.Str::studly(str_replace('.', '_', $payload['event_name']));
        
        WebhookReceived::dispatch($payload);

        if (method_exists($this, $method)) {
            $this->{$method}($payload);

            return new Response('Webhook Handled', 200);
        }

        return new Response;
    }

    /**
     * Handle transaction paid webhook event.
     *
     * @param array $payload
     * @return void
     */
    protected function handleTransactionPaid(array $payload)
    {
        $customer = Arr::get($payload, 'payload.customer');
        $transaction = Arr::get($payload, 'payload.transaction');
        $youcanpay_id = Arr::get($payload, 'id');
        $user_model  = LaravelYoucanPay::$customerModel;
        $user = (new $user_model)->whereEmail($customer['email'])->first();

        Transaction::create([
            'user_id' => $user? $user->id : null,
            'name' => 'default',
            'order_id' => $transaction['order_id'],
            'status' => YouCanPayStatus::PAID(),
            'youcanpay_id' => $youcanpay_id,
            'price' => $transaction['amount'],
            'payload' => $payload
        ]);
    }
    
    /**
     * Handle transaction failed webhook event.
     *
     * @param array $payload
     * @return void
     */
    protected function handleTransactionFailed(array $payload)
    {
        Log::info([
           'failed' => $payload
        ]);
    }
    
    /**
     * Handle transaction refunded webhook event.
     *
     * @param array $payload
     * @return void
     */
    protected function handleTransactionRefunded(array $payload)
    {
        Log::info([
           'refunded' => $payload
        ]);
    }
}
