<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table): void {
            $table->string('publication_status', 30)->default('draft')->after('is_published');
            $table->foreignId('reviewed_by_user_id')->nullable()->after('publication_status')->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable()->after('reviewed_by_user_id');
        });

        DB::table('articles')->where('is_published', true)->update([
            'publication_status' => 'published',
            'reviewed_at' => now(),
        ]);

        DB::table('articles')->where('is_published', false)->whereNull('published_at')->update([
            'publication_status' => 'draft',
        ]);
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('reviewed_by_user_id');
            $table->dropColumn(['publication_status', 'reviewed_at']);
        });
    }
};
