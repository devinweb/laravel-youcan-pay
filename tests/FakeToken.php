<?php

namespace Devinweb\LaravelYouCanPay\Tests;

use YouCan\Pay\Models\Token;

class FakeToken extends Token
{
    public const BASE_APP_URL = 'https://youcanpay.com/';

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getPaymentURL($lang = 'en'): string
    {
        return sprintf(
            "%spayment-form/%s?lang=%s",
            self::BASE_APP_URL . (config('youcanpay.sandboxMode') ? 'sandbox/' : ''),
            $this->getId(),
            $lang
        );
    }
}
