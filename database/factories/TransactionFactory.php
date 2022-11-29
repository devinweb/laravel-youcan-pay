<?php

namespace Devinweb\LaravelYouCanPay\Database\Factories;

use Devinweb\LaravelYouCanPay\Enums\YouCanPayStatus;
use Devinweb\LaravelYouCanPay\LaravelYouCanPay;
use Devinweb\LaravelYouCanPay\Models\Transaction;
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
        $model = LaravelYouCanPay::$customerModel;

        return [
            // 'id' => Str::uuid()->toString(),
            (new $model)->getForeignKey() => ($model)::factory(),
            'name' => 'default',
            'order_id' => Str::random(40),
            'youcanpay_id' => Str::random(40),
            'status' => YouCanPayStatus::PAID(),
            'price' => null,
            'payload'=> []
        ];
    }


    /**
     * Indicate that the transaction is pending.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function pending()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => YouCanPayStatus::pending(),
            ];
        });
    }
}
