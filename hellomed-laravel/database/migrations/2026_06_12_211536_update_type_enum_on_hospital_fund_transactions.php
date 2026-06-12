<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Change enum using raw DB query since Doctrine DBAL can have issues with enum changes
        DB::statement("ALTER TABLE hospital_fund_transactions MODIFY COLUMN type ENUM('appointment_cut', 'test_fee', 'medicine_profit', 'medicine_sale', 'medicine_expense') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE hospital_fund_transactions MODIFY COLUMN type ENUM('appointment_cut', 'test_fee', 'medicine_profit') NOT NULL");
    }
};
