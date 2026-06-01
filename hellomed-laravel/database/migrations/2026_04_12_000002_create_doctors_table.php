<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doctors', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('department_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('specialty');
            $table->text('bio')->nullable();
            $table->string('qualification')->nullable();
            $table->unsignedSmallInteger('experience_years')->default(0);
            $table->decimal('consultation_fee', 10, 2)->default(0);
            $table->decimal('online_fee', 10, 2)->nullable();
            $table->decimal('offline_fee', 10, 2)->nullable();
            $table->boolean('online_available')->default(true);
            $table->boolean('offline_available')->default(true);
            $table->string('clinic_address')->nullable();
            $table->string('photo_path')->nullable();
            $table->json('available_days')->nullable();
            $table->time('available_from')->nullable();
            $table->time('available_to')->nullable();
            $table->unsignedSmallInteger('slot_minutes')->default(30);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};
