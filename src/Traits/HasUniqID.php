<?php

namespace Devinweb\LaravelYouCanPay\Traits;

use Illuminate\Support\Str;

trait HasUniqID
{
    public static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (! $model->getKey()) {
                $model->{$model->getKeyName()} = Str::uuid()->toString();
            }
        });
    }
}
