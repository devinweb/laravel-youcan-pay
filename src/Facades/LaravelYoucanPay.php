<?php

namespace Devinweb\LaravelYoucanPay\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Devinweb\LaravelYoucanPay\Skeleton\SkeletonClass
 */
class LaravelYoucanPay extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'laravel-youcan-pay';
    }
}
