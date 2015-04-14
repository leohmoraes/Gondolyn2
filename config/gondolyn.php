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

    'remember_me_duration' => 20160,

    'basic-app-info' => array(
        "page_title" => "A seed application for Laravel 4",
        "page_keywords" => "Gondolyn, gondolin, Laravel 4, Seed",
        "page_description" => "A clean seed application for Laravel 4",
        "page_assets" => "",
        "page_js" => "",
        "page_details" => ""
    ),

    'roles' => array(
        'admin',
        'member'
    ),

    // Subscription details

    'subscription' => TRUE,

    'company' => '',

    'product' => '',

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
