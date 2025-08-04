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
        Schema::table('tasks', function (Blueprint $table) {
            // Kolom untuk menyimpan path file bukti pengumpulan
            $table->string('submission_file_path')->nullable()->after('status');
            // Kolom untuk menyimpan waktu pengumpulan
            $table->timestamp('submitted_at')->nullable()->after('submission_file_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn(['submission_file_path', 'submitted_at']);
        });
    }
};
