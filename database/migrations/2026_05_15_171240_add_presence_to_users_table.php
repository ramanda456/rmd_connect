<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Tambah kolom is_online (default: false = offline)
            $table->boolean('is_online')->default(false);
            // Tambah kolom last_seen_at (kapan terakhir online)
            $table->timestamp('last_seen_at')->nullable();
        });
    }

    public function down(): void
    {
        // Ini dijalankan kalau migration di-rollback
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_online', 'last_seen_at']);
        });
    }
};
