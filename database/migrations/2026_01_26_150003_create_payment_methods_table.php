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
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')->constrained()->cascadeOnDelete();
            $table->string('provider'); // stripe, paypal, etc.
            $table->string('provider_payment_method_id'); // pm_xxx for Stripe
            $table->string('type'); // card, bank_account, paypal, etc.
            $table->string('brand')->nullable(); // visa, mastercard, amex
            $table->string('last_four')->nullable();
            $table->integer('exp_month')->nullable();
            $table->integer('exp_year')->nullable();
            $table->boolean('is_default')->default(false);
            $table->json('provider_data')->nullable();
            $table->timestamps();

            $table->index(['clinic_id', 'provider']);
            $table->index(['provider', 'provider_payment_method_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
