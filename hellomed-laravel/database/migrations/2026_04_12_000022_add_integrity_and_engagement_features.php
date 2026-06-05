<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('medicine_orders', function (Blueprint $table): void {
            $table->string('payment_callback_token', 100)->nullable()->after('payment_method');
            $table->string('payment_reference')->nullable()->after('payment_status');
            $table->timestamp('inventory_committed_at')->nullable()->after('contains_prescription_items');
            $table->timestamp('inventory_released_at')->nullable()->after('inventory_committed_at');
            $table->index('payment_callback_token');
        });

        Schema::table('appointments', function (Blueprint $table): void {
            $table->text('prescription_safety_notes')->nullable()->after('prescription_advice');
        });

        Schema::create('notification_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('recipient_email');
            $table->string('channel', 40)->default('email');
            $table->string('event_key', 120);
            $table->string('status', 30)->default('pending');
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->text('last_error')->nullable();
            $table->string('notifiable_type', 120)->nullable();
            $table->unsignedBigInteger('notifiable_id')->nullable();
            $table->json('payload')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
            $table->index(['event_key', 'status']);
            $table->index(['notifiable_type', 'notifiable_id']);
        });

        Schema::create('patient_profiles', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->text('allergies')->nullable();
            $table->text('medical_notes')->nullable();
            $table->timestamps();
        });

        Schema::create('doctor_reviews', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('doctor_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('rating');
            $table->text('comment')->nullable();
            $table->timestamps();
            $table->unique(['doctor_id', 'user_id']);
        });

        Schema::create('article_comments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('article_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('rating')->nullable();
            $table->text('comment');
            $table->timestamps();
        });

        Schema::create('qna_questions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('question');
            $table->string('status', 20)->default('open');
            $table->timestamps();
            $table->index('status');
        });

        Schema::create('qna_answers', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('qna_question_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('answer');
            $table->boolean('is_official')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qna_answers');
        Schema::dropIfExists('qna_questions');
        Schema::dropIfExists('article_comments');
        Schema::dropIfExists('doctor_reviews');
        Schema::dropIfExists('patient_profiles');
        Schema::dropIfExists('notification_logs');

        Schema::table('appointments', function (Blueprint $table): void {
            $table->dropColumn('prescription_safety_notes');
        });

        Schema::table('medicine_orders', function (Blueprint $table): void {
            $table->dropIndex(['payment_callback_token']);
            $table->dropColumn([
                'payment_callback_token',
                'payment_reference',
                'inventory_committed_at',
                'inventory_released_at',
            ]);
        });
    }
};
