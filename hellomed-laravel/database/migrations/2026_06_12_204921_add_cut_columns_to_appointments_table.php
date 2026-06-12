<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            if (!Schema::hasColumn('appointments', 'doctor_cut')) {
                $table->decimal('doctor_cut', 10, 2)->nullable()->after('status');
            }
            if (!Schema::hasColumn('appointments', 'hospital_cut')) {
                $table->decimal('hospital_cut', 10, 2)->nullable()->after('doctor_cut');
            }
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            if (Schema::hasColumn('appointments', 'doctor_cut')) {
                $table->dropColumn('doctor_cut');
            }
            if (Schema::hasColumn('appointments', 'hospital_cut')) {
                $table->dropColumn('hospital_cut');
            }
        });
    }
};
