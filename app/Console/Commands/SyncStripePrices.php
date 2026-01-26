<?php

namespace App\Console\Commands;

use App\Models\Plan;
use App\Models\PlanPrice;
use Illuminate\Console\Command;

class SyncStripePrices extends Command
{
    protected $signature = 'stripe:sync-prices
                            {--plan= : Sync only a specific plan by slug}
                            {--list : List current price configuration}';

    protected $description = 'Sync Stripe price IDs with the database';

    public function handle(): int
    {
        if ($this->option('list')) {
            return $this->listPrices();
        }

        $planSlug = $this->option('plan');

        if ($planSlug) {
            $plan = Plan::where('slug', $planSlug)->first();
            if (!$plan) {
                $this->error("Plan '{$planSlug}' not found.");
                return 1;
            }
            $this->syncPlan($plan);
        } else {
            $plans = Plan::all();
            foreach ($plans as $plan) {
                $this->syncPlan($plan);
            }
        }

        $this->newLine();
        $this->info('Stripe prices synced successfully!');

        return 0;
    }

    protected function listPrices(): int
    {
        $plans = Plan::with('prices')->ordered()->get();

        $this->table(
            ['Plan', 'Provider', 'Interval', 'Price ID', 'Active'],
            $plans->flatMap(function ($plan) {
                if ($plan->prices->isEmpty()) {
                    return [[
                        $plan->name,
                        '-',
                        '-',
                        'No prices configured',
                        '-',
                    ]];
                }
                return $plan->prices->map(function ($price) use ($plan) {
                    return [
                        $plan->name,
                        $price->provider,
                        $price->interval,
                        $price->provider_price_id ?: '(not set)',
                        $price->is_active ? 'Yes' : 'No',
                    ];
                });
            })
        );

        return 0;
    }

    protected function syncPlan(Plan $plan): void
    {
        $this->info("Configuring prices for: {$plan->name}");

        foreach (['monthly', 'annual'] as $interval) {
            $existingPrice = $plan->prices()
                ->where('provider', 'stripe')
                ->where('interval', $interval)
                ->first();

            $currentPriceId = $existingPrice?->provider_price_id;
            $displayCurrent = $currentPriceId ? " [current: {$currentPriceId}]" : '';

            $priceId = $this->ask(
                "  Stripe {$interval} price ID{$displayCurrent}",
                $currentPriceId
            );

            if ($priceId) {
                PlanPrice::updateOrCreate(
                    [
                        'plan_id' => $plan->id,
                        'provider' => 'stripe',
                        'interval' => $interval,
                    ],
                    [
                        'provider_price_id' => $priceId,
                        'is_active' => true,
                    ]
                );
                $this->line("    ✓ {$interval} price saved");
            }
        }
    }
}
