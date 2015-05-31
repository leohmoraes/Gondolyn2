<?php

return array(

    'app_admin_email' => '',
    'app_admin_name' => '',

    'max_file_upload_size' => 6291456, // 6MB

    'signup' => TRUE,

    'smLogin' => FALSE,

    'authKeys' => [
        'XdTSB0kgxuEVzd8NSDejjeVUMJuj',
        'BsmyFfqhgKxNfbIOpRChuQBN5wIR',
        'fdbRFxqlNQC4BaSQmBFLsh3X7EKd',
        'at4qGfilNIHBzkGavy1RaHDCYHz6',
        'lkjwNoXuEfQsXx3wRKHniJfsqS5p',
        'zFvV0PrOdpiRE8kCGn8x7QVqAaT1',
        '3ZqDEWyFL0tZpLR3TbFzzO7GF37f',
        'd0gg0HTFDrkpsNoRzfUsrA20paH4',
        'dOkrPBHPiAuV2PluCJlbZgEqRHqh',
        'S7m6WGxLheBAP6wRVRmyBIKAZp1Q',
        '7s7ZAt6waDisSU2H0yzWpWu1EfBD',
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
        "page_assets" => "",
        "page_js" => "",
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
