<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\PlanPrice;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminPlanController extends Controller
{
    public function index(): View
    {
        $plans = Plan::with('prices')->ordered()->get();

        return view('admin.plans.index', compact('plans'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $plan = Plan::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'price_monthly_cents' => 'required|integer|min:0',
            'price_annual_cents' => 'required|integer|min:0',
            'features' => 'nullable|json',
            'sort_order' => 'required|integer|min:0',
        ]);

        if (isset($validated['features'])) {
            $validated['features'] = json_decode($validated['features'], true);
        }

        $plan->update($validated);

        return back()->with('success', "Plan '{$plan->name}' updated.");
    }

    public function toggleActive(int $id): RedirectResponse
    {
        $plan = Plan::findOrFail($id);
        $plan->update(['is_active' => !$plan->is_active]);

        $status = $plan->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "Plan '{$plan->name}' {$status}.");
    }

    public function toggleFeatured(int $id): RedirectResponse
    {
        $plan = Plan::findOrFail($id);
        $plan->update(['is_featured' => !$plan->is_featured]);

        $status = $plan->is_featured ? 'featured' : 'unfeatured';
        return back()->with('success', "Plan '{$plan->name}' {$status}.");
    }

    public function updatePrice(Request $request, int $id): RedirectResponse
    {
        $price = PlanPrice::findOrFail($id);

        $validated = $request->validate([
            'provider_price_id' => 'nullable|string|max:255',
            'provider_product_id' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $price->update($validated);

        return back()->with('success', 'Stripe price updated.');
    }

    public function storePrice(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'provider' => 'required|string|max:50',
            'interval' => 'required|in:monthly,annual',
            'currency' => 'required|string|max:3',
            'provider_price_id' => 'nullable|string|max:255',
            'provider_product_id' => 'nullable|string|max:255',
        ]);

        PlanPrice::updateOrCreate(
            [
                'plan_id' => $validated['plan_id'],
                'provider' => $validated['provider'],
                'interval' => $validated['interval'],
                'currency' => strtolower($validated['currency']),
            ],
            [
                'provider_price_id' => $validated['provider_price_id'],
                'provider_product_id' => $validated['provider_product_id'],
                'is_active' => true,
            ]
        );

        return back()->with('success', 'Price added.');
    }
}
