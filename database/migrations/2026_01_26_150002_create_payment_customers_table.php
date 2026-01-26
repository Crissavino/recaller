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
        Schema::create('payment_customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')->constrained()->cascadeOnDelete();
            $table->string('provider'); // stripe, paypal, mercadopago, etc.
            $table->string('provider_customer_id'); // cus_xxx for Stripe
            $table->string('email')->nullable();
            $table->string('name')->nullable();
            $table->json('provider_data')->nullable(); // Additional provider-specific data
            $table->timestamps();

            $table->unique(['clinic_id', 'provider']);
            $table->index(['provider', 'provider_customer_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_customers');
    }
};
