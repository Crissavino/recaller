<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('missed_calls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lead_id')->constrained()->cascadeOnDelete();
            $table->foreignId('clinic_phone_number_id')->constrained()->cascadeOnDelete();
            $table->string('caller_phone');
            $table->string('provider_call_id')->nullable();
            $table->integer('ring_duration_seconds')->nullable();
            $table->timestamp('called_at');
            $table->timestamps();

            $table->index(['clinic_id', 'called_at']);
            $table->index('provider_call_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('missed_calls');
    }
};
