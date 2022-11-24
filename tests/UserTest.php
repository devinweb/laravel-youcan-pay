<?php

namespace Devinweb\LaravelYoucanPay\Tests;

use Devinweb\LaravelYoucanPay\Actions\CreateToken;
use Devinweb\LaravelYoucanPay\Facades\LaravelYoucanPay;
use Devinweb\LaravelYoucanPay\LaravelYoucanPay as LYC;
use Devinweb\LaravelYoucanPay\Models\Transaction;
use Devinweb\LaravelYoucanPay\Tests\Fixtures\User as FixturesUser;
use Devinweb\LaravelYoucanPay\Traits\Billable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Mockery;
use Mockery\MockInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = $this->createCustomer();
    }

    /**
     * @test
     * @return void
     */
    public function test_user_can_generate_a_token()
    {
        LaravelYoucanPay::useCustomerModel(FixturesUser::class);
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

        $token = $this->user->getPaymentToken($required_data, $request);

        $this->assertEquals($token_id, $token);
    }

    /**
     * @test
     * @return void
     */
    public function test_user_can_generate_a_payment_url()
    {
        LaravelYoucanPay::useCustomerModel(FixturesUser::class);

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

        $payment_url = "https://youcanpay.com/sandbox/payment-form/token_id?lang=en";
        $url = $this->user->getPaymentURL($required_data, $request);
        $this->assertEquals($url, $payment_url);
    }

    /**
     * @test
     * @return void
     */
    public function test_an_exception_should_fired_if_getCustomerInfo_doesnt_added_to_the_user_model()
    {
        $user = User::create([
            'email' => "imad-youcanpay@devinweb.com",
            'name' => 'Darbaoui imad',
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        ]);

        $token_id = 'token_id';

        $required_data = [
            'order_id' => '123',
            'amount' => '200'
        ];

        $request = new Request([], [], [], [], [], [], '');

        $this->instance(
            CreateToken::class,
            Mockery::mock(CreateToken::class, function (MockInterface $mock) use ($token_id) {
                $mock->shouldReceive('__invoke')->andReturn((new FakeToken($token_id)));
            })
        );
        
        try {
            $user->getPaymentToken($required_data, $request);
        } catch (InvalidArgumentException $e) {
            $this->assertStringContainsString('Please make sure to add getCustomerInfo that return an array', $e->getMessage());
        }
    }

    /**
     * @test
     * @return void
     */
    public function test_an_exception_should_fired_if_getCustomerInfo_exist_and_a_key_doesnt_exists_in_the_returned_array()
    {
        $user = UserWithMethodGetCustomerInfo::create([
            'email' => "imad-youcanpay@devinweb.com",
            'name' => 'Darbaoui imad',
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        ]);

        $token_id = 'token_id';

        $required_data = [
            'order_id' => '123',
            'amount' => '200'
        ];

        $request = new Request([], [], [], [], [], [], '');

        $this->instance(
            CreateToken::class,
            Mockery::mock(CreateToken::class, function (MockInterface $mock) use ($token_id) {
                $mock->shouldReceive('__invoke')->andReturn((new FakeToken($token_id)));
            })
        );
        
        try {
            $user->getPaymentToken($required_data, $request);
        } catch (InvalidArgumentException $e) {
            $this->assertStringContainsString('Please make sure to add address key to the array that returned by getCustomerInfo', $e->getMessage());
        }
    }

    /**
     * @test
     * @return void
     */
    public function test_transactions()
    {
        \Devinweb\LaravelYoucanPay\Tests\Fixtures\User::factory()
            ->has(Transaction::factory()->count(3))
            ->create();

        $this->assertDatabaseCount('transactions', 3);
    }
}


class User extends Model
{
    use Billable;
    protected $guarded = [];
}

class UserWithMethodGetCustomerInfo extends User
{
    protected $table = 'users';

    public function getCustomerInfo()
    {
        return [
            'name'         => $this->name,
            'zip_code'     => '93000',
            'city'         => 'Tetouan',
            'state'        => 'Tanger-Tétouan-Al Hoceïma',
            'country_code' => 'MA',
            'phone'        => '0620000202',
            'email'        => $this->email,
        ];
    }
}
