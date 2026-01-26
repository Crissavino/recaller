<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clinic_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')->unique()->constrained()->cascadeOnDelete();
            $table->decimal('avg_ticket_value', 10, 2)->default(150.00);
            $table->string('currency', 3)->default('USD');
            $table->string('booking_link')->nullable();
            $table->text('business_hours_text')->nullable();
            $table->integer('followup_delay_seconds')->default(60);
            $table->integer('no_response_timeout_minutes')->default(30);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clinic_settings');
    }
};
