<?php

namespace Devinweb\LaravelYouCanPay\Tests\Fixtures;

use Devinweb\LaravelYouCanPay\Database\Factories\UserFactory;
use Illuminate\Foundation\Auth\User as Model;
use Devinweb\LaravelYouCanPay\Traits\Billable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Model
{
    use Billable;
    use HasFactory;

    protected $guarded = [];

    /**
     * Get the address to sync with Stripe.
     *
     * @return array
     */
    public function getCustomerInfo()
    {
        return [
                'name'         => $this->name,
                'address'      => 'Wilaya center, Avenue Ali Yaeta, étage 3, n 31',
                'zip_code'     => '93000',
                'city'         => 'Tetouan',
                'state'        => 'Tanger-Tétouan-Al Hoceïma',
                'country_code' => 'MA',
                'phone'        => '0620000202',
                'email'        => $this->email,
        ];
    }


    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return UserFactory::new();
    }
}
