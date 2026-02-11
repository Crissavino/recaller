<?php

return [
    'currencies' => ['eur', 'ron'],

    'plans' => [
        'starter' => [
            'eur' => [
                'monthly' => 49,
                'annual' => 39,
                'sms_extra' => '€0.08',
            ],
            'ron' => [
                'monthly' => 249,
                'annual' => 199,
                'sms_extra' => '0,40 RON',
            ],
        ],
        'growth' => [
            'eur' => [
                'monthly' => 99,
                'annual' => 79,
                'sms_extra' => '€0.05',
            ],
            'ron' => [
                'monthly' => 499,
                'annual' => 399,
                'sms_extra' => '0,25 RON',
            ],
        ],
        'pro' => [
            'eur' => [
                'monthly' => 199,
                'annual' => 159,
                'sms_extra' => '€0.03',
            ],
            'ron' => [
                'monthly' => 999,
                'annual' => 799,
                'sms_extra' => '0,15 RON',
            ],
        ],
    ],
];
