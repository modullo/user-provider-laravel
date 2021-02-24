<?php
return [
    // the client environment
    'env' => env('MODULLO_ENV', 'staging'),


   

    /*
    |--------------------------------------------------------------------------
    | API Credentials
    |--------------------------------------------------------------------------
    |
    | You need to provide the credentials that will be used while communicating
    | with the modullo API.
    |
    |
    */
    'client' => [

        // the client ID provided to you for use with your app
        'id' => env('MODULLO_CLIENT_ID', 0),

        // the client secret
        'secret' => env('MODULLO_CLIENT_SECRET', '')
    ]
];