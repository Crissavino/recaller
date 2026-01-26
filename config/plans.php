<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Subscription Plans
    |--------------------------------------------------------------------------
    |
    | Define your subscription plans here. Each plan should have a unique key
    | and contain the Stripe Price IDs for monthly and annual billing.
    |
    | You'll need to create these products and prices in your Stripe dashboard
    | and copy the Price IDs here.
    |
    */

    'plans' => [
        'starter' => [
            'name' => 'Starter',
            'stripe_monthly_price_id' => env('STRIPE_STARTER_MONTHLY_PRICE_ID'),
            'stripe_annual_price_id' => env('STRIPE_STARTER_ANNUAL_PRICE_ID'),
            'features' => [
                'missed_calls_limit' => 50,
                'sms_included' => 100,
                'phone_numbers' => 1,
                'team_members' => 1,
                'api_access' => false,
            ],
        ],

        'growth' => [
            'name' => 'Growth',
            'stripe_monthly_price_id' => env('STRIPE_GROWTH_MONTHLY_PRICE_ID'),
            'stripe_annual_price_id' => env('STRIPE_GROWTH_ANNUAL_PRICE_ID'),
            'features' => [
                'missed_calls_limit' => null, // unlimited
                'sms_included' => 500,
                'phone_numbers' => 3,
                'team_members' => 5,
                'api_access' => false,
            ],
        ],

        'pro' => [
            'name' => 'Pro',
            'stripe_monthly_price_id' => env('STRIPE_PRO_MONTHLY_PRICE_ID'),
            'stripe_annual_price_id' => env('STRIPE_PRO_ANNUAL_PRICE_ID'),
            'features' => [
                'missed_calls_limit' => null, // unlimited
                'sms_included' => 2000,
                'phone_numbers' => 10,
                'team_members' => null, // unlimited
                'api_access' => true,
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Trial Period
    |--------------------------------------------------------------------------
    |
    | The number of days for the free trial period.
    |
    */

    'trial_days' => env('TRIAL_DAYS', 14),

    /*
    |--------------------------------------------------------------------------
    | Default Plan
    |--------------------------------------------------------------------------
    |
    | The default plan to use when a user signs up for a trial.
    |
    */

    'default_plan' => env('DEFAULT_PLAN', 'growth'),
];
