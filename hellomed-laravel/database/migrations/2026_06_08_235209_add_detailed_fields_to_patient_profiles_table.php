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
        Schema::table('patient_profiles', function (Blueprint $table) {
            $table->date('date_of_birth')->nullable()->after('medical_notes');
            $table->string('gender')->nullable()->after('date_of_birth');
            $table->string('height')->nullable()->after('gender');
            $table->string('weight')->nullable()->after('height');
            $table->text('known_conditions')->nullable()->after('weight');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patient_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'date_of_birth',
                'gender',
                'height',
                'weight',
                'known_conditions',
            ]);
        });
    }
};
