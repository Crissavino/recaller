<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clinic_phone_numbers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')->constrained()->cascadeOnDelete();
            $table->foreignId('integration_id')->constrained()->cascadeOnDelete();
            $table->string('phone_number');
            $table->string('friendly_name')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('voice_enabled')->default(true);
            $table->boolean('sms_enabled')->default(true);
            $table->timestamps();

            $table->unique('phone_number');
            $table->index('clinic_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clinic_phone_numbers');
    }
};
