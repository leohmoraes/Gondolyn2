<?php

return [

    'shipping' => array(

        'street'        => [],
        'city'          => [],
        'state'         => [
            'type' => 'select',
            'options' => [
                'Alberta' => 'AB',
                'British Columbia' => 'BC',
                'Manitoba' => 'MB',
                'New Brunswick' => 'NB',
                'Newfoundland and Labrador' => 'NL',
                'Northwest Territories' => 'NT',
                'Nova Scotia' => 'NS',
                'Nunavut' => 'NU',
                'Ontario' => 'ON',
                'Prince Edward Island' => 'PE',
                'Quebec' => 'QC',
                'Saskatchewan' => 'SK',
                'Yukon' => 'YT',
            ]
        ],
        'country'       => [
            'type' => 'select',
            'options' => [
                'Canada' => 'CA',
            ]
        ],
        'postal'        => [
            'alt_name' => 'Postal Code'
        ],
        'other'         => [
            'type' => 'textarea'
        ],

    )

];
