<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('departments', function (Blueprint $table): void {
            $table->string('image_path')->nullable()->after('description');
        });

        Schema::create('appointment_prescription_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('appointment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('medicine_id')->nullable()->constrained()->nullOnDelete();
            $table->string('medicine_name');
            $table->string('amount')->nullable();
            $table->string('dosage')->nullable();
            $table->string('intake_time')->nullable();
            $table->string('instructions')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointment_prescription_items');

        Schema::table('departments', function (Blueprint $table): void {
            $table->dropColumn('image_path');
        });
    }
};
