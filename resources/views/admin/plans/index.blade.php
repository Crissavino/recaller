<x-admin-layout>
    <x-slot name="header">
        <h2 style="font-weight: 600; font-size: 20px; color: #1e293b; margin: 0;">
            Plans & Prices
        </h2>
    </x-slot>

    @if(session('success'))
        <div style="background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; padding: 14px 16px; border-radius: 10px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; font-size: 14px;" x-data="{ show: true }" x-show="show" x-transition>
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span style="flex: 1;">{{ session('success') }}</span>
            <button @click="show = false" style="background: none; border: none; cursor: pointer; color: #166534; opacity: 0.6;">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 14px 16px; border-radius: 10px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; font-size: 14px;" x-data="{ show: true }" x-show="show" x-transition>
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span style="flex: 1;">{{ session('error') }}</span>
            <button @click="show = false" style="background: none; border: none; cursor: pointer; color: #991b1b; opacity: 0.6;">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    @endif

    @foreach($plans as $plan)
        <div style="background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; margin-bottom: 24px;" x-data="{ editing: false, showAddPrice: false }">
            <!-- Plan Header -->
            <div style="padding: 20px 24px; display: flex; justify-content: space-between; align-items: flex-start; gap: 16px;">
                <div style="flex: 1; min-width: 0;">
                    <!-- View Mode -->
                    <div x-show="!editing">
                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 6px;">
                            <h3 style="font-weight: 700; color: #1e293b; font-size: 18px; margin: 0;">{{ $plan->name }}</h3>
                            <span style="font-size: 12px; color: #94a3b8; font-family: ui-monospace, monospace;">{{ $plan->slug }}</span>
                            @if($plan->is_active)
                                <span style="background: #dcfce7; color: #166534; font-size: 11px; padding: 2px 8px; border-radius: 9999px; font-weight: 600;">Active</span>
                            @else
                                <span style="background: #f1f5f9; color: #64748b; font-size: 11px; padding: 2px 8px; border-radius: 9999px; font-weight: 600;">Inactive</span>
                            @endif
                            @if($plan->is_featured)
                                <span style="background: #fef3c7; color: #92400e; font-size: 11px; padding: 2px 8px; border-radius: 9999px; font-weight: 600;">Featured</span>
                            @endif
                        </div>
                        <p style="color: #64748b; font-size: 14px; margin: 0 0 10px 0;">{{ $plan->description }}</p>
                        <div style="display: flex; gap: 20px; align-items: center; margin-bottom: 10px;">
                            <span style="color: #334155; font-size: 14px;">Monthly: <strong style="color: #1e293b;">${{ number_format($plan->monthly_price, 2) }}</strong></span>
                            <span style="color: #334155; font-size: 14px;">Annual: <strong style="color: #1e293b;">${{ number_format($plan->annual_price, 2) }}</strong></span>
                            <span style="color: #94a3b8; font-size: 13px;">Sort: {{ $plan->sort_order }}</span>
                            @php
                                $currencies = $plan->prices->pluck('currency')->unique()->map(fn($c) => strtoupper($c))->sort()->values();
                            @endphp
                            @if($currencies->isNotEmpty())
                                <span style="color: #94a3b8; font-size: 13px;">Currencies:
                                    @foreach($currencies as $cur)
                                        <span style="background: #f1f5f9; color: #475569; font-size: 11px; padding: 2px 6px; border-radius: 4px; font-weight: 600; margin-left: 2px;">{{ $cur }}</span>
                                    @endforeach
                                </span>
                            @endif
                        </div>
                        @if($plan->features && is_array($plan->features))
                            <div style="display: flex; flex-wrap: wrap; gap: 6px;">
                                @foreach($plan->features as $key => $value)
                                    @php
                                        $label = ucwords(str_replace('_', ' ', $key));
                                        if (is_bool($value) || $value === 0 || $value === 1 && !is_string($value)) {
                                            // Skip false booleans
                                            if (!$value) continue;
                                            $display = $label;
                                            $bg = '#dcfce7'; $color = '#166534';
                                        } elseif (is_null($value)) {
                                            $display = $label . ': Unlimited';
                                            $bg = '#ede9fe'; $color = '#5b21b6';
                                        } else {
                                            $display = $label . ': ' . $value;
                                            $bg = '#f1f5f9'; $color = '#475569';
                                        }
                                    @endphp
                                    <span style="background: {{ $bg }}; color: {{ $color }}; font-size: 11px; padding: 3px 8px; border-radius: 6px; font-weight: 500;">{{ $display }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Edit Mode -->
                    <div x-show="editing" x-transition>
                        <form action="{{ route('admin.plans.update', $plan->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 12px; margin-bottom: 12px;">
                                <div>
                                    <label style="display: block; font-size: 12px; font-weight: 500; color: #64748b; margin-bottom: 4px;">Name</label>
                                    <input type="text" name="name" value="{{ $plan->name }}" required
                                        style="width: 100%; padding: 8px 10px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px; background: #fff; color: #1e293b;">
                                </div>
                                <div>
                                    <label style="display: block; font-size: 12px; font-weight: 500; color: #64748b; margin-bottom: 4px;">Description</label>
                                    <input type="text" name="description" value="{{ $plan->description }}"
                                        style="width: 100%; padding: 8px 10px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px; background: #fff; color: #1e293b;">
                                </div>
                                <div>
                                    <label style="display: block; font-size: 12px; font-weight: 500; color: #64748b; margin-bottom: 4px;">Monthly (cents)</label>
                                    <input type="number" name="price_monthly_cents" value="{{ $plan->price_monthly_cents }}" required min="0"
                                        style="width: 100%; padding: 8px 10px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px; background: #fff; color: #1e293b;">
                                </div>
                                <div>
                                    <label style="display: block; font-size: 12px; font-weight: 500; color: #64748b; margin-bottom: 4px;">Annual (cents)</label>
                                    <input type="number" name="price_annual_cents" value="{{ $plan->price_annual_cents }}" required min="0"
                                        style="width: 100%; padding: 8px 10px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px; background: #fff; color: #1e293b;">
                                </div>
                                <div>
                                    <label style="display: block; font-size: 12px; font-weight: 500; color: #64748b; margin-bottom: 4px;">Sort Order</label>
                                    <input type="number" name="sort_order" value="{{ $plan->sort_order }}" required min="0"
                                        style="width: 100%; padding: 8px 10px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px; background: #fff; color: #1e293b;">
                                </div>
                                <div style="grid-column: 1 / -1;">
                                    <label style="display: block; font-size: 12px; font-weight: 500; color: #64748b; margin-bottom: 4px;">Features (JSON)</label>
                                    <input type="text" name="features" value="{{ json_encode($plan->features) }}"
                                        style="width: 100%; padding: 8px 10px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px; background: #fff; color: #1e293b; font-family: ui-monospace, monospace;">
                                </div>
                            </div>
                            <div style="display: flex; gap: 8px;">
                                <button type="submit" style="background: #7c3aed; color: #fff; padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 500; border: none; cursor: pointer;">
                                    Save Changes
                                </button>
                                <button type="button" @click="editing = false" style="background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; padding: 8px 16px; border-radius: 8px; font-size: 13px; cursor: pointer;">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div x-show="!editing" style="display: inline-flex; gap: 6px; flex-shrink: 0;">
                    <button @click="editing = true" style="background: #f1f5f9; border: 1px solid #e2e8f0; padding: 6px 14px; border-radius: 8px; font-size: 12px; color: #475569; cursor: pointer; font-weight: 500; white-space: nowrap;">
                        Edit
                    </button>
                    <form action="{{ route('admin.plans.toggle-active', $plan->id) }}" method="POST" style="margin: 0; display: inline;">
                        @csrf
                        @method('PATCH')
                        <button type="submit" style="background: {{ $plan->is_active ? '#fef2f2' : '#f0fdf4' }}; border: 1px solid {{ $plan->is_active ? '#fecaca' : '#bbf7d0' }}; padding: 6px 14px; border-radius: 8px; font-size: 12px; color: {{ $plan->is_active ? '#991b1b' : '#166534' }}; cursor: pointer; font-weight: 500; white-space: nowrap;">
                            {{ $plan->is_active ? 'Deactivate' : 'Activate' }}
                        </button>
                    </form>
                    <form action="{{ route('admin.plans.toggle-featured', $plan->id) }}" method="POST" style="margin: 0; display: inline;">
                        @csrf
                        @method('PATCH')
                        <button type="submit" style="background: {{ $plan->is_featured ? '#fef3c7' : '#f1f5f9' }}; border: 1px solid {{ $plan->is_featured ? '#fde68a' : '#e2e8f0' }}; padding: 6px 14px; border-radius: 8px; font-size: 12px; color: {{ $plan->is_featured ? '#92400e' : '#475569' }}; cursor: pointer; font-weight: 500; white-space: nowrap;">
                            {{ $plan->is_featured ? 'Unfeature' : 'Feature' }}
                        </button>
                    </form>
                </div>
            </div>

            <!-- Stripe Prices Table -->
            <div style="border-top: 1px solid #e2e8f0;">
                <div style="padding: 12px 24px; display: flex; justify-content: space-between; align-items: center; background: #f8fafc;">
                    <h4 style="font-weight: 600; color: #64748b; font-size: 12px; margin: 0; text-transform: uppercase; letter-spacing: 0.5px;">Stripe Prices</h4>
                    <button @click="showAddPrice = !showAddPrice" style="display: inline-flex; align-items: center; gap: 4px; background: #fff; border: 1px solid #e2e8f0; color: #475569; padding: 5px 12px; border-radius: 6px; font-size: 12px; cursor: pointer; font-weight: 500;">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Add Price
                    </button>
                </div>

                <!-- Add Price Form -->
                <div x-show="showAddPrice" x-transition style="padding: 16px 24px; border-top: 1px solid #e2e8f0; background: #f8fafc;">
                    <form action="{{ route('admin.plan-prices.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 12px; margin-bottom: 12px;">
                            <div>
                                <label style="display: block; font-size: 12px; font-weight: 500; color: #64748b; margin-bottom: 4px;">Provider</label>
                                <select name="provider" required style="width: 100%; padding: 8px 10px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px; background: #fff; color: #1e293b;">
                                    <option value="stripe">Stripe</option>
                                </select>
                            </div>
                            <div>
                                <label style="display: block; font-size: 12px; font-weight: 500; color: #64748b; margin-bottom: 4px;">Interval</label>
                                <select name="interval" required style="width: 100%; padding: 8px 10px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px; background: #fff; color: #1e293b;">
                                    <option value="monthly">Monthly</option>
                                    <option value="annual">Annual</option>
                                </select>
                            </div>
                            <div>
                                <label style="display: block; font-size: 12px; font-weight: 500; color: #64748b; margin-bottom: 4px;">Currency</label>
                                <input type="text" name="currency" placeholder="eur" maxlength="3" required
                                    style="width: 100%; padding: 8px 10px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px; background: #fff; color: #1e293b;">
                            </div>
                            <div>
                                <label style="display: block; font-size: 12px; font-weight: 500; color: #64748b; margin-bottom: 4px;">Stripe Price ID</label>
                                <input type="text" name="provider_price_id" placeholder="price_..."
                                    style="width: 100%; padding: 8px 10px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px; background: #fff; color: #1e293b; font-family: ui-monospace, monospace;">
                            </div>
                            <div>
                                <label style="display: block; font-size: 12px; font-weight: 500; color: #64748b; margin-bottom: 4px;">Stripe Product ID</label>
                                <input type="text" name="provider_product_id" placeholder="prod_..."
                                    style="width: 100%; padding: 8px 10px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px; background: #fff; color: #1e293b; font-family: ui-monospace, monospace;">
                            </div>
                        </div>
                        <div style="display: flex; gap: 8px;">
                            <button type="submit" style="background: #7c3aed; color: #fff; padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 500; border: none; cursor: pointer;">
                                Add Price
                            </button>
                            <button type="button" @click="showAddPrice = false" style="background: #fff; color: #475569; border: 1px solid #e2e8f0; padding: 8px 16px; border-radius: 8px; font-size: 13px; cursor: pointer;">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>

                @if($plan->prices->isNotEmpty())
                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="background: #f8fafc;">
                                    <th style="padding: 10px 16px; text-align: left; font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase;">Currency</th>
                                    <th style="padding: 10px 16px; text-align: left; font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase;">Interval</th>
                                    <th style="padding: 10px 16px; text-align: left; font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase;">Price ID</th>
                                    <th style="padding: 10px 16px; text-align: left; font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase;">Product ID</th>
                                    <th style="padding: 10px 16px; text-align: left; font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase;">Status</th>
                                    <th style="padding: 10px 16px; text-align: right; font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($plan->prices as $price)
                                    <tr style="border-top: 1px solid #f1f5f9;" x-data="{ editingPrice: false }">
                                        <!-- View Mode -->
                                        <td x-show="!editingPrice" style="padding: 12px 16px;">
                                            <span style="background: #ede9fe; color: #5b21b6; font-size: 11px; padding: 3px 8px; border-radius: 4px; font-weight: 600; text-transform: uppercase;">{{ $price->currency ?: '—' }}</span>
                                        </td>
                                        <td x-show="!editingPrice" style="padding: 12px 16px; color: #334155; font-size: 13px;">{{ ucfirst($price->interval) }}</td>
                                        <td x-show="!editingPrice" style="padding: 12px 16px;">
                                            @if($price->provider_price_id)
                                                <code style="background: #f1f5f9; color: #7c3aed; font-size: 12px; padding: 3px 8px; border-radius: 4px; border: 1px solid #e2e8f0;">{{ $price->provider_price_id }}</code>
                                            @else
                                                <span style="color: #cbd5e1; font-size: 12px;">Not set</span>
                                            @endif
                                        </td>
                                        <td x-show="!editingPrice" style="padding: 12px 16px;">
                                            @if($price->provider_product_id)
                                                <code style="background: #f1f5f9; color: #7c3aed; font-size: 12px; padding: 3px 8px; border-radius: 4px; border: 1px solid #e2e8f0;">{{ $price->provider_product_id }}</code>
                                            @else
                                                <span style="color: #cbd5e1; font-size: 12px;">Not set</span>
                                            @endif
                                        </td>
                                        <td x-show="!editingPrice" style="padding: 12px 16px;">
                                            @if($price->is_active)
                                                <span style="background: #dcfce7; color: #166534; font-size: 11px; padding: 2px 8px; border-radius: 9999px; font-weight: 600;">Active</span>
                                            @else
                                                <span style="background: #f1f5f9; color: #64748b; font-size: 11px; padding: 2px 8px; border-radius: 9999px; font-weight: 600;">Inactive</span>
                                            @endif
                                        </td>
                                        <td x-show="!editingPrice" style="padding: 12px 16px; text-align: right;">
                                            <button @click="editingPrice = true" style="background: #f1f5f9; border: 1px solid #e2e8f0; padding: 5px 12px; border-radius: 6px; font-size: 11px; color: #475569; cursor: pointer; font-weight: 500;">
                                                Edit
                                            </button>
                                        </td>

                                        <!-- Edit Mode -->
                                        <td x-show="editingPrice" colspan="6" style="padding: 12px 16px; background: #f8fafc;">
                                            <form action="{{ route('admin.plan-prices.update', $price->id) }}" method="POST" style="display: flex; align-items: flex-end; gap: 12px; flex-wrap: wrap;">
                                                @csrf
                                                @method('PUT')
                                                <div style="flex-shrink: 0;">
                                                    <span style="display: block; font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 4px;">{{ strtoupper($price->currency ?: '?') }} / {{ ucfirst($price->interval) }}</span>
                                                </div>
                                                <div>
                                                    <label style="display: block; font-size: 12px; font-weight: 500; color: #64748b; margin-bottom: 4px;">Price ID</label>
                                                    <input type="text" name="provider_price_id" value="{{ $price->provider_price_id }}" placeholder="price_..."
                                                        style="width: 260px; padding: 7px 10px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 12px; background: #fff; color: #1e293b; font-family: ui-monospace, monospace;">
                                                </div>
                                                <div>
                                                    <label style="display: block; font-size: 12px; font-weight: 500; color: #64748b; margin-bottom: 4px;">Product ID</label>
                                                    <input type="text" name="provider_product_id" value="{{ $price->provider_product_id }}" placeholder="prod_..."
                                                        style="width: 260px; padding: 7px 10px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 12px; background: #fff; color: #1e293b; font-family: ui-monospace, monospace;">
                                                </div>
                                                <div style="display: flex; align-items: center; gap: 6px;">
                                                    <input type="checkbox" name="is_active" id="price_active_{{ $price->id }}" value="1" {{ $price->is_active ? 'checked' : '' }}
                                                        style="width: 16px; height: 16px; accent-color: #7c3aed;">
                                                    <label for="price_active_{{ $price->id }}" style="font-size: 12px; color: #334155;">Active</label>
                                                </div>
                                                <div style="display: flex; gap: 6px;">
                                                    <button type="submit" style="background: #7c3aed; color: #fff; padding: 7px 14px; border-radius: 8px; font-size: 12px; font-weight: 500; border: none; cursor: pointer;">
                                                        Save
                                                    </button>
                                                    <button type="button" @click="editingPrice = false" style="background: #fff; color: #475569; border: 1px solid #e2e8f0; padding: 7px 14px; border-radius: 8px; font-size: 12px; cursor: pointer;">
                                                        Cancel
                                                    </button>
                                                </div>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div style="padding: 24px; text-align: center; border-top: 1px solid #f1f5f9;">
                        <p style="color: #94a3b8; font-size: 13px; margin: 0;">No Stripe prices configured. Click "Add Price" to create one.</p>
                    </div>
                @endif
            </div>
        </div>
    @endforeach

    @if($plans->isEmpty())
        <div style="background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 48px 20px; text-align: center;">
            <div style="width: 64px; height: 64px; background: #f1f5f9; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                <svg width="32" height="32" fill="none" stroke="#94a3b8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
            </div>
            <p style="color: #64748b; font-size: 14px; margin: 0;">No plans found. Run the PlanSeeder to create plans.</p>
        </div>
    @endif
</x-admin-layout>
