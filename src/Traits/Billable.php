<?php

namespace Devinweb\LaravelYouCanPay\Traits;

use Devinweb\LaravelYouCanPay\Facades\LaravelYouCanPay;
use Devinweb\LaravelYouCanPay\Models\Transaction;
use InvalidArgumentException;
use Illuminate\Http\Request;

trait Billable
{
    /**
     * Get all of the transactions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class, $this->getForeignKey());
    }

    /**
     * Get Payment token
     *
     * @param array $data
     * @param Request $request
     * @param array $metadata
     *
     * @throws \InvalidArgumentException
     * @return string
     */
    public function getPaymentToken(array $data, Request $request, array $metadata=[])
    {
        $this->validateCustomerInfoFields();
        return $this->getInstance($data, $request, $metadata)->getId();
    }


    /**
     * Get Payment URL
     *
     * @param array $data
     * @param Request $request
     * @param array $metadata
     *
     * @throws \InvalidArgumentException
     * @return string
     */
    public function getPaymentURL(array $data, Request $request, array $metadata=[])
    {
        $this->validateCustomerInfoFields();
        return $this->getInstance($data, $request, $metadata)->getPaymentURL();
    }

    /**
     * Validate the cutomer info fields
     *
     * @throws \InvalidArgumentException
     * @return void
     */
    private function validateCustomerInfoFields()
    {
        if (!method_exists($this, 'getCustomerInfo')) {
            throw new InvalidArgumentException("Please make sure to add getCustomerInfo that return an array");
        }

        $fields = ['name', 'address', 'zip_code', 'city', 'state', 'country_code', 'phone', 'email'];

        foreach ($fields as $field) {
            if (! array_key_exists($field, $this->getCustomerInfo())) {
                throw new InvalidArgumentException("Please make sure to add {$field} key to the array that returned by getCustomerInfo");
            }
        }
    }

    /**
     * Get an instance of LaravelYouCanPay
     *
     * @param array $data
     * @param Request $request
     * @param array $metadata
     * @return \Devinweb\LaravelYouCanPay\LaravelYouCanPay
     */
    private function getInstance(array $data, Request $request, array $metadata=[])
    {
        return LaravelYouCanPay::setCustomerInfo($this->getCustomerInfo())->setMetadata($metadata)->createTokenization($data, $request);
    }
}
