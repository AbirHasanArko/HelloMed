<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table): void {
            $table->text('prescription_diagnosis')->nullable()->after('doctor_prescription');
            $table->text('prescription_medicines')->nullable()->after('prescription_diagnosis');
            $table->text('prescription_advice')->nullable()->after('prescription_medicines');
            $table->date('prescription_follow_up_date')->nullable()->after('prescription_advice');
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table): void {
            $table->dropColumn([
                'prescription_diagnosis',
                'prescription_medicines',
                'prescription_advice',
                'prescription_follow_up_date',
            ]);
        });
    }
};
