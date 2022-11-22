<?php
namespace Devinweb\LaravelYoucanPay\Actions;

use YouCan\Pay\YouCanPay;

class CreateToken
{
    /**
     * Create a token
     *
     * @param YouCanPay $youCanPay
     * @param array $attributes
     * @param array $config
     * @param array $customer_info
     * @param array $metadata
     * @return \YouCan\Pay\Models\Token
     */
    public function __invoke(YouCanPay $youCanPay, array $data_attributes)
    {
        $attributes = $data_attributes['attributes'];
        $config = $data_attributes['config'];
        $metadata = $data_attributes['metadata'];
        $customer_info = $data_attributes['customer_info'];
        $ip=$data_attributes['ip'];
        $success_redirect_uri  = $config['success_redirect_uri'];
        $fail_redirect_uri  = $config['fail_redirect_uri'];
        
        return $youCanPay->token->create(
            $attributes['order_id'],
            $attributes['amount'],
            $config['currency'],
            $ip,
            $success_redirect_uri,
            $fail_redirect_uri,
            $customer_info,
            $metadata
        );
    }
}
