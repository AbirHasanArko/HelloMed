<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hospital_fund_transactions', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['appointment_cut', 'test_fee', 'medicine_profit']);
            $table->unsignedBigInteger('reference_id')->nullable()->comment('ID of the appointment, lab test, or medicine order');
            $table->decimal('amount', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hospital_fund_transactions');
    }
};
