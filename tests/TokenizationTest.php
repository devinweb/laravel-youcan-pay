<?php

namespace Devinweb\LaravelYoucanPay\Tests;

use Devinweb\LaravelYoucanPay\Actions\CreateToken;
use Devinweb\LaravelYoucanPay\Enums\YouCanPayStatus;
use Devinweb\LaravelYoucanPay\Facades\LaravelYoucanPay;
use InvalidArgumentException;
use Illuminate\Http\Request;
use Mockery;
use Mockery\MockInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TokenizationTest extends TestCase
{
    use RefreshDatabase;

    private $request;
    
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
    }

    /**
     * @test
     * @return void
     */
    public function test_fail_when_we_generate_a_tokenization_without_order_id()
    {
        $required_data = [
            'amount' => 200,
        ];

        try {
            LaravelYoucanPay::createTokenization($required_data, $this->request);
        } catch (InvalidArgumentException $e) {
            $this->assertStringContainsString('The order_id must be availabe in the array', $e->getMessage());
        }
    }

    /**
     * @test
     * @return void
     */
    public function test_fail_when_we_generate_a_tokenization_without_amount()
    {
        $required_data = [
            'order_id' => '123',
        ];

        try {
            LaravelYoucanPay::createTokenization($required_data, $this->request);
        } catch (InvalidArgumentException $e) {
            $this->assertStringContainsString('The amount must be availabe in the array', $e->getMessage());
        }
    }

    /**
     * @test
     * @return void
     */
    public function test_a_user_can_generate_token_from_required_attributes()
    {
        [$request, $required_data, $token_id] = $this->initData();
        $token_id_generated = LaravelYoucanPay::createTokenization($required_data, $request)->getId();
        $this->assertDatabaseHas('transactions', [
            'status' => YouCanPayStatus::pending()
        ]);
        $this->assertEquals($token_id_generated, $token_id);
    }

    /**
     * @test
     * @return void
     */
    public function test_a_user_can_generate_token_from_required_attributes_and_customer_info()
    {
        [$request, $required_data, $token_id, $customer_info] = $this->initDataWithCustomerInfo();
        $token_id_generated = LaravelYoucanPay::setCustomerInfo($customer_info)->createTokenization($required_data, $request)->getId();
        $this->assertDatabaseHas('transactions', [
            'status' => YouCanPayStatus::pending(),
        ]);
        $this->assertEquals($token_id_generated, $token_id);
    }
    
    /**
     * @test
     * @return void
     */
    public function test_a_user_can_generate_token_from_required_attributes_and_metadata()
    {
        [$request, $required_data, $token_id, $metadata] = $this->initDataWithMetadata();
        $token_id_generated = LaravelYoucanPay::setMetadata($metadata)->createTokenization($required_data, $request)->getId();
        $this->assertDatabaseHas('transactions', [
            'status' => YouCanPayStatus::pending(),
        ]);
        $this->assertEquals($token_id_generated, $token_id);
    }

    /**
     * @test
     * @return void
     */
    public function test_a_user_can_get_payment_url()
    {
        [$request, $required_data] = $this->initData();
        $payment_url = "https://youcanpay.com/sandbox/payment-form/token_id?lang=en";
        $url = LaravelYoucanPay::createTokenization($required_data, $request)->getPaymentURL();
        $this->assertDatabaseHas('transactions', [
            'status' => YouCanPayStatus::pending(),
        ]);
        $this->assertEquals($url, $payment_url);
    }

    
    private function initData()
    {
        $token_id = 'token_id';

        $required_data = [
           'order_id' => '123',
           'amount' => '200'
        ];

        $request = $this->instance(
            Request::class,
            Mockery::mock(Request::class, static function (MockInterface $mock): void {
                $mock->shouldReceive('ip')->once()->andReturn("123.123.123.123");
            })
        );

        $this->instance(
            CreateToken::class,
            Mockery::mock(CreateToken::class, function (MockInterface $mock) use ($token_id) {
                $mock->shouldReceive('__invoke')->andReturn((new FakeToken($token_id)));
            })
        );


        return [$request, $required_data,  $token_id];
    }

    private function initDataWithCustomerInfo()
    {
        $customer_info = [
            'name'         => 'John Doe',
            'address'      => 'Wilaya Center',
            'zip_code'     => '93000',
            'city'         => 'Tetouan',
            'state'        => 'Tetouan/Tanger',
            'country_code' => 'MA',
            'phone'        => '05399-66760',
            'email'        => 'contact@devinweb.com',
        ];
        [$request, $required_data, $token_id]=$this->initData();
        return [$request, $required_data, $token_id, $customer_info];
    }
    
    private function initDataWithMetadata()
    {
        $metadata = [
            'user_id'         => '123',
            'transaction_id'      => '123',
        ];
        [$request, $required_data, $token_id]=$this->initData();
        return [$request, $required_data, $token_id, $metadata];
    }
}
