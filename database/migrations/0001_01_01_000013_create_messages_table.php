<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')->constrained()->cascadeOnDelete();
            $table->foreignId('conversation_id')->constrained()->cascadeOnDelete();
            $table->string('channel');
            $table->string('direction');
            $table->string('from_phone');
            $table->string('to_phone');
            $table->text('body');
            $table->string('provider_message_id')->nullable();
            $table->string('status')->default('pending');
            $table->foreignId('sent_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['conversation_id', 'created_at']);
            $table->index('provider_message_id');
            $table->index(['clinic_id', 'direction']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
