<?php

namespace Devinweb\LaravelYouCanPay\Tests;

use Devinweb\LaravelYouCanPay\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class CommandTest extends TestCase
{
    use RefreshDatabase;

    private $now;

    /**
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->now = Carbon::now();
    }

    /**
     * @test
     * @return void
     */
    public function test_clean_pending_transactions()
    {
        $tolerance = config('youcanpay.transaction.tolerance');
        Transaction::factory()->pending()->count(3)->create();
        $this->assertDatabaseCount('transactions', 3);
        $this->now = now()->addSeconds($tolerance);
        Carbon::setTestNow($this->now);
        $this->artisan('youcanpay:clean-pending-transactions');
        $this->assertDatabaseCount('transactions', 0);
    }
}
