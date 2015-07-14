<?php

return [

    'conditions' => [

        'login_email' => [
            'email' => ['required', 'email'],
            'password' => ['required', 'min:8']
        ],

        'create_account' => [
            'user_email' => ['required', 'email', 'unique:users'],
            'user_name' => ['required'],
            'role' => ['required'],
        ],

        'update_account' => [
            'user_email' => ['required', 'email'],
            'user_name' => ['required'],
            'country' => ['required'],
            'state' => ['required'],
        ],

    ],

    'admin' => [
        'street' => ['required'],
        'city' => ['required'],
    ],

];
