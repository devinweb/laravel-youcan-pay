<?php

namespace Devinweb\LaravelYoucanPay\Http\Middleware;

use Closure;
use Devinweb\LaravelYoucanPay\Facades\LaravelYoucanPay;
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
        Log::info($payload);
        Log::info($signature);
        LaravelYoucanPay::validateWebhookSignature($signature, $payload);

        return $next($request);
    }
}
