<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('plan_prices', 'currency')) {
            Schema::table('plan_prices', function (Blueprint $table) {
                $table->string('currency', 3)->default('eur')->after('interval');
            });
        }

        // Drop foreign key first, then the unique index, then recreate both
        Schema::table('plan_prices', function (Blueprint $table) {
            $table->dropForeign(['plan_id']);
            $table->dropUnique(['plan_id', 'provider', 'interval']);
            $table->unique(['plan_id', 'provider', 'interval', 'currency']);
            $table->foreign('plan_id')->references('id')->on('plans')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('plan_prices', function (Blueprint $table) {
            $table->dropForeign(['plan_id']);
            $table->dropUnique(['plan_id', 'provider', 'interval', 'currency']);
            $table->unique(['plan_id', 'provider', 'interval']);
            $table->foreign('plan_id')->references('id')->on('plans')->cascadeOnDelete();
            $table->dropColumn('currency');
        });
    }
};
