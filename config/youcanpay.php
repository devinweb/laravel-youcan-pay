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
    
    "fail_redirect_uri" => env("FAIL_REDIRECT_URI")

];
