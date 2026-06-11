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
        Schema::table('payments', function (Blueprint $table): void {
            $table->string('transaction_id')->nullable()->after('status');
            $table->string('sender_number')->nullable()->after('transaction_id');
        });

        Schema::table('medicine_orders', function (Blueprint $table): void {
            $table->string('transaction_id')->nullable()->after('payment_status');
            $table->string('sender_number')->nullable()->after('transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table): void {
            $table->dropColumn(['transaction_id', 'sender_number']);
        });

        Schema::table('medicine_orders', function (Blueprint $table): void {
            $table->dropColumn(['transaction_id', 'sender_number']);
        });
    }
};
