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
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')->constrained()->cascadeOnDelete();
            $table->foreignId('clinic_subscription_id')->nullable()->constrained()->nullOnDelete();
            $table->string('provider'); // stripe, paypal, etc.
            $table->string('provider_transaction_id'); // in_xxx, ch_xxx for Stripe
            $table->string('type'); // charge, refund, invoice
            $table->string('status'); // succeeded, pending, failed
            $table->integer('amount_cents');
            $table->string('currency', 3)->default('usd');
            $table->text('description')->nullable();
            $table->json('provider_data')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['clinic_id', 'type']);
            $table->index(['provider', 'provider_transaction_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
