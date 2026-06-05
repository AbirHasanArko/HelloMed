<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('medicine_orders', function (Blueprint $table): void {
            $table->string('prescription_path')->nullable()->after('notes');
            $table->boolean('contains_prescription_items')->default(false)->after('prescription_path');
        });
    }

    public function down(): void
    {
        Schema::table('medicine_orders', function (Blueprint $table): void {
            $table->dropColumn(['prescription_path', 'contains_prescription_items']);
        });
    }
};
