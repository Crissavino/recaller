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
            // Add provider SID for Twilio phone number management
            $table->string('provider_sid')->nullable()->after('provider');

            // Make integration_id nullable since we now use global Twilio credentials
            $table->foreignId('integration_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clinic_phone_numbers', function (Blueprint $table) {
            $table->dropColumn('provider_sid');
            $table->foreignId('integration_id')->nullable(false)->change();
        });
    }
};
