<?php

namespace Database\Seeders;

use App\Models\Plan;
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
                'price_annual_cents' => 46800, // $39/month billed annually
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
            ],
            [
                'slug' => 'growth',
                'name' => 'Growth',
                'description' => 'Ideal for growing practices that want to maximize patient recovery.',
                'price_monthly_cents' => 9900,
                'price_annual_cents' => 94800, // $79/month billed annually
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
            ],
            [
                'slug' => 'pro',
                'name' => 'Pro',
                'description' => 'For multi-location practices and dental groups with advanced needs.',
                'price_monthly_cents' => 19900,
                'price_annual_cents' => 190800, // $159/month billed annually
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
            ],
        ];

        foreach ($plans as $planData) {
            Plan::updateOrCreate(
                ['slug' => $planData['slug']],
                $planData
            );
        }

        $this->command->info('Plans seeded successfully!');
        $this->command->info('Run "php artisan stripe:sync-prices" to configure Stripe price IDs.');
    }
}
