<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('doctors', function (Blueprint $table): void {
            $table->json('online_available_days')->nullable()->after('available_days');
            $table->time('online_available_from')->nullable()->after('online_available_days');
            $table->time('online_available_to')->nullable()->after('online_available_from');
            $table->json('offline_available_days')->nullable()->after('online_available_to');
            $table->time('offline_available_from')->nullable()->after('offline_available_days');
            $table->time('offline_available_to')->nullable()->after('offline_available_from');
        });
    }

    public function down(): void
    {
        Schema::table('doctors', function (Blueprint $table): void {
            $table->dropColumn([
                'online_available_days',
                'online_available_from',
                'online_available_to',
                'offline_available_days',
                'offline_available_from',
                'offline_available_to',
            ]);
        });
    }
};
