<?php

namespace Devinweb\LaravelYoucanPay\Tests;

use Devinweb\LaravelYoucanPay\Facades\LaravelYoucanPay;
use Illuminate\Http\Request;

class ApiKeysTest extends TestCase
{
    private $config;

    public function setUp(): void
    {
        parent::setUp();
        config()->set('youcanpay.sandboxMode', true);
        config()->set('youcanpay.private_key', 'private_key');
        config()->set('youcanpay.public_key', 'public_key');
        config()->set('youcanpay.currency', 'MAD');
        config()->set('youcanpay.success_redirect_uri', 'https://yourdomain.com/payments-status/success');
        config()->set('youcanpay.fail_redirect_uri', 'https://yourdomain.com/payments-status/fail');
        $this->request = new Request([], [], [], [], [], [], '');
        $this->config = config('youcanpay');
    }

    /**
     * @test
     * @return void
     */
    public function test_pass()
    {
        $result = LaravelYoucanPay::checkKeys($this->config['private_key'], $this->config['public_key']);
        $this->assertFalse($result);
    }
}
