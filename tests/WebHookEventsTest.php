<?php

namespace Devinweb\LaravelYoucanPay\Tests;

use Devinweb\LaravelYoucanPay\Actions\CreateToken;
use Devinweb\LaravelYoucanPay\Enums\YouCanPayStatus;
use Devinweb\LaravelYoucanPay\Events\WebhookReceived;
use Devinweb\LaravelYoucanPay\Facades\LaravelYoucanPay;
use Devinweb\LaravelYoucanPay\Models\Transaction;
use Devinweb\LaravelYoucanPay\Tests\Fixtures\User as FixturesUser;
use Devinweb\LaravelYoucanPay\Traits\Billable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Mockery;
use Mockery\MockInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;

class WebHookEventsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @return void
     */
    public function test_webhook_uri_with_an_existing_event_name()
    {
        Event::fake();

        $payload = [
            'id' => $youcanpay_id = 'a433f4de-b1f8-4e6a-a462-11ab2a92dba7',
            'event_name'=> 'transaction.paid',
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
