<?php

namespace Devinweb\LaravelYouCanPay\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Devinweb\LaravelYouCanPay\Skeleton\SkeletonClass
 */
class LaravelYouCanPay extends Facade
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
