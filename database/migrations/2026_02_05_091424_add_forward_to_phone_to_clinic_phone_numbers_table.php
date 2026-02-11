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
        Schema::table('clinic_phone_numbers', function (Blueprint $table) {
            $table->string('forward_to_phone')->nullable()->after('sms_enabled');
            $table->integer('forward_timeout_seconds')->default(20)->after('forward_to_phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clinic_phone_numbers', function (Blueprint $table) {
            $table->dropColumn(['forward_to_phone', 'forward_timeout_seconds']);
        });
    }
};
