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
        Schema::table('departments', function (Blueprint $table) {
            if (!Schema::hasColumn('departments', 'is_featured')) {
                $table->boolean('is_featured')->default(false);
            }
            if (!Schema::hasColumn('departments', 'featured_order')) {
                $table->integer('featured_order')->default(0);
            }
        });

        Schema::table('doctors', function (Blueprint $table) {
            if (!Schema::hasColumn('doctors', 'featured_order')) {
                $table->integer('featured_order')->default(0);
            }
        });

        Schema::table('articles', function (Blueprint $table) {
            if (!Schema::hasColumn('articles', 'featured_order')) {
                $table->integer('featured_order')->default(0);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->dropColumn(['is_featured', 'featured_order']);
        });

        Schema::table('doctors', function (Blueprint $table) {
            $table->dropColumn(['featured_order']);
        });

        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn(['featured_order']);
        });
    }
};
