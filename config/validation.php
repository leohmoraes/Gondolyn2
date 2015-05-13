<?php

return [

    'conditions' => [

        'login_email' => [
            'email' => ['required', 'email'],
            'password' => ['required', 'min:8']
        ]

    ],

    'admin' => [
        'street' => ['required'],
        'city' => ['required'],
    ]

];
