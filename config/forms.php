<?php

return [

    'account' => [

        'user_email'      => [
            'alt_name' => 'Email Address',
        ],
        'user_alt_email'  => [
            'alt_name' => 'Alt. Email Address',
        ],
        'user_name'       => [
            'alt_name' => 'Username',
        ],
        'profile'       => [
            'type' => 'file',
            'alt_name' => 'Profile Image',
        ],
        'in_app_notifications'       => [
            'type' => 'checkbox',
            'alt_name' => 'In App Notifications',
        ]
    ],

    'two-factor' => [
        'two_factor_enabled'       => [
            'alt_name' => 'Enable Two Factor Logins',
            'type' => 'checkbox'
        ],
        'two_factor_phone' => [
            'alt_name' => 'Mobile Phone Number',
            'placeholder' => '+1 (234) 567-8910',
            'type' => 'string'
        ],
    ],

    'shipping' => [

        'street'        => [],
        'city'          => [],
        'state'         => [
            'type' => 'string',
        ],
        'country'       => [
            'type' => 'select',
            'options' => include(__DIR__.'/countries.php'),
        ],
        'postal'        => [
            'alt_name' => 'Postal Code'
        ],
        'other'         => [
            'type' => 'textarea'
        ]
    ]

];
