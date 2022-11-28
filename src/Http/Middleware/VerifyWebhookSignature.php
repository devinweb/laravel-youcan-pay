<?php

namespace Devinweb\LaravelYouCanPay\Http\Middleware;

use Closure;
use Devinweb\LaravelYouCanPay\Facades\LaravelYouCanPay;
use Illuminate\Support\Facades\Log;

class VerifyWebhookSignature
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Illuminate\Http\Response
     *
     * @throws \YouCan\Pay\API\Exceptions\InvalidWebhookSignatureException
     */
    public function handle($request, Closure $next)
    {
        $payload = json_decode($request->getContent(), true);
        $signature = $request->header('x-youcanpay-signature');
        LaravelYouCanPay::validateWebhookSignature($signature, $payload);

        return $next($request);
    }
}
