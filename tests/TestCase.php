<?php

namespace Devinweb\LaravelYouCanPay\Tests;

use Devinweb\LaravelYouCanPay\LaravelYouCanPay;
use Devinweb\LaravelYouCanPay\Providers\LaravelYouCanPayServiceProvider;
use Devinweb\LaravelYouCanPay\Tests\Fixtures\User;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        // additional setup
    }

    protected function defineDatabaseMigrations()
    {
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');
        $this->loadLaravelMigrations();
    }

    protected function getPackageProviders($app)
    {
        return [LaravelYouCanPayServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app)
    {
        LaravelYouCanPay::useCustomerModel(User::class);
    }

    protected function createCustomer($email = 'imad', array $options = []): User
    {
        return User::create(array_merge([
            'email' => "{$email}@devinweb.com",
            'name' => 'Darbaoui imad',
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        ], $options));
    }
}
