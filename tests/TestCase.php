<?php

namespace Devinweb\LaravelYoucanPay\Tests;

use Devinweb\LaravelYoucanPay\LaravelYoucanPay;
use Devinweb\LaravelYoucanPay\Providers\LaravelYoucanPayServiceProvider;
use Devinweb\LaravelYoucanPay\Tests\Fixtures\User;
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
        return [LaravelYoucanPayServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app)
    {
        // // import the CreatePostsTable class from the migration
        // include_once __DIR__ . '/../database/migrations/2020_11_22_000001_create_transactions_table.php';

        // // // run the up() method of that migration class
        // (new \CreateTransactionsTable)->up();

        LaravelYoucanPay::useCustomerModel(User::class);
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
