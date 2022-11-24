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
    public function test_webhook_uri()
    {
        Event::fake();

        $payload = ['foo'=> 'bar'];
        $signature = "ceee75263456946bf35b87708bea371708ce0e31f4daf9e8b301c1e53e3e3b06";

        $response = $this->withHeaders([
            'x-youcanpay-signature' => $signature,
        ])->postJson(route('youcanpay.webhook'), $payload);
        
        Event::assertDispatched(WebhookReceived::class);

        $response->assertOk();
    }
}
