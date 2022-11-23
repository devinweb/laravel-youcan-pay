<?php

namespace Laravel\Cashier\Database\Factories;

use Devinweb\LaravelYoucanPay\Enums\YouCanPayStatus;
use Devinweb\LaravelYoucanPay\LaravelYoucanPay;
use Devinweb\LaravelYoucanPay\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TransactionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Transaction::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $model = LaravelYoucanPay::$customerModel;

        return [
            (new $model)->getForeignKey() => ($model)::factory(),
            'name' => 'default',
            'order_id' => Str::random(40),
            'status' => YouCanPayStatus::PAID(),
            'price' => null,
            'payload'=> []
        ];
    }
}
