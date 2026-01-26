<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('plan_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained()->cascadeOnDelete();
            $table->string('provider'); // stripe, paypal, mercadopago, etc.
            $table->string('interval'); // monthly, annual
            $table->string('provider_price_id'); // price_xxx for Stripe
            $table->string('provider_product_id')->nullable(); // prod_xxx for Stripe
            $table->json('provider_data')->nullable(); // Additional provider-specific data
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['plan_id', 'provider', 'interval']);
            $table->index(['provider', 'provider_price_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plan_prices');
    }
};
