<?php

namespace Devinweb\LaravelYouCanPay\Tests;

use Devinweb\LaravelYouCanPay\Facades\LaravelYouCanPay;
use YouCan\Pay\API\Exceptions\InvalidWebhookSignatureException;

class WebHooksTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        config()->set('youcanpay.sandboxMode', true);
        config()->set('youcanpay.private_key', 'private_key');
        config()->set('youcanpay.public_key', 'public_key');
        config()->set('youcanpay.currency', 'MAD');
        config()->set('youcanpay.success_redirect_uri', 'https://yourdomain.com/payments-status/success');
        config()->set('youcanpay.fail_redirect_uri', 'https://yourdomain.com/payments-status/fail');
    }
    
    /**
     * @test
     * @return void
     */
    public function test_success_verify_webhook_signature_should_return_true()
    {
        $payload = ['foo'=> 'bar'];
        $expectedSignature = "ceee75263456946bf35b87708bea371708ce0e31f4daf9e8b301c1e53e3e3b06";
        $result = LaravelYouCanPay::verifyWebhookSignature($expectedSignature, $payload);
        $this->assertTrue($result);
    }
    
    /**
     * @test
     * @return void
     */
    public function test_fail_verify_webhook_signature_should_return_false()
    {
        $payload = ['foo'=> 'bar'];
        $expectedSignature = "1234";
        $result = LaravelYouCanPay::verifyWebhookSignature($expectedSignature, $payload);
        $this->assertFalse($result);
    }

    /**
     * @test
     * @return void
     */
    public function test_success_validate_webhook_signature()
    {
        $payload = ['foo'=> 'bar'];
        $expectedSignature = "ceee75263456946bf35b87708bea371708ce0e31f4daf9e8b301c1e53e3e3b06";
        $result = LaravelYouCanPay::validateWebhookSignature($expectedSignature, $payload);
        $this->assertNull($result);
    }
    
    /**
     * @test
     * @return void
     */
    public function test_throw_an_exception_when_the_validate_webhook_signature_fails()
    {
        $payload = ['foo'=> 'bar'];
        $expectedSignature = "1234";
        
        try {
            LaravelYouCanPay::validateWebhookSignature($expectedSignature, $payload);
        } catch (InvalidWebhookSignatureException $e) {
            $this->assertStringContainsString('invalid webhook signature', $e->getMessage());
        }
    }
}
