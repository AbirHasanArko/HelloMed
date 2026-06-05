<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('medicines', function (Blueprint $table): void {
            $table->string('medicine_group')->nullable()->after('name');
            $table->string('power')->nullable()->after('description');
            $table->string('amount')->nullable()->after('power');
        });

        DB::table('medicines')
            ->whereNull('power')
            ->whereNotNull('strength')
            ->update(['power' => DB::raw('strength')]);
    }

    public function down(): void
    {
        Schema::table('medicines', function (Blueprint $table): void {
            $table->dropColumn(['medicine_group', 'power', 'amount']);
        });
    }
};
