<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_payouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('month', 7); // Format: YYYY-MM
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['pending', 'paid', 'confirmed'])->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamps();
            
            $table->unique(['user_id', 'month']); // One payout per user per month
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_payouts');
    }
};
