<?php

namespace Devinweb\LaravelYoucanPay\Enums;

use Spatie\Enum\Enum;

/**
 * @see the status for the Model \Tenant\Models\Payment
 *
 * @method static self paid()
 * @method static self pending()
 * @method static self failed()
 */
final class YouCanPayStatus extends Enum
{
}
