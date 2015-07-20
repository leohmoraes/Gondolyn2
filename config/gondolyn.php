<?php

return array(

    'appAdminEmail' => '',
    'appAdminName' => '',

    'maxFileUploadSize' => 6291456, // 6MB

    'signUp' => TRUE,

    'confirmEmail' => TRUE,

    'socialMediaLogin' => FALSE,

    'failedLogins' => FALSE,

    'failedLoginsLimit' => 2,

    'twoFactorAuthentication' => [
        'enabled' => FALSE,
        'duration' => '60days', // session, lifetime, 60days
        'twilio' => [
            'account_sid' => '',
            'auth_token' => '',
            'from_number' => '+15005550006'
        ],
    ],

    'authKeys' => [
        '7Hp6qlswIuLKSKRJ7FVOkAre2Bcs',
        'jPKrx9yYv0vdugYm0Q67w2WQCn4E',
        'Yg7i6SJE2hCxh7LMoJ9ozfv5isVU',
        'PZzNgH6mzP1B7E9pMUbaDkydA4iS',
        'weNmenau4gRE6TfdxoCjjOuW939K',
        '7sCEHq1VObpSrhxxaNLHbo1vcvrm',
        'yB6G4JlLamGYx6RZnowybifnGhST',
        'MkfkWm016lMgItefA5eYuLwF3M3J',
        '3WDPgT9dfaelw0BeuQu4WI2qty6x',
        'l9go5UdmNnz3yNo4O0jjQNnMwqd0',
        'ZjxktOIzIVWvjwzRkXW8YgsO3PBA',
    ],

    'csrfIgnoredRoutes' => [
        'api/login',
        'failed/payment'
    ],

    'cors' => [
        'access-control-allow-origin' => '*',
        'access-control-allow-methods' => 'GET, OPTIONS, PUT, POST, DELETE',
        'access-control-allow-headers' => 'Content-Type, X-Auth-Token, Origin, Token, Authorization',
    ],

    'security' => [
        'xss-protection' => '1',
        'x-frame-option' => 'SAMEORIGIN',
        'content-security-policy' => 'default-src \'self\' \'unsafe-inline\' \'unsafe-eval\' '.str_replace('|', ' ', env('SAFE_DOMAINS')).'; img-src *; frame-src *; font-src \'self\' data: *;',
        'x-content-type-options' => 'nosniff',
        'browser-cache' => [
            'cache-control' => 'no-cache, no-store, must-revalidate', // HTTP 1.1.
            'pragma' => 'no-cache', // HTTP 1.0.
            'expires' => '0', // Proxies.
        ],
    ],

    'rememberMeDuration' => 20160,

    'appInfo' => array(
        "page_title" => "A seed application for Laravel 5",
        "page_keywords" => "Gondolyn, gondolin, Laravel 5, Seed App",
        "page_description" => "A clean seed application for Laravel 5",
        "page_details" => ""
    ),

    // Subscription details

    'subscription' => TRUE,

    // Tax rates
    'tax' => [
        "alberta" => 5,
        "british columbia" => 12,
        "manitoba" => 13,
        "new brunswick" => 13,
        "newfoundland &amp; labrador" => 13,
        "northwest territories" => 5,
        "nova scotia" => 15,
        "nunavut" => 5,
        "ontario" => 13,
        "prince edward island" => 14,
        "quebec" => 14.98,
        "saskatchewan" => 10,
        "yukon" => 5,
    ],

    // Invoice Details
    'company'   => '',
    'street'    => '',
    'location'  => '',
    'phone'     => '',
    'url'       => '',
    'product'   => '',

    'stripe' => array(
        'secret_key' => env('STRIPE_SEC_KEY'),
        'publish_key' => env('STRIPE_PUB_KEY'),
    ),

    'trial' => 30,

    'packages' => array(
        'basic' => array(
            'id'            => 'basic',
            'name'          => 'Basic Plan',
            'type'          => 'monthly',           // monthly or yearly
            'cost'          => '$5',                // cost
            'dollar'        => 'CAD',               // Dollar type
            'stripe_id'     => 'basic',             // Stipe Plan ID
        ),
        'advanced' => array(
            'id'            => 'advanced',
            'name'          => 'Advanced Plan',
            'type'          => 'monthly',           // monthly or yearly
            'cost'          => '$15',               // cost
            'dollar'        => 'CAD',               // Dollar type
            'stripe_id'     => 'advanced',          // Stipe Plan ID
        ),

    )

);
