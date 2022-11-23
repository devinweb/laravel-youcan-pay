<p align="center"><img src="/art/socialcard.png" alt="Laravel YouCanPay"></p>
<p align="center">
<a href="https://codecov.io/gh/devinweb/laravel-youcan-pay" > <img src="https://codecov.io/gh/devinweb/laravel-youcan-pay/branch/master/graph/badge.svg?token=2KRE3L2IMF"/></a>
<a href="https://packagist.org/packages/devinweb/laravel-youcan-pay" rel="nofollow"><img src="https://img.shields.io/packagist/v/devinweb/laravel-youcan-pay.svg?style=flat-square" alt="Latest Version on Packagist" data-canonical-src="https://img.shields.io/packagist/v/devinweb/laravel-youcan-pay.svg?style=flat-square" style="max-width: 100%;"></a>
<a href="https://packagist.org/packages/devinweb/laravel-youcan-pay"><img src="https://img.shields.io/packagist/dt/devinweb/laravel-youcan-pay" alt="Total Downloads"></a>
<a target="_blank" href="https://github.com/devinweb/laravel-youcan-pay/actions/workflows/main.yml/badge.svg"> <img src="https://github.com/devinweb/laravel-youcan-pay/actions/workflows/main.yml/badge.svg" alt="GitHub Actions" style="max-width: 100%;"></a>
<a target="_blank" href="https://github.com/devinweb/laravel-youcan-pay/actions/workflows/Psalm.yml"> <img src="https://github.com/devinweb/laravel-youcan-pay/actions/workflows/Psalm.yml/badge.svg" alt="Psalm" style="max-width: 100%;"></a>
</p>

# Laravel YouCayPay

- [Introduction](#Introduction)
- [Installation](#Installation)
- [Database Migrations](#Database-Migrations)
- [Configuration](#configuration)
  - [Billable Model](#Billable-Model)
  - [YouCanPay Keys](#YouCanPay-Keys)
- [Customers](#Customers)
  - [Retrieving Customers](#Retrieving-Customers)
  - [Generate Token](#Generate-Token)
  - [Generate Payment URL](#Generate-Payment-URL)
- [Usage](#Usage)
  - [Tokenization](#Create-a-payment)
    - [Get Token id](#Get-token-id)
    - [Get Payment url](#Get-Payment-url)
    - [Customer info](#Customer-info)
    - [Metadata](#Metadata)
  - [Generate Payment form](#generate-payment-form)
  - [Handling YouCanPay Webhooks](#Handling-YouCanPay-Webhooks)
    - [Verify webhook signature](#Verify-webhook-signature)
    - [Validate webhook signature](#validate-webhook-signature)
- [Testing and test cards](#Testing-and-test-cards)

## Introduction

Laravel YouCanPay provides an easy experience, to generate the payment form, and process all the operations related to the payment.

## Installation

You can install the package via composer:

```bash
composer require devinweb/laravel-youcan-pay
```

## Database Migrations

LaravelYouCanPay package provides it's own database to manage the user transactions in different steps, the migrations will create a new `transactions` table to hold all your user's transactions.

```shell
php artisan migrate
```

If you need to overwrite the migrations that ship with LaravelYouCanPay, you can publish them using the vendor:publish Artisan command:

```shell
php artisan vendor:publish --tag="youcanpay-migrations"
```

## Configuration

To publish the config file you can use the command

```bash
php artisan vendor:publish --tag="youcanpay-config"
```

then you can find the config file in `config/youcanpay.php`

### Billable Model

If you want the package manage the transactions based on the user model, add the `Billable` trait to your user model.
This trait provides various methods to allow to perform transaction tasks, such as creating a transaction, get `paid`, `failed` and `pending` transactions

```php
use Devinweb\LaravelYoucanPay\Traits\Billable;

class User extends Authenticatable
{
    use Billable;
}
```

LaravelYoucanPay assumes your user model will be `App\Models\User`, if you use different user model namespace you should specify it using the method `useCustomerModel` method.
This method should typically be called in the boot method of your `AppServiceProvider` class

```php
use App\Models\Core\User;
use Devinweb\LaravelYoucanPay\Facades\LaravelYoucanPay;

/**
 * Bootstrap any application services.
 *
 * @return void
 */
public function boot()
{
    LaravelYoucanPay::useCustomerModel(User::class);
}
```

If you need in each transaction the package uses the billing data for each user, make sure to include a `getCustomerInfo()` method in your user model, which return an array that contains all the data we need.

```php

/**
 * Get the customer info to send them when we generate the form token.
 *
 * @return array
 */
public function getCustomerInfo()
{
    return [
      'name'         => $this->name,
      'address'      => '',
      'zip_code'     => '',
      'city'         => '',
      'state'        => '',
      'country_code' => 'MA',
      'phone'        => $this->phone,
      'email'        => $this->email,
    ];
}
```

### YouCanPay Keys

Next, you should configure your environment in your application's `.env`

```bash
# YouCanPay env keys
SANDBOX_MODE=
PRIVATE_KEY=
PUBLIC_KEY=
CURRENCY=MAD
SUCCCESS_REDIRECT_URI=
FAIL_REDIRECT_URI=
```

## Customers

### Retrieving Customers

You can retrieve a customer by their YouCanPay ID using the `findBillable` method. This method will return an instance of the billable model:

```php

use Devinweb\LaravelYoucanPay\Facades\LaravelYoucanPay;

$user = LaravelYoucanPay::findBillable($order_id);

```

### Generate Token

If you need to generate the token form the user model the cutomer info will be attached directly from `getCustomerInfo` method

```php

$data= [
  'order_id' => '123',
  'amount' => 2000 // amount=20*100
];

$token = $user->getPaymentToken($data, $request);
```

If you need to add the metadata you can use

```php

$data= [
  'order_id' => '123',
  'amount' => 2000 // amount=20*100
];

$metadata = [
  'key' => 'value'
];

$token = $user->getPaymentToken($data, $request, $metadata);
```

If you need to get the payment url as well from the user model you can use `getPaymentURL` method with the same parameters below.

```php
$payment_url = $user->getPaymentURL($data, $request, $metadata);
```

### Generate Payment URL

## Usage

Before starting using the package make sure to update your `config/youcanpay.php` with the correct values provided by YouCanPay.

### Tokenization

#### Get Token id

The first step we need is to create a token based on the credentails get it from YouCanPay dashboard using `getId()` method.

> **Note** <br>The amount should be * 100,
> Exemple if your Product price=20$ you should send <br> **amount** = 20*100 = 200 with **currency**='USD'

```php

use Devinweb\LaravelYoucanPay\Facades\LaravelYoucanPay;
use Illuminate\Support\Str;


public function tokenization(Request $request)
{
    $order_data = [
        'order_id' => (string) Str::uuid(),
        'amount' => 200
    ];

    $token= LaravelYoucanPay::createTokenization($data, $request)->getId();
    $public_key = config('youcanpay.public_key');
    $isSandbox = config('youcanpay.sandboxMode');
    $language = config('app.locale');


    // You can at this point share a lot of data with the front end,
    // the idea behind this is making the backend manage the environment keys,
    // we don't need to store the keys in many places.
    return response()->json(compact('token', 'public_key', 'isSandbox', 'language'));
}

```

#### Get payment url

Standalone Integration, you can generate the payment url using the method `getPaymentUrl()`

```php
$paymentUrl= LaravelYoucanPay::createTokenization($data, $request)->getPaymentURL();
```

Then you can put that url in your html page

```blade
<a href="{{ $paymentUrl }}">Pay Now</a>
```

#### Customer info

If you need to add the customer data during the tokenization you can use

```php
use Devinweb\LaravelYoucanPay\Facades\LaravelYoucanPay;

$customerInfo = [
  'name'         => '',
  'address'      => '',
  'zip_code'     => '',
  'city'         => '',
  'state'        => '',
  'country_code' => '',
  'phone'        => '',
  'email'        => '',
];

$token= LaravelYoucanPay::setCustomerInfo($customerInfo)->createTokenization($data, $request)->getId();
```

#### Metadata

You can use the metadata to send data that can be retrieved after the response or in the webhook.

```php
use Devinweb\LaravelYoucanPay\Facades\LaravelYoucanPay;

$customerInfo = [
  'name'         => '',
  'address'      => '',
  'zip_code'     => '',
  'city'         => '',
  'state'        => '',
  'country_code' => '',
  'phone'        => '',
  'email'        => '',
];

$metadata = [
  // Can you insert what you want here...
  'key' => 'value'
];

$token= LaravelYoucanPay::seMetadata($metadata)
                          ->setCustomerInfo($customerInfo)
                          ->createTokenization($data, $request)->getId();
```

### Generate Payment form

At this point we receive the token from our backend, so in our blade or any other html page, you can put at the `head` this script

```html
<html>
  ...
  <head>
    <!--Add this line -->
    <script src="https://pay.youcan.shop/js/ycpay.js"></script>
  </head>
  ...
</html>
```

Then to display the form your logic it's will be looks like the code below

```javascript
<script type="text/javascript">
  // Create a YouCan Pay instance.
  const ycPay = new YCPay(
    // String public_key (required): Login to your account.
    // Go to Settings and open API Keys and copy your key.
    "public_key",
    // Optional options object
    {
      formContainer: "#payment-card",
      // Defines what language the form should be rendered in, supports EN, AR, FR.
      locale: "en",

      // Whether the integration should run in sandbox (test) mode or live mode.
      isSandbox: false,

      // A DOM selector representing which component errors should be injected into.
      // If you omit this option, you may alternatively handle errors by chaining a .catch()
      // On the pay method.
      errorContainer: "#error-container",
    }
  );

  // Select which gateways to render
  ycPay.renderAvailableGateways(["CashPlus", "CreditCard"]);

  // Alternatively, you may use gateway specific render methods if you only need one.
  ycPay.renderCreditCardForm();

</script>

```

For more information please check this [link](https://github.com/NextmediaMa/youcan-payment-php-sdk).

### Handling YouCanPay Webhooks

YouCan Pay uses webhooks to notify your application when an event happens in your account. Webhooks are useful for handling reactions to asynchronous events on your backend, such as successful payments, failed payments, successful refunds, and many other real time events. A webhook enables YouCan Pay to push real-time notifications to your application by delivering JSON payloads over HTTPS.

Before making any action related to the events received by YouCanPay, you can need to verify the signature to make sure the payload was received from YouCan pay services.
there's two method `verifyWebhookSignature` and `validateWebhookSignature`

#### Verify webhook signature

The webhook data looks like

```
[
  'id' => 'bbbb832c-dd13-4ce7-a642-6059885d9a7e',
  'event_name' => 'transaction.paid',
  'sandbox' => true,
  'payload' => [
    'transaction' => [
      'id' => 'fdd795ab-9f8a-4cf7-886c-dca9ce3ab6c0',
      'status' => 1,
      'order_id' => 'cc984e79-0de1-4e84-a654-74c102c3ba66',
      'amount' => '2000',
      'currency' => 'MAD',
      'base_currency' => NULL,
      'base_amount' => NULL,
      'created_at' => '2022-11-22T13:20:52.000000Z',
    ],
    'payment_method' => [
      'id' => 1,
      'name' => 'credit_card',
      'card' => [
        'id' => '2e42d52d-ab0a-4440-b1e2-9a41fb843dad',
        'country_code' => NULL,
        'brand' => NULL,
        'last_digits' => '4242',
        'fingerprint' => 'df276a45f62277bd43775616827f0718',
        'is_3d_secure' => false,
      ],
    ],
    'token' => [
      'id' => '811dd60e-4655-41da-b576-a1a537cda071',
    ],
    'event' => [
      'name' => 'transaction.paid',
    ],
    'customer' => [
      'id' => 'edc6fb6f-415f-4ac3-8b86-c70bead4770e',
      'email' => NULL,
      'name' => NULL,
      'address' => NULL,
      'phone' => NULL,
      'country_code' => NULL,
      'city' => NULL,
      'state' => NULL,
      'zip_code' => NULL,
    ],
    'metadata' => [],
  ],
]
```

But the interesting part is in the header request you can find the signature value using the key **`x-youcanpay-signature`**.

To verify the webhook signature before processing any logic or action.

```php
<?php

namespace App\Http\Controllers;

use Devinweb\LaravelYoucanPay\Facades\LaravelYoucanPay;
use Illuminate\Http\Request;

class YouCanPayWebhooksController extends Controller
{
    public function handle(Request $request)
    {
        $signature = $request->header('x-youcanpay-signature');
        $payload = $request->get('payload');
        if (LaravelYoucanPay::verifyWebhookSignature($signature, $payload)) {
            // you code here
        }
    }
}
```

#### Validate webhook signature

The validation has the same impact as the verification, but the validation throws an exception that you can inspect it in the log file.

```php
<?php

namespace App\Http\Controllers;

use Devinweb\LaravelYoucanPay\Facades\LaravelYoucanPay;
use Illuminate\Http\Request;

class YouCanPayWebhooksController extends Controller
{
    public function handle(Request $request)
    {
        LaravelYoucanPay::validateWebhookSignature($signature, $payload)

        // you code here
    }
}
```

## Testing and test cards

| **CARD**              | **CVV** | **DATE** | **BEHAVIOUR**         |
| --------------------- | ------- | -------- | --------------------- |
| `4242 4242 4242 4242` | 112     | 10/24    | `No 3DS - Success`    |
| `4000 0000 0000 3220` | 112     | 10/24    | `3DS - Success`       |
| `4000 0084 0000 1629` | 112     | 10/24    | `3DS - Card Rejected` |
| `4000 0000 0000 0077` | 112     | 10/24    | `No 3DS - No Funds`   |

### Testing

```bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email imad@devinweb.com instead of using the issue tracker.

## Credits

- [DARBAOUI Imad](https://github.com/devinweb)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Laravel Package Boilerplate

This package was generated using the [Laravel Package Boilerplate](https://laravelpackageboilerplate.com).

```

```
