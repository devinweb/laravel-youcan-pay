<?php

namespace Devinweb\LaravelYouCanPay\Tests;

use Devinweb\LaravelYouCanPay\Enums\YouCanPayStatus;
use Devinweb\LaravelYouCanPay\Events\WebhookReceived;
use Devinweb\LaravelYouCanPay\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;

class WebHookEventsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @return void
     */
    public function test_webhook_uri_with_event_name_transaction_paid()
    {
        Event::fake([
            WebhookReceived::class,
        ]);
        
        $user = $this->createCustomer();

        $transaction = Transaction::create([
            'user_id' =>$user->id,
            'name' => 'default',
            'status' => YouCanPayStatus::pending(),
            'price' => $amount = 2000,
            'order_id' => $order_id='123',
            'payload' => []
        ]);

        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'status' => YouCanPayStatus::pending(),
            'price' => $amount = 2000,
            'order_id' => $order_id='123'
        ]);

        $payload = [
            'id' => $youcanpay_id = 'a433f4de-b1f8-4e6a-a462-11ab2a92dba7',
            'event_name'=> 'transaction.paid',
            'payload' => [
                'customer' => [
                    'email' => 'imad@devinweb.com'
                ],
                'transaction' => [
                    'order_id'=> $order_id,
                    'amount' => $amount
                ]
            ]
        ];

        $signature = hash_hmac(
            'sha256',
            json_encode($payload),
            config('youcanpay.private_key'),
            false
        );

        $response = $this->withHeaders([
            'x-youcanpay-signature' => $signature,
        ])->postJson(route('youcanpay.webhook'), $payload);
        
        Event::assertDispatched(WebhookReceived::class);
        
        $this->assertDatabaseHas('transactions', [
            'order_id' => $transaction->order_id,
            'user_id' => $transaction->user_id,
            'status' => YouCanPayStatus::paid(),
            'youcanpay_id' => $youcanpay_id,
        ]);

        $response->assertOk();
    }


    /**
     * @test
     * @return void
     */
    public function test_webhook_uri_with_event_name_transaction_failed()
    {
        Event::fake([
            WebhookReceived::class,
        ]);
        
        $payload = [
            'id' => $youcanpay_id = 'a433f4de-b1f8-4e6a-a462-11ab2a92dba7',
            'event_name'=> 'transaction.failed',
            'payload' => [
                'customer' => [
                    'email' => 'imad@devinweb.com'
                ],
                'transaction' => [
                    'order_id'=> $order_id='123',
                    'amount' => '2000'
                ]
            ]
        ];

        $signature = hash_hmac(
            'sha256',
            json_encode($payload),
            config('youcanpay.private_key'),
            false
        );

        $response = $this->withHeaders([
            'x-youcanpay-signature' => $signature,
        ])->postJson(route('youcanpay.webhook'), $payload);
        
        Event::assertDispatched(WebhookReceived::class);
        
        $this->assertDatabaseHas('transactions', [
            'order_id' => $order_id,
            'status' => YouCanPayStatus::failed(),
            'youcanpay_id' => $youcanpay_id,
        ]);

        $response->assertOk();
    }
    
    /**
     * @test
     * @return void
     */
    public function test_webhook_uri_with_event_name_not_found()
    {
        Event::fake();

        $payload = [
            'id' => $youcanpay_id = 'a433f4de-b1f8-4e6a-a462-11ab2a92dba7',
            'event_name'=> 'fake_event_name',
            'payload' => [
                'customer' => [
                    'email' => 'imad@devinweb.com'
                ],
                'transaction' => [
                    'order_id'=> $order_id='123',
                    'amount' => '2000'
                ]
            ]
        ];

        $signature = hash_hmac(
            'sha256',
            json_encode($payload),
            config('youcanpay.private_key'),
            false
        );

        $response = $this->withHeaders([
            'x-youcanpay-signature' => $signature,
        ])->postJson(route('youcanpay.webhook'), $payload);

        Event::assertDispatched(WebhookReceived::class);
        
        $response->assertOk();
    }
}
