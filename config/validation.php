<?php

return array(

    'conditions' => array(

        'login/email' => array(
            'email' => array('required', 'email'),
            'password' => array('required', 'min:8')
        )

    )

);
