<?php

return array(

    'app_admin_email' => '',
    'app_admin_name' => '',

    'max_file_upload_size' => 6291456, // 6MB

    'signup' => TRUE,

    'confirmEmail' => TRUE,

    'socialMediaLogin' => FALSE,

    'two-factor-authentication' => [
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
    ],

    'cors' => [
        'access-control-allow-origin' => '*',
        'access-control-allow-methods' => 'GET, OPTIONS, PUT, POST, DELETE',
        'access-control-allow-headers' => 'Content-Type, X-Auth-Token, Origin, Token, Authorization',
    ],

    'remember_me_duration' => 20160,

    'basic-app-info' => array(
        "page_title" => "A seed application for Laravel 5",
        "page_keywords" => "Gondolyn, gondolin, Laravel 5, Seed App",
        "page_description" => "A clean seed application for Laravel 5",
        "page_details" => ""
    ),

    // Subscription details

    'subscription' => TRUE,

    'tax' => 0.00,

    // Invoice Details
    'company'   => '',
    'street'    => '',
    'location'  => '',
    'phone'     => '',
    'url'       => '',
    'product'   => '',

    'stripe' => array(
        'secret_key' => '',
        'publish_key' => '',
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
