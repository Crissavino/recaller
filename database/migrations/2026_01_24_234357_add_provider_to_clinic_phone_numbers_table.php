<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('clinic_phone_numbers', function (Blueprint $table) {
            $table->string('provider', 50)->default('twilio')->after('phone_number');
        });

        // Set existing records to twilio
        DB::table('clinic_phone_numbers')->whereNull('provider')->update(['provider' => 'twilio']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clinic_phone_numbers', function (Blueprint $table) {
            $table->dropColumn('provider');
        });
    }
};
