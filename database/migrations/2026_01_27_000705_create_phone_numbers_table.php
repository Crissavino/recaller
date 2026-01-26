<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('phone_numbers', function (Blueprint $table) {
            $table->id();
            $table->string('phone_number')->unique();
            $table->string('provider')->default('twilio');
            $table->string('provider_sid')->nullable();
            $table->string('friendly_name')->nullable();
            $table->string('country_code', 2)->default('US');
            $table->foreignId('clinic_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('status', ['available', 'assigned', 'reserved', 'released'])->default('available');
            $table->boolean('voice_enabled')->default(true);
            $table->boolean('sms_enabled')->default(true);
            $table->decimal('monthly_cost', 8, 2)->nullable();
            $table->timestamp('purchased_at')->nullable();
            $table->timestamp('assigned_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('clinic_id');
            $table->index('country_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('phone_numbers');
    }
};
