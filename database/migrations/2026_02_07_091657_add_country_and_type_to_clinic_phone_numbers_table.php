<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clinic_phone_numbers', function (Blueprint $table) {
            $table->string('country', 2)->nullable()->after('phone_number'); // ISO 3166-1 alpha-2 (RO, ES, NL)
            $table->string('type')->default('voice')->after('country'); // voice, whatsapp
            $table->foreignId('linked_whatsapp_number_id')->nullable()->after('forward_timeout_seconds');

            $table->index(['clinic_id', 'type']);
            $table->index('country');
        });
    }

    public function down(): void
    {
        Schema::table('clinic_phone_numbers', function (Blueprint $table) {
            $table->dropIndex(['clinic_id', 'type']);
            $table->dropIndex(['country']);
            $table->dropColumn(['country', 'type', 'linked_whatsapp_number_id']);
        });
    }
};
