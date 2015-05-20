<?php

return array(

    'signup' => TRUE,

    'smLogin' => FALSE,

    'authKeys' => array(
        'mon1ROG3aobtj2xN2utYcQ2PlG1x1b'
    ),

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
        "page_assets" => "",
        "page_js" => "",
        "page_details" => ""
    ),

    // Subscription details

    'subscription' => TRUE,

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
