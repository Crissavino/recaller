<?php

namespace App\Console\Commands;

use App\Models\Plan;
use App\Models\PlanPrice;
use Illuminate\Console\Command;
use Stripe\Price;
use Stripe\Product;
use Stripe\Stripe;

class SyncStripePrices extends Command
{
    protected $signature = 'stripe:sync-prices
                            {--plan= : Sync only a specific plan by slug}
                            {--list : List current price configuration}
                            {--fetch : Fetch prices from Stripe API and auto-match to plans}';

    protected $description = 'Sync Stripe price IDs with the database';

    public function handle(): int
    {
        if ($this->option('list')) {
            return $this->listPrices();
        }

        if ($this->option('fetch')) {
            return $this->fetchFromStripe();
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

    protected function fetchFromStripe(): int
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $this->info('Fetching active products from Stripe...');

        $products = Product::all(['active' => true, 'limit' => 100]);
        $plans = Plan::all();

        $nameToSlug = [];
        foreach ($plans as $plan) {
            $nameToSlug[strtolower($plan->name)] = $plan;
        }

        $matched = 0;

        foreach ($products->data as $product) {
            $productName = strtolower($product->name);

            // Match by product name containing plan name (e.g. "Recaller Growth" matches "growth")
            $matchedPlan = null;
            foreach ($nameToSlug as $slug => $plan) {
                if (str_contains($productName, $slug)) {
                    $matchedPlan = $plan;
                    break;
                }
            }

            if (!$matchedPlan) {
                $this->warn("  Skipping Stripe product '{$product->name}' — no matching plan in DB");
                continue;
            }

            $this->info("  Product '{$product->name}' → Plan '{$matchedPlan->name}'");

            $prices = Price::all([
                'product' => $product->id,
                'active' => true,
                'limit' => 100,
            ]);

            foreach ($prices->data as $price) {
                if ($price->type !== 'recurring') {
                    continue;
                }

                $interval = $price->recurring->interval === 'year' ? 'annual' : 'monthly';
                $currency = $price->currency;

                PlanPrice::updateOrCreate(
                    [
                        'plan_id' => $matchedPlan->id,
                        'provider' => 'stripe',
                        'interval' => $interval,
                        'currency' => $currency,
                    ],
                    [
                        'provider_price_id' => $price->id,
                        'provider_product_id' => $product->id,
                        'is_active' => true,
                    ]
                );

                $amount = $price->unit_amount / 100;
                $this->line("    ✓ {$currency} {$interval}: {$price->id} ({$amount} {$currency}/{$price->recurring->interval})");
                $matched++;
            }
        }

        $this->newLine();

        if ($matched === 0) {
            $this->warn('No prices were matched. Make sure Stripe product names contain the plan name (starter, growth, pro).');
            return 1;
        }

        $this->info("{$matched} prices synced from Stripe!");

        // Show final state
        $this->newLine();
        return $this->listPrices();
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
