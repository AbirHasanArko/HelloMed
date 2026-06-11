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
            $table->foreignId('user_id')->nullable()->change();
            $table->string('customer_name')->nullable()->after('user_id');
            $table->string('customer_email')->nullable()->after('customer_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medicine_orders', function (Blueprint $table): void {
            $table->dropColumn(['customer_name', 'customer_email']);
            $table->foreignId('user_id')->nullable(false)->change();
        });
    }
};
