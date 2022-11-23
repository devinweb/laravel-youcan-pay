<?php

namespace Devinweb\LaravelYoucanPay\Traits;

use Devinweb\LaravelYoucanPay\LaravelYoucanPay;

trait Billable
{
    /**
     * Get all of the subscriptions for the Stripe model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transactions()
    {
        $transaction_model = LaravelYoucanPay::$customerModel;

        return $this->hasMany($transaction_model, $this->getForeignKey())->orderBy('created_at', 'desc');
    }
}
