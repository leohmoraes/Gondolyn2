<?php

return [

    'conditions' => [

        'login_email' => [
            'email' => ['required', 'email'],
            'password' => ['required', 'min:8']
        ]

    ],
    ''

];
