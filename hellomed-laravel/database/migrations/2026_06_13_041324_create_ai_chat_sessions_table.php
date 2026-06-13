<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_chat_sessions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('session_id', 64)->unique();   // UUID generated client-side
            $table->json('messages');                      // [{role, content, timestamp}]
            $table->json('suggested_doctor_ids')->nullable();
            $table->json('suggested_article_ids')->nullable();
            $table->json('suggested_test_ids')->nullable();
            $table->string('primary_department', 100)->nullable();
            $table->enum('last_intent', ['health', 'howto', 'error'])->nullable();
            $table->enum('urgency_level', ['low', 'moderate', 'high'])->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_chat_sessions');
    }
};
