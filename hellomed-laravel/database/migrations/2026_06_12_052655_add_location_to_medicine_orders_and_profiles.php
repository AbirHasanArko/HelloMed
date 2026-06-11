<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('medicine_orders', function (Blueprint $table): void {
            $table->decimal('latitude', 10, 8)->nullable()->after('delivery_address');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
        });

        Schema::table('patient_profiles', function (Blueprint $table): void {
            $table->text('address')->nullable()->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medicine_orders', function (Blueprint $table): void {
            $table->dropColumn(['latitude', 'longitude']);
        });

        Schema::table('patient_profiles', function (Blueprint $table): void {
            $table->dropColumn('address');
        });
    }
};
