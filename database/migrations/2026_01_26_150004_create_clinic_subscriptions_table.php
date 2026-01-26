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
        Schema::create('clinic_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')->constrained()->cascadeOnDelete();
            $table->foreignId('plan_id')->constrained()->cascadeOnDelete();
            $table->string('provider'); // stripe, paypal, etc.
            $table->string('provider_subscription_id'); // sub_xxx for Stripe
            $table->string('provider_price_id')->nullable();
            $table->string('status'); // active, canceled, past_due, trialing, paused
            $table->string('interval'); // monthly, annual
            $table->integer('quantity')->default(1);
            $table->timestamp('trial_starts_at')->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('current_period_start')->nullable();
            $table->timestamp('current_period_end')->nullable();
            $table->timestamp('canceled_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->json('provider_data')->nullable();
            $table->timestamps();

            $table->index(['clinic_id', 'status']);
            $table->index(['provider', 'provider_subscription_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clinic_subscriptions');
    }
};
