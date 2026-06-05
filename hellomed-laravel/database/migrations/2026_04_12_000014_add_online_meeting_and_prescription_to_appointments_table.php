<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table): void {
            $table->string('online_meeting_link')->nullable()->after('payment_status');
            $table->text('doctor_prescription')->nullable()->after('notes');
            $table->timestamp('prescription_written_at')->nullable()->after('doctor_prescription');
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table): void {
            $table->dropColumn([
                'online_meeting_link',
                'doctor_prescription',
                'prescription_written_at',
            ]);
        });
    }
};
