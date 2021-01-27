<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Simfoni API Endpoint
    |--------------------------------------------------------------------------
    |
    | The Simfoni API endpoint
    |
    */

    'endpoint' => env('SIMFONI_ENDPOINT', 'https://simfoni.co.uk'),

    /*
    |--------------------------------------------------------------------------
    | Simfoni Verify SSL
    |--------------------------------------------------------------------------
    |
    | Verify SSL certificates via API calls. We do not recommend disabling
    | this for security reasons. This should only be adjusted when developing
    | locally using a self signed SSL certificate.
    |
    */

    'verify_ssl' => env('SIMFONI_VERIFY_SSL', true),

    /*
    |--------------------------------------------------------------------------
    | Simfoni Webhook Signature
    |--------------------------------------------------------------------------
    |
    | The webhook signature for this application, you can find this signature
    | in the webhook admin interface on the Simfoni application
    */

    'webhook_signature' => env('SIMFONI_WEBHOOK_SIGNATURE', 'default'),

    /*
    |--------------------------------------------------------------------------
    | Simfoni Token
    |--------------------------------------------------------------------------
    |
    | The token used to authenticate API requests, this will be provided by the
    | simfoni application itself
    */

    'token' => env('SIMFONI_TOKEN', null),


];
