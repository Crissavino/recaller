<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\PlanPrice;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'slug' => 'starter',
                'name' => 'Starter',
                'description' => 'Perfect for small dental practices just getting started with patient recovery.',
                'price_monthly_cents' => 4900,
                'price_annual_cents' => 46800, // €39/month billed annually
                'features' => [
                    'missed_calls_limit' => 50,
                    'sms_included' => 100,
                    'phone_numbers' => 1,
                    'team_members' => 1,
                    'api_access' => false,
                    'priority_support' => false,
                    'custom_templates' => false,
                    'analytics' => 'basic',
                ],
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 1,
                'stripe' => [
                    'product_id' => 'prod_TrWgQGDkXyc5Nv',
                    'eur' => [
                        'monthly' => 'price_1StncsBBNEPwshQEoTy0Pm4e',
                        'annual' => 'price_1StngjBBNEPwshQE0bcSMRYk',
                    ],
                    'ron' => [
                        'monthly' => 'price_PLACEHOLDER_starter_ron_monthly',
                        'annual' => 'price_PLACEHOLDER_starter_ron_annual',
                    ],
                ],
            ],
            [
                'slug' => 'growth',
                'name' => 'Growth',
                'description' => 'Ideal for growing practices that want to maximize patient recovery.',
                'price_monthly_cents' => 9900,
                'price_annual_cents' => 94800, // €79/month billed annually
                'features' => [
                    'missed_calls_limit' => null, // unlimited
                    'sms_included' => 500,
                    'phone_numbers' => 3,
                    'team_members' => 5,
                    'api_access' => false,
                    'priority_support' => true,
                    'custom_templates' => true,
                    'analytics' => 'advanced',
                ],
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 2,
                'stripe' => [
                    'product_id' => 'prod_TrWg5v6msDAOW5',
                    'eur' => [
                        'monthly' => 'price_1StndbBBNEPwshQEITWiyqiZ',
                        'annual' => 'price_1StngNBBNEPwshQENAXowNJo',
                    ],
                    'ron' => [
                        'monthly' => 'price_PLACEHOLDER_growth_ron_monthly',
                        'annual' => 'price_PLACEHOLDER_growth_ron_annual',
                    ],
                ],
            ],
            [
                'slug' => 'pro',
                'name' => 'Pro',
                'description' => 'For multi-location practices and dental groups with advanced needs.',
                'price_monthly_cents' => 19900,
                'price_annual_cents' => 190800, // €159/month billed annually
                'features' => [
                    'missed_calls_limit' => null, // unlimited
                    'sms_included' => 2000,
                    'phone_numbers' => 10,
                    'team_members' => null, // unlimited
                    'api_access' => true,
                    'priority_support' => true,
                    'custom_templates' => true,
                    'analytics' => 'full',
                    'white_label' => true,
                    'dedicated_support' => true,
                ],
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 3,
                'stripe' => [
                    'product_id' => 'prod_TrWhqx8Ei5MPct',
                    'eur' => [
                        'monthly' => 'price_1Stne4BBNEPwshQEdNsz6nXp',
                        'annual' => 'price_1StnfxBBNEPwshQEo0LY7xT3',
                    ],
                    'ron' => [
                        'monthly' => 'price_PLACEHOLDER_pro_ron_monthly',
                        'annual' => 'price_PLACEHOLDER_pro_ron_annual',
                    ],
                ],
            ],
        ];

        foreach ($plans as $planData) {
            $stripeData = $planData['stripe'];
            unset($planData['stripe']);

            $plan = Plan::updateOrCreate(
                ['slug' => $planData['slug']],
                $planData
            );

            // Seed Stripe prices for each currency
            foreach (['eur', 'ron'] as $currency) {
                foreach (['monthly', 'annual'] as $interval) {
                    PlanPrice::updateOrCreate(
                        [
                            'plan_id' => $plan->id,
                            'provider' => 'stripe',
                            'interval' => $interval,
                            'currency' => $currency,
                        ],
                        [
                            'provider_price_id' => $stripeData[$currency][$interval],
                            'provider_product_id' => $stripeData['product_id'],
                            'is_active' => true,
                        ]
                    );
                }
            }
        }

        $this->command->info('Plans and Stripe prices seeded successfully (EUR + RON)!');
    }
}
