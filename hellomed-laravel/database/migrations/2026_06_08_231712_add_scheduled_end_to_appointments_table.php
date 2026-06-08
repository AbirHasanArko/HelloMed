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
        Schema::table('appointments', function (Blueprint $table) {
            $table->dateTime('scheduled_end')->after('scheduled_for')->nullable();
        });

        // Set scheduled_end for existing appointments based on doctor slot_minutes
        \Illuminate\Support\Facades\DB::table('appointments')
            ->join('doctors', 'appointments.doctor_id', '=', 'doctors.id')
            ->update([
                'scheduled_end' => \Illuminate\Support\Facades\DB::raw('DATE_ADD(appointments.scheduled_for, INTERVAL COALESCE(doctors.slot_minutes, 30) MINUTE)')
            ]);

        // Make it non-nullable after populating
        Schema::table('appointments', function (Blueprint $table) {
            $table->dateTime('scheduled_end')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn('scheduled_end');
        });
    }
};
