<?php

/*
 * You can place your custom package configuration in here.
 */
return [

    "sandboxMode" => env("SANDBOX_MODE"),

    "private_key"=> env("PRIVATE_KEY"),
    
    "public_key" => env("PUBLIC_KEY"),
    
    "currency"=> env("CURRENCY"),
    
    "success_redirect_uri" => env("SUCCCESS_REDIRECT_URI"),
    
    "fail_redirect_uri" => env("FAIL_REDIRECT_URI"),

    /*
     * The tolerance value at which to look to the old pending transactions,
     * and remove them from the database.
     * By default it's set to 48 hours
     */
    "transaction" => [
        "tolerance" => env('TRANSACTION_TOLERANCE', 60 * 60 * 48),
    ]

];
